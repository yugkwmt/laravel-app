<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public function getMyPost () {
        $query = Post::query()->select('posts.*', 'postusers.id as postuserid', 'replyuser.id as replyuserid', 'postusers.name as postuser', 'replyuser.name as replyuser')
            ->leftJoin('users as postusers', 'postusers.id', '=', 'posts.user_id')
            ->leftJoin('users as replyuser', 'replyuser.id', '=', 'posts.reply_user_id')
            ->whereNull('deleted_at')
            ->orderBy('posts.updated_at', 'DESC');

        $posts = $query->get();

        return $posts;
    }

    public function getReplyUser () {
        $query = Post::query()->select('users.id')
            ->leftJoin('users', 'users.id', '=', 'posts.user_id')
            ->whereNull('deleted_at');

        $posts = $query->get();

        if (count($posts) > 0) {
            return $posts[0]['id'];
        } else {
            return null;
        }
    }

}
