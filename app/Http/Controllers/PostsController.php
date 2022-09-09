<?php

namespace App\Http\Controllers;

use App\Events\GeneralEvent;
use App\Models\Country;
use App\Models\Group;
use App\Models\Page;
use App\Models\PostJobs;
use App\Models\Posts;
use App\Models\Reflection;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\UnitOfWork\iUnitOfWork;
use App\Repositories\SingleModel\iSingleModelRepository;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    use NotificationTrait;

    private $unitOfWork;
    private $singleModel;

    public function __construct(iUnitOfWork $unitOfWork, iSingleModelRepository $singleModel)
    {
        $this->singleModel = $singleModel;
        $this->unitOfWork = $unitOfWork;
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'privacy' => 'required',
            'post_type' => 'required|string'
        ]);
        DB::beginTransaction();
        try{
        $postType = $this->singleModel->postType->where('name', $request->input('post_type'))->first();
        $request['post_type_id'] = $postType->id;

        $postPrivacy = $this->singleModel->postPrivacy->where('name', $request->input('privacy'))->first();
        $request['post_privacy_id'] = $postPrivacy->id;

        $post = auth()->user()->posts()->create($request->all());

        if ($postType->name != 'Job') {

            if ($request->hasFile('file')) {
                $fileArray = [];
                foreach ($request->file('file') as $file) {

                    $filNameWithExtention = $file->getClientOriginalName();
                    $fileName = pathinfo($filNameWithExtention, PATHINFO_FILENAME);
                    $extention = $file->getClientOriginalExtension();
                    $image = trim(str_replace(' ', '', $fileName . '_' . time() . '.' . $extention));
                    str_replace(' ', '', $image);
                    $path = $file->storeAs('media', $image, 's3');

                    Storage::disk('s3')->setVisibility($path, 'public');

                    $fileArray[] = [
                        'name' => $image
                    ];
                }
                $post->postMedia()->createMany($fileArray);
            }

        } else {
            if ($request->has('file')) {

                $filNameWithExtention = $request->file('file')->getClientOriginalName();
                $fileName = pathinfo($filNameWithExtention, PATHINFO_FILENAME);
                $extention = $request->file('file')->getClientOriginalExtension();
                $image = trim(str_replace(' ', '', $fileName . '_' . time() . '.' . $extention));
                str_replace(' ', '', $image);
                $path = $request->file('file')->storeAs('media', $image, 's3');
                Storage::disk('s3')->setVisibility($path, 'public');
                $fileArray = [
                    'name' => $image
                ];

                $post->postMedia()->create($fileArray);

                $request['job_poster'] = $image;

            }
            $post->jobs()->create($request->all());
        }
        DB::commit();
        return redirect()->back()->with(['success' => 'Post created successfully']);
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['error' => 'Unable to create post. something went wrong']);
        }
    }

    public function repost(Request $request)
    {
        if ($request->ajax()) {
            $postType = $this->singleModel->postType->where('name', $request->input('post_type'))->first();

            $request['post_type_id'] = $postType->id;

            $postPrivacy = $this->singleModel->postPrivacy->where('name', $request->input('privacy'))->first();
            $request['post_privacy_id'] = $postPrivacy->id;

            $post = auth()->user()->posts()->create($request->all());

            return $this->unitOfWork->response(['ResponseCode' => 1, 'ResponseMessage' => 'Post shared successfully']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Posts $posts
     * @return \Illuminate\Http\Response
     */
    public function show($post_id, Request $request)
    {
        if ($request->ajax()) {

            $postModal = $this->unitOfWork->post->getModel();

            $post = $postModal->with(['user', 'postMedia', 'reflections' => function ($query) {
                $query->with('user')->limit(2)->latest();
            },
                'rate' => function ($queryRate) {
                    $queryRate->where('user_id', auth()->user()->id);
                }])
                ->withCount('reflections')
                ->withCount('rate')
                ->find($post_id);

                $existingViewCount = $post->view_count;

                $post->update([
                    'view_count'    =>  $existingViewCount + 1
                ]);

            $post->dateForHumans = $post->created_at->diffForHumans();

            $urlPost = env("AWS_S3_BUCKET_URL")."media/";
            $urlProfile = env("PROFILE_URL");

            return $this->unitOfWork->response(['urlPost' => $urlPost, 'urlProfile' => $urlProfile, 'post' => $post]);
        }
    }

    public function postDetail($post_id, Request $request)
    {
        $postModal = $this->unitOfWork->post->getModel();

        $post = $postModal->with(['user', 'postMedia', 'reflections' => function ($query) {
            $query->with('user')->limit(2)->latest();
        },
            'rate' => function ($queryRate) {
                $queryRate->where('user_id', auth()->user()->id);
            }])
            ->withCount('reflections')
            ->withCount('rate')
            ->find($post_id);

        $post->dateForHumans = $post->created_at->diffForHumans();

        $similarPages = Page::inRandomOrder()->limit(5)->get();
        $groups = Group::inRandomOrder()->limit(5)->get();
        $jobs = PostJobs::where('job_title', 'LIKE', '%' . auth()->user()->job_title . '%')->limit(3)->get();
        $urlPost = $this->unitOfWork->url->UrlPost();
        $urlProfile = $this->unitOfWork->url->UrlProfile();
        return view('postDetail')->with(compact('post', 'urlPost', 'urlProfile', 'similarPages', 'groups', 'jobs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Posts $posts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // $request->validate([
        //     'post'  =>  'required|exists:posts,id'
        // ]);

        // dd($request->all());
        $post = Posts::with('postType')->find($request->post);

        if($post->user_id == auth()->id()){

            if($post->postType->name == 'Job'){
                unset($request['_token']);
                unset($request['post']);
                $post->jobs()->update($request->all());
            }else{
                $postType = $this->singleModel->postPrivacy->where('name', 'Public')->first();
                $post->description = $request->description;
                $post->is_edited = 1;
                $post->post_privacy_id = $postType->id;
                if($request->has('heading') && $request->filled('heading')){
                    $post->heading = $request->heading;
                }
                $post->save();
            }

            return redirect()->back()->with(['success' => 'Post Updated successfully.']);


        }else{
            return redirect()->back()->with(['success' => 'Unable to update this post.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Posts $posts
     * @return \Illuminate\Http\Response
     */
    public function destroy($post_id)
    {
        //
    }

    public function reflect(Request $request)
    {
        if ($request->ajax()) {
            $post = $this->unitOfWork->post->getById($request->input('post'));
            if ($post) {
                $request['user_id'] = auth()->user()->id;
                $reflection = $post->reflections()->create($request->all());
                if ($reflection) {
                    $profileUrl = env("PROFILE_URL");
                    return $this->unitOfWork->response(['ResponseCode' => 1, 'data' => $reflection, 'profileUrl' => $profileUrl, 'user' => $reflection->user()->first()]);
                }
            }

            return $this->unitOfWork->response(['ResponseCode' => 0, 'ResponseMessage' => 'Unable to add reflection. Try again later']);
        }
    }

    public function getMoreReflections(Request $request)
    {
        if ($request->ajax()) {

            if ($request->input('count') > 0) {
                $count = $request->input('count') * 10;
            } else {
                $count = 0;
            }
            $profileUrl = $this->unitOfWork->url->UrlProfile();
            $nextCount = $request->input('count') + 1;
            $comments = $this->singleModel->reflections->with('user')->where('post_id', $request->input('post'))->skip($count)->take(10)->orderBy('id', 'DESC')->get();
            return $this->unitOfWork->response(['ResponseCode' => 1, 'profileUrl' => $profileUrl, 'nextCount' => $nextCount, 'data' => $comments]);
        }
    }

    public function rate(Request $request)
    {
        if ($request->ajax()) {
            // return response()->json($request->all());
            $post = $this->unitOfWork->post->getById($request->input('post'));

            if ($post) {
                $request['user_id'] = auth()->user()->id;
                $ifExists = $post->rate()->where('user_id', auth()->user()->id)->first();
                if ($ifExists && !$request->has('updateStars') && !$request->input('updateStars') == true) {
                    $post->rate()->find($ifExists->id)->delete();
                    return $this->unitOfWork->response(['ResponseCode' => 2]);
                } else {

                    $existingViewCount = $post->view_count;

                    $post->update([
                        'view_count' => $existingViewCount + 1
                    ]);

                    $rate = $post->rate()->updateOrCreate(['id' => $request->input('id')], $request->all());

                    $name = auth()->user()->firstname . ' ' . auth()->user()->lastname;

                    $notification = [
                        'user_id' => $post->user_id,
                        'other_user_id' => auth()->id(),
                        'text' => $name . ' rate your post'
                    ];

                    $this->storeNotification($notification);

                    $data = [
                        'action' => 'RATE',
                        'text' => $name . ' rate your post'
                    ];


                    if($post->user_id != auth()->id()){
                        $this->storeNotification($notification);
                        event(new GeneralEvent($data, $post->user_id));
                      }

                    $totalRates = $post->rate()->get()->count();
                }
                return $this->unitOfWork->response(['ResponseCode' => 1, 'data' => $rate, 'totalRates' => $totalRates]);
            }
            return $this->unitOfWork->response(['ResponseCode' => 0, 'ResponseMessage' => 'Unable to rate this post. Try again later']);
        }
    }

    public function savedPostGet()
    {
        $totalReflections = Reflection::with(['post' => function ($query) {
            $query->where('user_id', auth()->id());
        }])->count('id');
        $totalReflections = number_format($totalReflections / 1000, 2) . 'k';

        $totalRating = Rate::with(['post' => function ($query) {
            $query->where('user_id', auth()->id());
        }])->count('stars');
        $totalRating = number_format($totalRating / 1000, 2) . 'k';


        $profileUrl = $this->unitOfWork->url->UrlProfile();
        $employeeTypes = $this->singleModel->employeeTypes->all();
        $experiences = auth()->user()->experience()->get();

//        $post = $this->unitOfWork->post->getModel();
////        $posts = $post->with(
////            [
////                'user', 'postType', 'postMedia', 'jobs.employeeType', 'shared.user', 'shared.postType', 'shared.jobs.employeeType', 'reflections' => function ($query) {
////                $query->with('user')->orderBy('created_at', 'DESC');
////            },
////                'rate' => function ($queryRate) {
////                    $queryRate->where('user_id', auth()->user()->id);
////                }
////            ]
//        )->withCount('reflections')
//
//
//            ->withCount('rate')
//            ->orderBy('created_at', 'DESC')
//            ->paginate(10)
//            ->map(function ($query) {
//                $query->setRelation('reflections', $query->reflections->take(2));
//                return $query;
//            });
        $posts = auth()->user()->savePost()->with(
            [
                'user', 'postType', 'postMedia', 'jobs.employeeType', 'shared.user', 'shared.postType', 'shared.jobs.employeeType', 'reflections' => function ($query) {
                $query->with('user')->orderBy('created_at', 'DESC');
            },
                'rate' => function ($queryRate) {
                    $queryRate->where('user_id', auth()->user()->id);
                }
            ]
        )->withCount('reflections')
            ->withCount('rate')
            ->orderBy('created_at', 'DESC')
            ->paginate(10)
            ->map(function ($query) {
                $query->setRelation('reflections', $query->reflections->take(2));
                return $query;
            });

//        dd($posts);
        $follows = Page::inRandomOrder()->limit(5)->get();
        $follows = DB::table('pages')->WhereIn('id',
            DB::table('page_followers')->where('user_id', auth()->id())->pluck('id'))
            ->get();

        $totalPages = DB::table('pages')->WhereIn('id',
            DB::table('page_followers')->where('user_id', auth()->id())->pluck('id'))
            ->count('id');
        $urlPost = $this->unitOfWork->url->UrlPost();
        $urlProfile = $this->unitOfWork->url->UrlProfile();
        $countries = Country::all();

        return view('savedPost')->with(compact('profileUrl', 'employeeTypes', 'experiences', 'posts', 'urlPost', 'urlProfile', 'follows', 'totalPages', 'countries', 'totalReflections', 'totalRating'));

    }

    public function savePost(Request $request)
    {

        auth()->user()->savePost()->attach($request->post);
        return 1;
    }

    public function delete(Request $request){
        $request->validate([
            'post'  =>  'required|exists:posts,id'
        ]);

        $post  = Posts::find($request->post);
        if($post->user->id == auth()->id()){
            $post->delete();
            return 1;
        }else{
            return 0;
        }
    }

    public function edit(Request $request){
        $request->validate([
            'post'  =>  'required|exists:posts,id'
        ]);

        $post  = Posts::with('postType', 'jobs.employeeType')->find($request->post);

        if($post->user->id == auth()->id()){
            return $post;
        }else{
            return 0;
        }

    }
}
