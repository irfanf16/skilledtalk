<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Group;
use App\Models\Page;
use App\Models\PostJobs;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Repositories\UnitOfWork\iUnitOfWork;

class HomeController extends Controller
{
    private $unitOfWork;

    public function __construct(iUnitOfWork $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    public function index()
    {
        $post = $this->unitOfWork->post->getModel();
        $posts = $post->with(
            [
                'user', 'postType', 'postMedia', 'jobs.employeeType', 'shared.user', 'shared.postType', 'shared.postMedia', 'shared.jobs.employeeType', 'reflections' => function ($query) {
                $query->with('user')->orderBy('created_at', 'DESC');
            },
                'rate' => function ($queryRate) {
                    $queryRate->where('user_id', auth()->user()->id);
                }
            ]
        )->withCount('reflections')
            ->withCount('rate')
            ->withCount('postShared')
            ->orderBy('created_at', 'DESC')
            ->paginate(10)
            ->map(function ($query) {
                $query->setRelation('reflections', $query->reflections->take(2));
                return $query;
            });

        //  foreach($posts as $post){
        //      if($post->postType->name == 'Shared'){
        //          dd($post);
        //      }
        //  }
        $similarPages = Page::inRandomOrder()->doesntHave('pageUser')->limit(5)->get();

        $groups = Group::inRandomOrder()->limit(5)->get();

        $jobs = $post->with('jobs')
        ->whereHas('postType', function($query){
            $query->where('name', 'Job');
        })
        ->doesntHave('userApplicant')
        ->limit(3)
        ->get();

        $urlPost = env('AWS_S3_BUCKET_URL');
        $urlPost = $urlPost.'media';
        $urlPDF = $urlPost.'pdf';
        $urlProfile = $urlPost;
        return view('home')->with(compact('posts', 'urlPost', 'urlPDF', 'urlProfile', 'similarPages', 'groups', 'jobs'));
    }
    public function discover (Request $request)
    {


        $post = $this->unitOfWork->post->getModel();
        $posts = $post->with(
            [
                'user', 'postType', 'postMedia', 'jobs.employeeType', 'shared.user', 'shared.postType', 'shared.jobs.employeeType', 'reflections' => function ($query) {
                $query->with('user')->orderBy('created_at', 'DESC');
            },
                'rate' => function ($queryRate) {
                    $queryRate->where('user_id', auth()->user()->id);
                }
            ]
        )
            ->when($request->has('filter_type') && $request->filled('filter_type') && $request->filter_type=='Jobs',function ($query){
                $query->where('post_type_id','=',4);
            })
            ->when($request->has('discover') && $request->filled('discover'),function ($query) use ($request){
                $query->where('heading', 'like', '%'.$request->discover.'%')
                    ->orWhere('description', 'like', '%'.$request->discover.'%')
                    ->orWhere('hashtags', 'like', '%'.$request->discover.'%');

            })
            ->when($request->has('hashtags') && $request->filled('hashtags'),function ($query) use ($request){
                $input=$request->hashtags;
                $middle = ceil(strlen($input) / 2);
                $middle_space = strpos($input, " ", $middle - 1);

                if ($middle_space === false) {
                    //there is no space later in the string, so get the last sapce before the middle
                    $first_half = substr($input, 0, $middle);
                    $middle_space = strpos($first_half, " ");
                }

                if ($middle_space === false) {
                    //the whole string is one long word, split the text exactly in the middle
                    $first_half = substr($input, 0, $middle);
                    $second_half = substr($input, $middle);
                }
                else {
                    $first_half = substr($input, 0, $middle_space);
                    $second_half = substr($input, $middle_space);
                }

//                dd($string1,$string2,trim($first_half), trim($second_half));

                $query->where('hashtags', 'like', '%'.trim($first_half).'%');

            })
            ->withCount('reflections')
            ->withCount('rate')
            ->withCount('postShared')
            ->orderBy('created_at', 'DESC')
            ->paginate(10)

            ->map(function ($query) {
                $query->setRelation('reflections', $query->reflections->take(2));
                return $query;
            });

        $urlPost = env('AWS_S3_BUCKET_URL');
        $urlPost = $urlPost.'media';
        $urlPDF = $urlPost.'pdf';
        $urlProfile = $urlPost;
        return view('discover')->with(compact('posts', 'urlPost', 'urlPDF', 'urlProfile'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->with('otherUser')->latest()->get();
        $url = env('PROFILE_URL');
        return [
            'notifications' => $notifications,
            'url' => $url
        ];
    }


}
