<?php

namespace App\Repositories\GroupRepository;

use App\Models\Group;
use App\Repositories\GroupRepository\iGroupRepository;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;

class GroupRepository implements iGroupRepository {

    use FileUploadTrait;

    public function list(){
        return Group::whereHas('members', function($query){
            $query->where('users.id', auth()->user()->id);
        })->withCount('members')->get();
    }

    public function store(Request $request)
    {

       $image =  $this->uploadSingleImage($request, 'log');
       $request['profile_pic'] = $image;
       $image =  $this->uploadSingleImage($request, 'ban');
        $request['banner'] = $image;
       $group =  Group::create($request->all());
       $group->members()->attach(auth()->user()->id, ['is_admin' => 1]);
       return $group;
    }

    public function join(Request $request)
    {
        $group = Group::findOrFail($request->group_id);

        $group->members()->attach(auth()->user()->id);
        return 1;
    }

    public function leave(Request $request)
    {
        $group = Group::findOrFail($request->group_id);

        $group->members()->detach(auth()->user()->id);
        return 1;
    }
    public function allGroups(){

        return Group::withCount('members')->get();

    }
}
