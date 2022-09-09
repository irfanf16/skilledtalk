<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MyFellowResource;
use App\Models\Friend;
use Illuminate\Http\Request;

class FellowController extends Controller
{
    public function myFellows(){
        $myFellows = Friend::with('sender', 'receiver')
        ->whereHas('receiver')
        ->whereHas('sender')
        ->where(['request_from' => auth()->id()])
        ->orWhere(['request_to' => auth()->id()])
        ->paginate(10);
        $myFellows = MyFellowResource::collection($myFellows);

        return response()->success(1, 'list of my fellows', $myFellows);
    }
}
