<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\Post\iPostRepository;
use App\Repositories\URL\iUrlRepository;

class JobController extends Controller
{
    private $post;
    private $url;

    public function __construct(iPostRepository $post, iUrlRepository $url)
    {
        $this->post = $post;
        $this->url = $url;
    }

    public function detail($id){
        $post = $this->post->getFullPost($id);
        return view('job.detail')->with(compact('post'));
    }

    public function apply(Request $request){
        auth()->user()->apply()->attach($request->job_id);
        return 1;
    }

    public function list(){
        $posts = $this->post->jobs();
        $urlPost = $this->url->UrlPost();
        return view('job.jobs')->with(compact('posts', 'urlPost'));
    }
}
