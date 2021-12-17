@extends('layouts.app')

@section('javascript')
<script src="/js/confirm.js"></script>
@endsection

@section('content')
<div id="edit">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            メモ編集
            <form id="delete-form" action="{{ route('destroy') }}" method="POST">
                @csrf
                <input type="hidden" name="memoId" value="{{ $editPost[0]['id']}}">
                <i onclick="deleteHandle(event);" class="fas fa-trash"></i>
            </form>
        </div>

        <form class="card-body my-card-body" action="{{ route('update') }}" method="POST">
            @csrf
            <input type="hidden" name="memoId" value="{{ $editPost[0]['id']}}">
            <div class="form-group">
                <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">{{ $editPost[0]['content'] }}</textarea>
            </div>
            @error('content')
                <div class="alert alert-danger">メモ内容を入力してください。</div>
            @enderror
            <div class="form-group form-inline" v-for="tag in tagList">
                <input type="text" class="form-control mr-1 tags" v-bind:name="tag.name" v-bind:value="tag.content" placeholder="新しいタグを入力">
                <button type="button" v-if="tagList.length > 1" class="btn btn-danger mr-1" v-on:click="removeTag(tag.id)">×</button>
                <button type="button" v-if="tag.id == max" class="btn btn-primary" v-on:click="addTag">タグを追加</button>
            </div>
            <input type="hidden" name="reply_post_id" value="{{ $id }}" v-if="replyId > 0">
            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
</div>
@endsection

@section('vue')
<script>
    var create = new Vue({
        el: '#edit',
        data: {
            tagList: @JSON($includeTags),
            max: {{ $max }},
            replyId: {{ $editPost['reply_user_id'] ?? 0 }}
        },
        methods:{
            addTag: function () {
                this.updateMax("add");
                this.tagList.push({
                    id: this.max,
                    content: "",
                    name: "tag[" + this.max + "]"
                })
            },
            updateMax: function (event) {
                var max = this.tagList.reduce(function (a, b) {
                    return a > b.id ? a : b.id
                }, 0);
                if (event == "add") {
                    this.max = max + 1;
                } else if (event == "remove") {
                    this.max = max;
                }
            },
            removeTag: function (id) {
                this.tagList = this.tagList.filter(item=> item.id != id);
                this.updateMax("remove");
            },
        }
    })
</script>
@endsection
