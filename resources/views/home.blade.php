@extends('layouts.app')

@section('content')
<div id="home">
    <div class="card">
        <div class="card-header">ホーム</div>
        @foreach($posts as $post)
            <div class="card-body my-card-body-post">
                <div class="content-info">
                    @if($post['replyuser'])
                        <p class="card-text d-block elipsis">返信先：<a href="/profile/{{$post['replyuserid']}}">{{ $post['replyuser'] }}</a></p>
                    @endif
                    <a href="/profile/{{$post['postuserid']}}" class="card-text d-block elipsis user-name">
                        <img class="profile-img" src="{{ asset('img/uver.jpg') }}" alt="">
                        {{ $post['postuser'] }}
                    </a>
                    <span class="card-text d-block elipsis content">{{ $post['content'] }}</a>
                </div>
                <div class="action">
                    <a href="/reply/{{$post['id']}}" class=""><i class="fas fa-reply"></i></a>
                    <a href="/edit/{{$post['id']}}" class=""><i class="fas fa-pen"></i></a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
