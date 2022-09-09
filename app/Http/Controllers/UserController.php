<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\Reflection;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\UnitOfWork\iUnitOfWork;
use App\Repositories\SingleModel\iSingleModelRepository;
use App\Traits\CheckFriendTrait;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{

    use CheckFriendTrait;

    public function __construct(iUnitOfWork $unitOfWork, iSingleModelRepository $singleModel)
    {
        $this->unitOfWork = $unitOfWork;
        $this->singleModel = $singleModel;
    }
    public function show($id)
    {

        $user = User::findOrFail($id);


        $totalReflections = Reflection::with(['post' => function ($query) use($user){
            $query->where('user_id', $user->id);
        }])->count('id');

        if ($totalReflections > 999) {
            $totalReflections = number_format($totalReflections / 1000, 2) . 'k';
        }

        $totalRating = Rate::with(['post' => function ($query) use ($user){
            $query->where('user_id', $user->id);
        }])->count('stars');

        if ($totalRating > 999) {
            $totalRating = number_format($totalRating / 1000, 2) . 'k';
        }

        $contentView =  $user->posts()->sum('view_count');

        if ($contentView > 999) {
            $contentView = number_format($contentView / 1000, 2) . 'k';
        }

        $engagement = $user->consultation()->count();


        $profileUrl = env("PROFILE_URL");
        $employeeTypes = $this->singleModel->employeeTypes->all();
        $experiences = $user->experience()->get();

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
        )->withCount('reflections')
            ->withCount('rate')
            ->withCount('postShared')
            ->orderBy('created_at', 'DESC')
            ->where('user_id', $id)
            ->get()
            ->map(function ($query) {
                $query->setRelation('reflections', $query->reflections->take(2));
                return $query;
            });

        $urlPost = $this->unitOfWork->url->UrlPost();
        $urlProfile = $profileUrl;

        $follows = DB::table('pages')->WhereIn('id',
            DB::table('page_followers')->where('user_id', $user->id)->pluck('id'))
            ->get();
        $totalPages = DB::table('pages')->WhereIn('id',
            DB::table('page_followers')->where('user_id', auth()->id())->pluck('id'))
            ->count('id');

        $is_friend = $this->is_friend(auth()->id(), $id);

        return view('visitUserProfile')->with(compact('profileUrl', 'totalPages','follows','employeeTypes', 'experiences', 'posts', 'urlPost', 'urlProfile', 'user', 'is_friend', 'totalReflections','totalRating','engagement', 'contentView'));
    }

    public function getuser(Request $request)
    {
        $user = User::findOrFail($request->id);
        return $user;
    }

    public function addSkill(Request $request)
    {
        auth()->user()->skills()->create([
            'name'  =>  $request->skill
        ]);
        return 1;
    }
}
