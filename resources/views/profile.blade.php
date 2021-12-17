@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">プロフィール</div>
    <div class="card-body my-card-body-post">
        <p class="card-text d-block elipsis">{{ $user['name'] }}</p>
        @if($isFollow)
            <form id="delete-form" action="{{ route('follow') }}" method="POST">
                @csrf
                <input type="hidden" name="follow_user_id" value="{{ $userId }}">
                <button type="submit" class="btn btn-primary">フォロー</button>
            </form>
        @endif
        <p class="card-text d-block elipsis">フォロー：{{ $followCount['follow'] }}</p>
        <p class="card-text d-block elipsis">フォロワー：{{ $followerCount['follower'] }}</p>
    </div>
    @foreach($userPosts as $post)
        <div class="card-body my-card-body-post">
            @if($post['replyuser'])
                <p class="card-text d-block elipsis">返信先：<a href="/profile/{{$post['replyuserid']}}">{{ $post['replyuser'] }}</a></p>
            @endif
            <a href="/" class="card-text d-block elipsis">{{ $post['postuser'] }}</a>
            <a href="/edit/{{$post['id']}}" class="card-text d-block elipsis">{{ $post['content'] }}</a>
            <a href="/reply/{{$post['id']}}" class="card-text d-block elipsis">リプライ</a>
        </div>
    @endforeach
</div>
@endsection
