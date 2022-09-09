@extends('custom.layouts.app')
@section('content')
@include('custom.inc.loading')
@include('custom.inc.header')


    <!-- MAIN CONTAINER -->
    <div class="container company-container">
        <!-- PROFILE -->
        <section id="profile">
            <div class="main-card company-page">
                <div class="company-profile">
                    <img src="{{ $url }}/{{ $page->banner }}" alt="" style="height:191px; width:1128px; object-fit:cover;"/>
                    <div class="company-description">
                        <div class="logo-company">
                            <img src="{{ $url }}/{{ $page->logo }}" style="height:112px; width:112px; object-fit:cover;" alt="">
                            {{-- <img src="{{ $url }}/{{ $page->logo }}" height="112" width="112" alt=""> --}}
                            <div class="name-company">
                                <h4>{{ $page->name }}</h4>
                                <p>{{ $page->company_type }} | {{$page_followers}} followers</p>
                            </div>
                        </div>
                        <p>{{ $page->tagline }}</p>
                        @if($page->website != null)<a target="_blank" href="https://{{$page->website}}" class="btn__primary--large">   Visit Website <i class="fas fa-external-link-alt"></i></a>@endif
                        {{-- <input type="button" class="btn__primary--large" style="float: right;" value="Follow"> --}}
                    </div>
                </div>
            </div>
        </section>
        <!-- LEFT ASIDE -->
        <div class="left-aside-wrapper" id="left-aside-wrapper">
            <aside class="left-aside" id="left-aside">
                <div class="profile-groups" id="profile-groups">
                   <div class="company-links">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#home" role="tab" aria-controls="home">Home</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#about" role="tab" aria-controls="about">About</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#jobs" role="tab" aria-controls="jobs">Jobs</a>
                          </li>
{{--                          <li class="nav-item">--}}
{{--                            <a class="nav-link" data-toggle="tab" href="#people" role="tab" aria-controls="people">People</a>--}}
{{--                          </li>--}}
                        </ul>
                   </div>
                </div>
            </aside>
        </div>
        <!-- MAIN -->
        <div id="main-wrapper">
            <main class="main-section" id="main-section">
                @if(auth()->user()->id == $page->user_id)
                    @include('custom.inc.postsCard')

                    @include('custom.inc.alerts')
                @endif

                <div class="tab-content">
                  <div class="tab-pane active" id="home" role="tabpanel">
                      {{-- <div class="little-overview round-shadow">
                        <h4>About</h4>
                        <p>As the world’s largest food and beverage company we are driven by a simple aim: enhancing quality of life and contributing to a healthier future. To deliver on this, we serve with passion, with a spirit of excellence, offering products and services for all stages of life, every moment of the day, helping people care for themselves and their families. Our culture is based on our values rooted in respect: respect for ourselves, respect for others, respect for diversity and respect for the future.</p>
                        <a href="" class="btn-primary">See all</a>
                      </div> --}}
                      <div class="company-articles">

                            @foreach($posts as $post)

                            <article data-post="{{ $post->id }}" class="post-article">
                                <!-- post header -->
                                <div class="post-author">
                                    <a href="profile.html">
                                        <div class="author-details">
                                            <figure class="image-container">
                                                @if($post->user->profile_pic != null)
                                                    <img class="img-size" src="{{ $urlProfile }}/{{ $post->user->profile_pic }}" alt="">
                                                @else
                                                    <img class="img-size" src="{{ asset('assets/img/profile.png') }}" alt="">
                                                @endif
                                            </figure>
                                            <div class="author-description">
                                                <div class="post-name-experience">
                                                    <strong class="post-author-name">{{ $post->user->firstname }} {{ $post->user->lastname }}</strong>
                                                    <span>
                                                        <span>&nbsp;·&nbsp;</span>
                                                        3Y Exp</span>
                                                    <span class="green_slot">J</span>
                                                </div>
                                                <span class="designation">
                                                    @if($post->user->is_student == 1) Student |@endif
                                                    {{ $post->user->current_position }}
                                                </span>
                                                <span class="time-ago">{{ $post->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="consult-btn">
                                        @if($post->user->id != auth()->id() && $post->user->session_price !=null)
                                            @if(auth()->user()->getSubscription() != null)
                                                <button class="open_consultation" data-user="{{ $post->user->id }}"><img src="{{asset('assets/img/camera.png')}}" alt="">Consult</button>
                                            @else
                                                <button type="button" data-toggle="modal" data-target="#subscription" ><img src="{{asset('assets/img/camera.png')}}" alt="">Consult</button>
                                            @endif
                                        @endif
                                    </div>
                                        <div class="vertical-icons">
                                            <span class="fas fa-circle"></span>
                                            <span class="fas fa-circle"></span>
                                            <span class="fas fa-circle"></span>
                                            <div class="notifications-signs">
                                                <ul>
                                                    <li><a href="save-post.html"><i class="fas fa-save" aria-hidden="true"></i> Save <span class="dropdown-edit">Save for later</span></a></li>
                                                    <li><a href=""><i class="fa fa-copy" aria-hidden="true"></i> Copy link to post</a></li>
                                                    <li><a href=""><i class="fas fa-volume-mute" aria-hidden="true"></i> Mute Usman Rahim <span class="dropdown-edit">Stop seeing post for Usman</span></a></li>
                                                    <li><a href=""><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Leave this group<span class="dropdown-edit">Stop seeing post for this group</span></a></li>
                                                    <li><a href="" data-toggle="modal" data-target="#report-post"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Report this post<span class="dropdown-edit">This post is offensive or account is hacked</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                </div>
                                <!-- /post header -->

                                <!-- post body -->
                                @if($post->postType->name == 'Article')
                                <div class="post-data">
                                    <h4 class="article-heading">{{ $post->heading }}</h4>
                                    <p>
                                        {{ $post->description }}
                                    </p>
                                    <p class="post-translation">
                                        <a href="{{route('discover')}}?hashtags={{preg_replace("/#/i", "", $post->hashtags)}}"><button>{{$post->hashtags}}</button></a>

                                    </p>

                                    @if(!$post->postMedia->isEmpty())
                                        {{-- <iframe src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" width="100%" height="500px">
                                        </iframe> --}}

                                        {{-- <embed src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" type="application/pdf" width="100%" height="500px" /> --}}
                                        <iframe src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" width="100%" height="500px"></iframe>
                                    @endif

                                </div>
                                @elseif($post->postType->name == 'Photo' || $post->postType->name == 'Video')
                                <div class="post-data">
                                    <p>
                                        {{ $post->description }}
                                    </p>
                                    <p class="post-translation">
                                        <a href="{{route('discover')}}?hashtags={{preg_replace("/#/i", "", $post->hashtags)}}"><button>{{$post->hashtags}}</button></a>

                                    </p>
                                    @php
                                        $mediaCount = count($post->postMedia);
                                    @endphp
                                    @if($mediaCount == 1)
                                        @if($post->postType->name == 'Photo')
                                        <img src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" class="postMediaContent" alt="Modal Image" />
                                        @elseif($post->postType->name == 'Video')
                                        <video style="width: 100%;" controls>
                                            <source src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" type="video/mp4">
                                            <source src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" type="video/mkv">
                                            Your browser does not support HTML video.
                                        </video>
                                        @endif
                                    @else

                                        @if($mediaCount == 2)
                                        <div class="row">
                                            <div class="col-md-6 imgShowColumn1">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" class="postImage postMediaContent" alt="" />
                                            </div>
                                            <div class="col-md-6 imgShowColumn2">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[1]->name }}" class="postImage postMediaContent" alt="" />
                                            </div>
                                        </div>
                                        @elseif($mediaCount == 3)
                                        <div class="row">
                                            <div class="col-md-12 singleImage">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" class="postMediaContent" alt="" />
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 imgShowColumn1">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" class="postImage postMediaContent" alt="" />
                                            </div>
                                            <div class="col-md-6 imgShowColumn2">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[1]->name }}" class="postImage postMediaContent" alt="" />
                                            </div>
                                        </div>
                                        @elseif($mediaCount == 4)
                                        <div class="row">
                                            <div class="col-md-6 imgShowColumn1">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" class="postImage postMediaContent" alt="" />
                                            </div>
                                            <div class="col-md-6 imgShowColumn2">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[1]->name }}" class="postImage postMediaContent" alt="" />
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 imgShowColumn3">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[2]->name }}" class="postImage postMediaContent" alt="" />
                                            </div>
                                            <div class="col-md-6 imgShowColumn4">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[3]->name }}" class="postImage postMediaContent" alt="" />
                                            </div>
                                        </div>
                                        @elseif($mediaCount > 4)
                                        <div class="row">
                                            <div class="col-md-12">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" class="postMediaContent" alt="" />
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                        <div class="col-md-4 imgShowColumn1">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[1]->name }}" class="postMediaContent" alt="" />
                                        </div>
                                        <div class="col-md-4 imgShowColumn2 imgShowColumn3">
                                            <img src="{{ $urlPost }}/{{ $post->postMedia[2]->name }}" class="postMediaContent" alt="" />
                                        </div>
                                        <div class="col-md-4 imgShowColumn3" id="moreImagesbox">
                                        <span id="moreImages">
                                                +{{ $mediaCount - 3 }}
                                            </span>
                                        </div>
                                        </div>

                                        @endif <!-- /count == 2 -->
                                    @endif <!-- /count == 1 -->

                                </div>
                                @elseif($post->postType->name == 'Job')
                                <div class="job-post-data">
                                    <img src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" alt="" />
                                    <div class="job-post-details">
                                        <h2>{{ $post->jobs->job_title }}</h2>
                                        <p>{{ $post->jobs->description }}</p>
                                        <div class="job-post-descrption-detail">
                                            <span>{{ $post->jobs->location }}</span> .
                                            <span>{{ $post->jobs->employeeType->name }}</span> .
                                            <span>{{ $post->jobs->salary_from }}-{{ $post->jobs->salary_to }}k /month</span>
                                            <a class="btn-primary" href="{{ route('job.detail', $post->jobs->id) }}">Apply now</a>
                                        </div>
                                    </div>
                                </div>
                                @elseif($post->postType->name == 'Shared')
                                <div class="job-post-data">
                                    {{-- <img src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" alt="" />
                                    <div class="job-post-details">
                                        <h2>{{ $post->jobs->job_title }}</h2>
                                        <p>{{ $post->jobs->description }}</p>
                                        <div class="job-post-descrption-detail">
                                            <span>{{ $post->jobs->location }}</span> .
                                            <span>{{ $post->jobs->employeeType->name }}</span> .
                                            <span>{{ $post->jobs->salary_from }}-{{ $post->jobs->salary_to }}k /month</span>
                                            <a class="btn-primary" href="jobs-detail.html">Apply now</a>
                                        </div>
                                    </div> --}}

                                        <!-- post header -->
                                        <div class="post-author">
                                            <a href="profile.html">
                                                <div class="author-details">
                                                    <figure class="image-container">
                                                        @if($post->shared->user->profile_pic != null)
                                                            <img class="img-size" src="{{ $urlProfile }}/{{ $post->shared->user->profile_pic }}" alt="">
                                                        @else
                                                            <img class="img-size" src="{{ asset('assets/img/profile.png') }}" alt="">
                                                        @endif
                                                    </figure>
                                                    <div class="author-description">
                                                        <div class="post-name-experience">
                                                            <strong class="post-author-name">{{ $post->shared->user->firstname }} {{ $post->shared->user->lastname }}</strong>
                                                            <span>
                                                                <span>&nbsp;·&nbsp;</span>
                                                                3Y Exp</span>
                                                            <span class="green_slot">J</span>
                                                        </div>
                                                        <span class="designation">
                                                            @if($post->shared->user->is_student == 1) Student |@endif
                                                            {{ $post->shared->user->current_position }}
                                                        </span>
                                                        <span class="time-ago">{{ $post->shared->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <!-- /post header -->

                                        {{-- shared post content start --}}
                                        @if($post->shared->postType->name == 'Article')
                                        <div class="post-data">
                                            <h4 class="article-heading">{{ $post->shared->heading }}</h4>
                                            <p>
                                                {{ $post->shared->description }}
                                            </p>
                                            <p class="post-translation">
                                                <a href="{{route('discover')}}?hashtags={{preg_replace("/#/i", "", $post->hashtags)}}"><button>{{$post->hashtags}}</button></a>

                                            </p>

                                            @if(!$post->shared->postMedia->isEmpty())
                                                {{-- <iframe src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" width="100%" height="500px">
                                                </iframe> --}}

                                                {{-- <embed src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" type="application/pdf" width="100%" height="500px" /> --}}
                                                <iframe src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" width="100%" height="500px"></iframe>
                                            @endif

                                        </div>
                                        @elseif($post->shared->postType->name == 'Photo' || $post->postType->name == 'Video')
                                            <div class="post-data">
                                                <p>
                                                    {{ $post->shared->description }}
                                                </p>
                                                <p class="post-translation">
                                                    <a href="{{route('discover')}}?hashtags={{preg_replace("/#/i", "", $post->hashtags)}}"><button>{{$post->hashtags}}</button></a>

                                                </p>
                                                @php
                                                    $mediaCount = count($post->shared->postMedia);
                                                @endphp
                                                @if($mediaCount == 1)
                                                    @if($post->shared->postType->name == 'Photo')
                                                    <img src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" class="postMediaContent" alt="Modal Image" />
                                                    @elseif($post->shared->postType->name == 'Video')
                                                    <video style="width: 100%;" controls>
                                                        <source src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" type="video/mp4">
                                                        <source src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" type="video/mkv">
                                                        Your browser does not support HTML video.
                                                    </video>
                                                    @endif
                                                @else

                                                    @if($mediaCount == 2)
                                                    <div class="row">
                                                        <div class="col-md-6 imgShowColumn1">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" class="postImage postMediaContent" alt="" />
                                                        </div>
                                                        <div class="col-md-6 imgShowColumn2">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[1]->name }}" class="postImage postMediaContent" alt="" />
                                                        </div>
                                                    </div>
                                                    @elseif($mediaCount == 3)
                                                    <div class="row">
                                                        <div class="col-md-12 singleImage">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" class="postMediaContent" alt="" />
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 imgShowColumn1">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" class="postImage postMediaContent" alt="" />
                                                        </div>
                                                        <div class="col-md-6 imgShowColumn2">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[1]->name }}" class="postImage postMediaContent" alt="" />
                                                        </div>
                                                    </div>
                                                    @elseif($mediaCount == 4)
                                                    <div class="row">
                                                        <div class="col-md-6 imgShowColumn1">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" class="postImage postMediaContent" alt="" />
                                                        </div>
                                                        <div class="col-md-6 imgShowColumn2">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[1]->name }}" class="postImage postMediaContent" alt="" />
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 imgShowColumn3">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[2]->name }}" class="postImage postMediaContent" alt="" />
                                                        </div>
                                                        <div class="col-md-6 imgShowColumn4">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[3]->name }}" class="postImage postMediaContent" alt="" />
                                                        </div>
                                                    </div>
                                                    @elseif($mediaCount > 4)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" class="postMediaContent" alt="" />
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                    <div class="col-md-4 imgShowColumn1">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[1]->name }}" class="postMediaContent" alt="" />
                                                    </div>
                                                    <div class="col-md-4 imgShowColumn2 imgShowColumn3">
                                                        <img src="{{ $urlPost }}/{{ $post->shared->postMedia[2]->name }}" class="postMediaContent" alt="" />
                                                    </div>
                                                    <div class="col-md-4 imgShowColumn3" id="moreImagesbox">
                                                    <span id="moreImages">
                                                            +{{ $mediaCount - 3 }}
                                                        </span>
                                                    </div>
                                                    </div>

                                                    @endif <!-- /count == 2 -->
                                                @endif <!-- /count == 1 -->

                                            </div>
                                        @elseif($post->shared->postType->name == 'Job')
                                            <div class="job-post-data">
                                                <img src="{{ $urlPost }}/{{ $post->shared->postMedia[0]->name }}" alt="" />
                                                <div class="job-post-details">
                                                    <h2>{{ $post->shared->jobs->job_title }}</h2>
                                                    <p>{{ $post->shared->jobs->description }}</p>
                                                    <div class="job-post-descrption-detail">
                                                        <span>{{ $post->shared->jobs->location }}</span> .
                                                        <span>{{ $post->shared->jobs->employeeType->name }}</span> .
                                                        <span>{{ $post->shared->jobs->salary_from }}-{{ $post->shared->jobs->salary_to }}k /month</span>
                                                        <a class="btn-primary" href="{{ route('job.detail', $post->shared->jobs->id) }}">Apply now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- shared post content end --}}
                                </div>
                                @endif
                                <!-- /post body -->

                                <!-- post interactions -->
                                <div class="post-interactions">
                                    <div class="interactions-amount">
                                        <div class="rating-stars">
                                            <ul id="stars">
                                            <li class="star 1 2 3 4 5 @if(!$post->rate->isEmpty() && $post->rate[0]->stars >= 1) selected @endif" title="Poor" data-value="1"  data-rate="@if(!$post->rate->isEmpty()) {{ $post->rate[0]->id }} @endif">
                                                <i class="fa fa-star fa-fw"></i>
                                            </li>
                                            <li class="star 2 3 4 @if(!$post->rate->isEmpty() && $post->rate[0]->stars >= 2 ) selected @endif" title="Fair" data-value="2" data-rate="@if(!$post->rate->isEmpty()) {{ $post->rate[0]->id }} @endif">
                                                <i class="fa fa-star fa-fw"></i>
                                            </li>
                                            <li class="star 3 4 5 @if(!$post->rate->isEmpty() && $post->rate[0]->stars >= 3) selected @endif" title="Good" data-value="3" data-rate="@if(!$post->rate->isEmpty()) {{ $post->rate[0]->id }} @endif">
                                                <i class="fa fa-star fa-fw"></i>
                                            </li>
                                            <li class="star 4 5 @if(!$post->rate->isEmpty() && $post->rate[0]->stars >= 4) selected @endif" title="Excellent" data-value="4" data-rate="@if(!$post->rate->isEmpty()) {{ $post->rate[0]->id }} @endif">
                                                <i class="fa fa-star fa-fw"></i>
                                            </li>
                                            <li class="star 5 @if(!$post->rate->isEmpty() && $post->rate[0]->stars == 5) selected @endif" title="WOW!!!" data-value="5" data-rate="@if(!$post->rate->isEmpty()) {{ $post->rate[0]->id }} @endif">
                                                <i class="fa fa-star fa-fw"></i>
                                            </li>
                                            </ul>
                                        </div>
                                        <span class="amount-info">{{ $post->view_count }} Views</span>
                                    </div>
                                    <div class="interactions-btns">
                                    @if($post->rate->isEmpty())
                                        <button>
                                        <span class="counter ratePost"><i class="far fa-star"></i></span>
                                        <span class="ratePost rateCounter">Rate [{{ $post->rate_count }}]</span>
                                        </button>
                                    @else
                                        <button>
                                        <span class="counter ratePost"><i class="fa fa-star fa-clicked"></i></span>
                                        <span class="ratePost rateCounter">Rate [{{ $post->rate_count }}]</span>
                                        </button>
                                    @endif

                                        <button>
                                            <span class="counter"><img src="{{asset('assets/img/Group8.png')}}" alt=""></span>
                                            <span>Reflect [{{ $post->reflections_count }}]</span>
                                        </button>
                                        <button class="repost-button">
                                            <span class="counter"><img src="{{asset('assets/img/Group5.png')}}" alt=""></span>
                                            <span>Repost [{{ $post->post_shared_count }}]</span>
                                        </button>
                                        <button>
                                            <span class="counter"><i class="fas fa-paper-plane"></i></span>
                                            <span class="post-send">Send</span>
                                        </button>
                                    </div>
                                </div>
                                <!-- /post interactions -->

                                <!-- add new comment -->
                                <div class="post-input">
                                    <div class="input-section">
                                        <figure class="image-container">
                                            @if(auth()->user()->profile_pic != null)
                                            <img class="img-size" src="{{ $urlProfile }}/{{ auth()->user()->profile_pic }}" alt="">
                                        @else
                                            <img class="img-size" src="{{ asset('assets/img/profile.png') }}" alt="">
                                        @endif
                                        </figure>
                                        <div class="input-portion">
                                            <div>
                                                <textarea class="form-control reflection" style="height: 43px;" type="text" placeholder="Add a reflection"></textarea>
                                            </div>
                                        </div>
                                        <button class="submit-reflection">
                                            <span class="counter"><i class="fas fa-paper-plane" aria-hidden="true"></i></span>
                                            <span>Reflect</span>
                                        </button>
                                    </div>
                                </div>
                                <!-- /add new comment -->

                                <!-- previous comments -->
                                <div class="commented-groups">
                                    <section>
                                        <header>
                                            <span class="heading-commented">Recent Reflections</span>
                                            <span
                                                class="fas fa-angle-down"
                                                onclick="toggleProfileGroupList(this)"
                                            ></span>
                                        </header>
                                        <ul class="group-list reflection-list">
                                        @foreach($post->reflections as $reflection)
                                            <li>
                                                <div class="operations-user">
                                                    <figure class="image-container">
                                                        @if($reflection->user->profile_pic != null)
                                                        <img class="img-size" src="{{ $urlProfile }}/{{ $reflection->user->profile_pic }}" alt="">
                                                        @else
                                                        <img class="img-size" src="{{ asset('assets/img/profile.png') }}" alt="">
                                                        @endif
                                                    </figure>
                                                    <div class="box-commented">
                                                        <div class="commented-description">
                                                            <strong class="post_name">{{ $reflection->user->firstname }} {{ $reflection->user->lastname }}</strong>
                                                            <span class="post_designation">
                                                                <span>&nbsp;·&nbsp;</span>
                                                                5Y Exp
                                                            </span>
                                                        </div>
                                                        <span class="designation">{{ $reflection->user->current_position }}</span>
                                                        <span class="comment-user">{{ $reflection->reflection }}</span>
                                                    </div>
                                                </div>
{{--                                                <div class="like-reply-part">--}}
{{--                                                    <span>Like <span>&nbsp;·&nbsp;</span> Reply</span>--}}
{{--                                                </div>--}}
                                            </li>
                                            @endforeach
                                        </ul>
                                        <div class="load-more">
                                            <a href="" data-value="0">Load more reflections</a>
                                        </div>
                                    </section>
                                </div>
                                <!-- /previous comments -->
                            </article>

                            @endforeach
                      </div>
                  </div>
                  <div class="tab-pane" id="about" role="tabpanel">
                      <div class="little-overview round-shadow">
                        {{ $page->about }}
                        <ul>
                            <li><strong>Website</strong> {{ $page->website }}</li>
                            <li><strong>Industry</strong> {{ $page->industry }}</li>
                            <li><strong>Company Size</strong> {{ $page->company_size }}</li>
                            <li><strong>Type</strong> {{ $page->company_type }}</li>
                        </ul>
                      </div>
                      {{-- <div class="little-overview round-shadow">
                        <h4>Location</h4>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d217635.8780925763!2d74.21890031640625!3d31.536226300000003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3919051df76a2abf%3A0x44b6d8fce5d48955!2sNestl%C3%A9%20Pakistan%20Limited%20Head%20office!5e0!3m2!1sen!2s!4v1610484773251!5m2!1sen!2s" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                      </div> --}}
                  </div>
                  <div class="tab-pane" id="jobs" role="tabpanel">
                      <section class="tag-list company-jobs">
                        <ul>
                            @foreach($posts as $post)
                                @if($post->postType->name == 'Job')

                            <li>
                                <a href="{{ route('job.detail', $post->jobs->id) }}">
                                    <div class="hashtag job-lists">
                                        <img class="job-logo" src="{{ $urlPost }}/{{ $post->postMedia[0]->name }}" alt="">
                                        <h3>{{ $post->jobs->job_title }} - {{ $post->jobs->salary_from }}-{{ $post->jobs->salary_to }}k /month</h3>
                                        <span>{{ $post->jobs->employeeType->name }}</span>
                                        <span>{{ $post->jobs->location }}</span>
                                    </div>
                                </a>
                            </li>
                                @endif
                            @endforeach
                        </ul>
                    </section>
                  </div>
{{--                  <div class="tab-pane" id="people" role="tabpanel">--}}
{{--                      <section class="tag-list fellow-page company-jobs">--}}
{{--                        <ul>--}}
{{--                            <li>--}}
{{--                                <a href="profile.html">--}}
{{--                                    <div class="hashtag job-lists fellow-lists">--}}
{{--                                        <div class="bg-fellow">--}}
{{--                                            <img src="{{ asset('assets/img/mustafa-banner.png') }}" alt="">--}}
{{--                                        </div>--}}
{{--                                        <img class="fellow-img" src="{{ asset('assets/img/profile.png') }}" alt="">--}}
{{--                                        <h3>Muhammad Ali</h3>--}}
{{--                                        <span>Computer Scientist</span>--}}
{{--                                        <h6><i class="fas fa-users" aria-hidden="true"></i> 4 Mutual Connections</h6>--}}
{{--                                        <button class="btn__primary--large">Connect</button>--}}
{{--                                    </div>--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </section>--}}
{{--                  </div>--}}
                </div>
            </main>
        </div>
        <!-- RIGHT ASIDE -->
        <aside class="right-aside" id="right-aside">
            <div class="advertisement-section" id="advertisement-section">
                <div class="advertisement-portion">
                    <header>
                        <span>Advertisement</span>
                    </header>
                    <div class="advertisement-space">
{{--                        <figure>--}}
{{--                            <img src="{{ asset('assets/img/advertisement.png') }}" alt="">--}}
{{--                        </figure>--}}
                        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3431362589381697"
                                crossorigin="anonymous"></script>
                        <!-- skilledtalk -->
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-3431362589381697"
                             data-ad-slot="1988597801"
                             data-ad-format="auto"
                             data-full-width-responsive="true"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                </div>
            </div>
        </aside>
    </div>


    @include('custom.inc.postsCardModels')

@include('custom.inc.chatWidget')

@endsection
