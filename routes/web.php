<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('login'));
});


//Protected Routes
Route::group([
    'middleware' => ['auth', 'verified']
], function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/discover', [App\Http\Controllers\HomeController::class, 'discover'])->name('discover');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/experience', [App\Http\Controllers\ProfileController::class, 'experience'])->name('experience');
    Route::get('/experience/edit/{id}', [App\Http\Controllers\ProfileController::class, 'experienceEdit'])->name('experience.edit');
    Route::post('/experience/update', [App\Http\Controllers\ProfileController::class, 'experienceUpdate'])->name('experience.update');

    Route::get('/inbox/{id?}', [App\Http\Controllers\ChatController::class, 'inbox'])->name('inbox');
    Route::post('/getConversationMessages', [App\Http\Controllers\ChatController::class, 'getConversationMessages'])->name('getConversationMessages');
    Route::post('/sendMessage', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('sendMessage');
    Route::post('/setMeeting', [App\Http\Controllers\ChatController::class, 'setMeeting'])->name('setMeeting');
    Route::post('/AcceptRejectMeeting', [App\Http\Controllers\ChatController::class, 'AcceptRejectMeeting'])->name('AcceptRejectMeeting');
    Route::get('user/calling/{meeting_id}', [ChatController::class, 'call'])->name('user.calling');

    Route::post('/sendRequest', [App\Http\Controllers\FriendController::class, 'sendRequest'])->name('friend.request.send');
    Route::post('/friend/requests', [App\Http\Controllers\FriendController::class, 'friendRequestList'])->name('friend.request.list');
    Route::post('/friend/requests/action', [App\Http\Controllers\FriendController::class, 'friendRequestAction'])->name('friend.request.action');
    Route::post('/friend/unfriend', [App\Http\Controllers\FriendController::class, 'unfriend'])->name('friend.unfriend');
    Route::get('/friend/list', [App\Http\Controllers\FriendController::class, 'friendList'])->name('friend.list');
    Route::get('/friend/list/ajax', [App\Http\Controllers\FriendController::class, 'getFriendListAjax'])->name('friend.list');

    Route::get('/notifications/list', [App\Http\Controllers\HomeController::class, 'notifications'])->name('notifications.list');

    Route::get('engagements/list', [App\Http\Controllers\EngagementController::class, 'list'])->name('engagements.list');
    Route::get('engagements/verify/{id}', [App\Http\Controllers\EngagementController::class, 'verify'])->name('engagements.verify');


    Route::group([
        'prefix' => 'wallet'
    ], function () {
        Route::get('/', [App\Http\Controllers\WalletController::class, 'wallet'])->name('wallet');
        Route::get('withdrawal', [App\Http\Controllers\WalletController::class, 'withdrawal'])->name('withdrawal');
        Route::post('addEbank', [App\Http\Controllers\WalletController::class, 'addEbank'])->name('addEbank');
        Route::post('withdrawalRequest', [App\Http\Controllers\WalletController::class, 'withdrawalRequest'])->name('withdrawalRequest');


    });

    Route::group([
        'prefix' => 'post'
    ], function () {
        Route::post('/edit',[\App\Http\Controllers\PostsController::class,'edit'])->name('post.edit');
        Route::post('/update',[\App\Http\Controllers\PostsController::class,'update'])->name('post.update');
        Route::post('/store', [App\Http\Controllers\PostsController::class, 'store'])->name('post.store');
        Route::post('/repost', [App\Http\Controllers\PostsController::class, 'repost'])->name('post.repost');
        Route::post('/rate', [App\Http\Controllers\PostsController::class, 'rate'])->name('post.rate');
        Route::post('/reflect', [App\Http\Controllers\PostsController::class, 'reflect'])->name('post.reflect');
        Route::post('/reflect/more', [App\Http\Controllers\PostsController::class, 'getMoreReflections'])->name('post.reflections.more');
        Route::get('/show/{id}', [App\Http\Controllers\PostsController::class, 'show'])->name('post.show');
        Route::get('/detail/{id}', [App\Http\Controllers\PostsController::class, 'postDetail'])->name('post.detail');

        Route::get('/saved',[\App\Http\Controllers\PostsController::class,'savedPostGet'])->name('post.saved');
        Route::post('/save',[\App\Http\Controllers\PostsController::class,'savePost'])->name('post.save');
        Route::post('/delete',[\App\Http\Controllers\PostsController::class,'delete'])->name('post.delete');


    });
    Route::group([
        'prefix' => 'group'
    ], function () {
        Route::get('list', [App\Http\Controllers\GroupController::class, 'list'])->name('group.list');
        Route::get('all', [App\Http\Controllers\GroupController::class, 'allGroups'])->name('group.all');
        Route::get('create', [App\Http\Controllers\GroupController::class, 'create'])->name('group.create');
        Route::post('store', [App\Http\Controllers\GroupController::class, 'store'])->name('group.store');
        Route::get('detail/{id}', [App\Http\Controllers\GroupController::class, 'detail'])->name('group.detail');

        //for ajax
        Route::post('join', [App\Http\Controllers\GroupController::class, 'join'])->name('group.join');
        Route::post('leave', [App\Http\Controllers\GroupController::class, 'leave'])->name('group.leave');
    });
    Route::group([
        'prefix' => 'job'
    ], function () {

        Route::get('list', [App\Http\Controllers\JobController::class, 'list'])->name('job.list');
        Route::get('detail/{id}', [App\Http\Controllers\JobController::class, 'detail'])->name('job.detail');

        //for ajax
        Route::post('apply', [App\Http\Controllers\JobController::class, 'apply'])->name('job.apply');
    });
    Route::resource('page', PageController::class);
    Route::get('page/setup/{id}', [App\Http\Controllers\PageController::class, 'pageSetup'])->name('page.setup');
    Route::get('page/follow/{id}', [App\Http\Controllers\PageController::class, 'pageFollow'])->name('page.follow');
    Route::get('page/detail/{id}', [App\Http\Controllers\PageController::class, 'show'])->name('page.detail');
    Route::get('pages/list', [App\Http\Controllers\PageController::class, 'pagesList'])->name('pages.list');

    Route::group([
        'prefix' => 'user'
    ], function () {
        Route::get('/profile/{id}', [App\Http\Controllers\UserController::class, 'show'])->name('user.profile.show');
        Route::post('get', [App\Http\Controllers\UserController::class, 'getuser'])->name('user.get');
        Route::post('addSkill', [App\Http\Controllers\UserController::class, 'addSkill'])->name('user.skill.add');

    });

    Route::post('/search', [App\Http\Controllers\SearchController::class, 'search'])->name('search');
    Route::post('/search/skills', [App\Http\Controllers\SearchController::class, 'skills'])->name('search');
    Route::post('subscription',[\App\Http\Controllers\SubscriptionController::class,'getSubscription'])->name('subscription');
    Route::get('unsubscribe',[\App\Http\Controllers\SubscriptionController::class,'unsubscribe'])->name('unsubscribe');

});


Route::get('/migrate', function(){
    Artisan::call('migrate');
 });


 Route::get('/rollback', function(){
    Artisan::call('migrate:rollback');
 });

 Route::get('/symlink', function(){
    Artisan::call('storage:link');
 });
 Route::get('/privacy-policy', function(){
    return view('privacy.privacyPolicy');
 })->name('policy');
 Route::get('/cookie-policy', function(){
    return view('privacy.cookiePolicy');
 })->name('cookie.policy');
 Route::get('/disclaimer', function(){
    return view('privacy.disclaimer');
 })->name('disclaimer');
 Route::get('/terms-of-use', function(){
    return view('privacy.terms');
 })->name('terms');
 Route::get('/about', function(){
    return view('privacy.about');
 })->name('about');


//resolve route by slug
// Route::get('/posts/{post:slug}', function (Post $post) {
//     return $post;
// });


// Route::get('/users/{user}/posts/{post:slug}', function (User $user, Post $post) {
//     return $post;
// });
require __DIR__.'/auth.php';
