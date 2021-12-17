@extends('layouts.app')

@section('content')

<form action="{{route('store')}}" enctype="multipart/form-data" method="POST">
    @csrf
    <div id="create">
        <div class="card">
            <div class="card-header">投稿</div>
            <div class="card-body my-card-body">
                <div class="form-group">
                    <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力"></textarea>
                </div>

                <div class="form-group form-inline" v-for="tag in tagList">
                    <input type="text" class="form-control mr-1 tags" v-bind:name="tag.name" placeholder="新しいタグを入力">
                    <button type="button" v-if="tagList.length > 1" class="btn btn-danger mr-1" v-on:click="removeTag(tag.id)">×</button>
                    <button type="button" v-if="tag.id == max" class="btn btn-primary" v-on:click="addTag">タグを追加</button>
                </div>
                <input type="hidden" name="reply_post_id" value="{{ $id }}" v-if="replyId > 0">
                <button type="submit" class="btn btn-primary text-white">投稿</button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('vue')
<script>
    var create = new Vue({
        el: '#create',
        data: {
            tagList: [
                {id: 1, content: "", name: "tag[1]"}
            ],
            max: 1,
            replyId: {{ $id }}
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
