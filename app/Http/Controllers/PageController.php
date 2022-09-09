<?php

namespace App\Http\Controllers;

use App\Models\CompanyInformation;
use App\Models\Page;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use App\Repositories\UnitOfWork\iUnitOfWork;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    use FileUploadTrait;


    private $unitOfWork;

    public function __construct(iUnitOfWork $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('page.pageType');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $logo = $this->uploadSingleImage($request, 'log');
        $banner = $this->uploadSingleImage($request, 'ban');

        $request['logo'] = $logo;
        $request['banner'] = $banner;

        $page = auth()->user()->pages()->create($request->all());
        CompanyInformation::updateOrCreate(['type'=>'industry','information'=>$request->industry],[
            'type'=>'industry',
            'information'=>$request->industry
        ]);
        CompanyInformation::updateOrCreate(['type'=>'company_size','information'=>$request->company_size],[
            'type'=>'company_size',
            'information'=>$request->company_size
        ]);
        CompanyInformation::updateOrCreate(['type'=>'company_type','information'=>$request->company_type],[
            'type'=>'company_type',
            'information'=>$request->company_type
        ]);

        return redirect(route('page.show', $page->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


        $post = $this->unitOfWork->post->getModel();
        $posts = $post->with(
            [
                'user', 'postType', 'postMedia', 'jobs.employeeType', 'jobs', 'shared.user', 'shared.postType', 'shared.jobs.employeeType', 'reflections' => function($query){
                    $query->with('user')->orderBy('created_at', 'DESC');
                    },
                'rate' => function($queryRate){
                    $queryRate->where('user_id', auth()->user()->id);
                }
            ]
            )->withCount('reflections')
            ->withCount('rate')
            ->withCount('postShared')
            ->orderBy('created_at', 'DESC')
            ->where('page_id', $id)
            ->paginate(10)
            ->map(function ($query) {
            $query->setRelation('reflections', $query->reflections->take(2));
                return $query;
            });

            $url = env('PAGE_CONTENT_URL');
            $urlProfile = env('PROFILE_URL');
            $page = Page::findOrFail($id);
            $page_followers=DB::table('page_followers')->where('page_id',$page->id)->count();

            $urlPost = $this->unitOfWork->url->UrlPost();

            return view('page.pageDetail')->with(compact('page','page_followers', 'url', 'urlProfile', 'posts', 'urlPost'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function pageSetup($id){
       $industry =CompanyInformation::where('type','industry')->get();
       $company_size =CompanyInformation::where('type','company_size')->get();
       $company_type =CompanyInformation::where('type','company_type')->get();
       return view('page.pageSetup')->with(compact('id','industry','company_size','company_type'));
    }
    public function pageFollow($id){

       $page =Page::find($id);
         $user=User::find(auth()->id());
        $user->pagesfollow()->attach($page);
       return 1;
    }
    public function pageDetail($id){

    }

    public function pagesList(){

        $pagesList=Page::paginate(12);


//        dd($pagesList);
        return view('page.pagesList',compact('pagesList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
