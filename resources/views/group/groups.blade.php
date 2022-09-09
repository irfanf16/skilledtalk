@extends('custom.layouts.app')
@section('content')
@include('custom.inc.loading')
@include('custom.inc.header')

@include('custom.inc.alerts')

<section class="group-list inner-padding-top">
    <div class="container default-container">
        <div class="row marginleftright">
            <div class="col-12 paddingleftright">
                <a href="{{ route('group.create') }}" class="btn btn-info" style="float: right;">Create Group</a>
                <div class="section-heading">
                    <h3>Your Groups</h3>
                </div>
            </div>
            <div class="col-12 paddingleftright">
                <div class="group-lists">

                    @forelse ($groups as $group)
                    <div class="group-section">
                        <figure>
                            <img src="{{ $url }}/{{ $group->profile_pic }}" alt="" width="50" height="50">
                        </figure>
                        <div class="group-name">
                            <a href="{{ route('group.detail', $group->id) }}">{{ $group->name }}</a>
                            <p>{{ $group->members_count }} members</p>
                        </div>
                        <div class="vertical-icons">
                            <span class="fas fa-circle" aria-hidden="true"></span>
                            <span class="fas fa-circle" aria-hidden="true"></span>
                            <span class="fas fa-circle" aria-hidden="true"></span>
                            <div class="notifications-signs">
                                <ul>
                                    <li><a href=""><i class="far fa-copy"></i> Copy the link</a></li>
                                    <li><a href=""><i class="fas fa-sign-out-alt"></i> Leave the group</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @empty
                        <p>No Group</p>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</section>


@include('custom.inc.chatWidget')


@endsection
