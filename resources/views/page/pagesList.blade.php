@extends('custom.layouts.app')
@section('content')
@include('custom.inc.loading')
@include('custom.inc.header')

<style>

</style>

<section class="tag-section inner-padding-top">
    <div class="container default-container">
        <div class="row marginleftright">
            <div class="col-12 tag-count">
                <h6>Page Search </h6>
                <form>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form__input--floating">
                                <label class="form__label--floating" id="label--text">Page Search </label>
                                <div class="search-jobs">
                                    <i class="fas fa-search"></i>
                                    <input id="input--text" type="text" placeholder="Search by title, skill or company" name="search">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button class="btn__primary--large from__button--floating">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="tag-list searchjob-lists ">
    <div class="container default-container">
        <div class="row marginleftright">
            <div class="col-12 paddingleftright">
                <div class="">
                </div>
                <ul>
                    @forelse ($pagesList as $page)
                        <li>
                            <a href="{{ route('page.detail', $page->id) }}">
                                <div class="hashtag job-lists">

                                    <img class="job-logo" style="width: auto;height: 150px!important;" src="{{ env('PAGE_CONTENT_URL') }}/{{ $page->logo }}" alt="">
                                    <h3>{{ $page->name }}</h3>
                                    <span>{{ $page->about }}</span>
                                    {{-- <div class="viewjob-connections">
                                        <img class="img-size" src="assets/img/Ellipse2.png" alt="">
                                        <p>1 Connection</p>
                                    </div> --}}
                                    <hr/>
                                    <a class="btn btn-primary follow_page " href="javascript:void(0)"  data-page="{{$page->id}}">
                                        Follow
                                    </a>
                                </div>
                            </a>
                        </li>
                    @empty
                        <p style="text-align: center">No Job Found</p>
                    @endforelse

                </ul>
            </div>

        </div>
        <div >
            {{ $pagesList->links('vendor.pagination.custom') }}

        </div>
    </div>
</section>
@include('custom.inc.chatWidget')
@endsection
