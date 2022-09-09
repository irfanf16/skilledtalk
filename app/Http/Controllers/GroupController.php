<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Posts;
use Illuminate\Http\Request;
use App\Repositories\GroupRepository\iGroupRepository;
use App\Repositories\URL\iUrlRepository;

class GroupController extends Controller
{
    private $group;
    private $url;

    public function __construct(iGroupRepository $group, iUrlRepository $url)
    {
        $this->group = $group;
        $this->url = $url;
    }

    public function list(){
        $groups = $this->group->list();
        $url = env('PAGE_CONTENT_URL');
        return view('group.groups')->with(compact('groups', 'url'));
    }
    public function allGroups(){

        $groups = $this->group->list();
        $url = env('PAGE_CONTENT_URL');
        return view('group.allGroups')->with(compact('groups', 'url'));

    }

    public function create(){
        return view('group.createGroup');
    }

    public function store(Request $request){
        $group = $this->group->store($request);
        if($group){
            return redirect(route('group.list'))->with(['success' => 'Group Created successfully']);
        }
    }

    public function detail($id){

        $group = Group::with(['members' => function($query){
            $query->limit(3);
        }, 'groupUser', 'groupAdmins'])
        ->withCount('members')->findOrFail($id);

        $posts = Posts::with(
            [
                'user', 'postType', 'postMedia', 'jobs.employeeType', 'shared.user', 'shared.postType', 'shared.jobs.employeeType', 'reflections' => function($query){
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
            ->where('group_id', $id)
            ->paginate(10)
            ->map(function ($query) {
            $query->setRelation('reflections', $query->reflections->take(2));
                return $query;
            });

            $url = env('PAGE_CONTENT_URL');
            $urlProfile = env('PROFILE_URL');
            $urlPost = $this->url->UrlPost();
            return view('group.groupDetail')->with(compact('group', 'posts', 'url', 'urlProfile', 'urlPost'));
    }

    public function join(Request $request){
        $group = $this->group->join($request);

        if($group){
            return 1;
        }

    }

    public function leave(Request $request){
        $group = $this->group->leave($request);

        if($group){
            return 1;
        }
    }
}
