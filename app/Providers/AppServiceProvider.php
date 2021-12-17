<?php

namespace App\Providers;

use App\Models\Post;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            // 自分のメモ取得はモデルに任せる
            // インスタンス化
            $postModel = new Post();
            // メモ取得
            $posts = $postModel->getMyPost();

            /*
            $tags = Tag::where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->get();
            */

            $view->with('posts', $posts);
                //->with('tags', $tags);
        });
    }
}
