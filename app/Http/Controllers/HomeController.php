<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile($id)
    {
        $profileInfo = $this->getprofile($id);
        $user = $profileInfo['user'];
        $userPosts = $profileInfo['userPosts'];
        $followCount = $profileInfo['followCount'];
        $followerCount = $profileInfo['followerCount'];
        $userId = $id;
        $isFollow = $profileInfo['isFollow'];

        return view('profile', compact('user', 'userPosts', 'followCount', 'followerCount', 'userId', 'isFollow'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function follow(Request $request)
    {
        $posts = $request->all();
        $request->validate(['follow_user_id' => 'required']);

        Follow::insert([
            'user_id' => \Auth::id(),
            'follow_user_id' => $posts['follow_user_id']
        ]);

        $profileInfo = $this->getprofile($posts['follow_user_id']);
        $user = $profileInfo['user'];
        $userPosts = $profileInfo['userPosts'];
        $followCount = $profileInfo['followCount'];
        $followerCount = $profileInfo['followerCount'];
        $userId = $posts['follow_user_id'];
        $isFollow = $profileInfo['isFollow'];

        return view('profile', compact('user', 'userPosts', 'followCount', 'followerCount', 'userId', 'isFollow'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $tags = Tag::where('user_id', '=', \Auth::id())->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        // return view('create', compact('tags'));
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $id = 0;
        return view('create', compact('id'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id)
    {
        $editPost = Post::select('posts.*', 'tags.id AS tag_id')
            ->leftJoin('post_tags', 'post_tags.post_id', '=', 'posts.id')
            ->leftJoin('tags', 'post_tags.tag_id', '=', 'tags.id')
            ->where('posts.user_id', '=', \Auth::id())
            ->where('posts.id', '=', $id)
            ->whereNull('posts.deleted_at')
            ->get();

        $includeTags = [];
        foreach ($editPost as $post) {
            $tag = Tag::where('id', '=', $post['tag_id'])->get();
            $tagData = ['id' => $post['tag_id'], 'content' => $tag[0]['content'], 'name' => 'tag['.$post['tag_id'].']'];
            array_push($includeTags, $tagData);
        }
        $max = count($includeTags);
        $id = $editPost[0]['reply_user_id'];

        return view('edit', compact('editPost', 'includeTags', 'max', 'id'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update(Request $request)
    {
        // $posts = $request->all();
        // $request->validate(['content' => 'required']);
        // FacadesDB::transaction(function() use ($posts) {
        //     $this->insertTag($posts, true);
        //     Memo::where('id', $posts['memoId'])->update(['content' => $posts['content']]);
        // });

        return redirect(route('home'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function reply($id)
    {
        return view('create', compact('id'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function destroy(Request $request)
    {
        // FacadesDB::transaction(function() use ($request) {
        //     $posts = $request->all();
        //     $date = new Carbon();
        //     Memo::where('id', $posts['memoId'])->update(['deleted_at' => $date::now()]);
        // });

        return redirect(route('home'));
    }

    /**
     * 投稿機能
     * 引数1: Request $request
     * 返却値: ホーム画面へリダイレクト
     *
     * @return \Illuminate\routing\Redirector
     */
    public function store(Request $request)
    {
        $posts = $request->all();
        $request->validate(['content' => 'required']);

        DB::transaction(function() use ($posts) {
            $this->insertTag($posts, false);
        });

        return redirect(route('home'));
    }

    /**
     * 投稿、編集処理
     * 引数1: Array $posts
     * 引数2: Boolean $isEdit
     *
     */
    public function insertTag($posts, $isEdit)
    {
        // インスタンス化
        $postModel = new Post();
        // 返信ユーザーID取得
        $replyUserId = $postModel->getReplyUser();
        $replyPostId = null;
        if (in_array('reply_post_id', $posts)) {
            $replyPostId = $posts['reply_post_id'];
        }

        if (!$isEdit) {
            $postId = Post::insertGetId([
                'content' => $posts['content'],
                'user_id' => \Auth::id(),
                'created_at' => Carbon::now(),
                'reply_user_id' => $replyUserId,
                'reply_post_id' => $replyPostId
            ]);
        } else {
            $postId = $posts['memoId'];
            PostTag::where('post_id', $postId)->delete();
        }

        if (!empty($posts['tag'])) {
            foreach ($posts['tag'] as $tag) {
                if ($tag) {
                    $tagId = Tag::insertGetId(['content' => $tag]);
                    PostTag::insert(['post_id' => $postId, 'tag_id' => $tagId]);
                }
            }
        }
    }

    /**
     * プロフィール取得処理
     * 引数1: Array $posts
     * 引数2: Boolean $isEdit
     *
     */
    public function getProfile($id)
    {
        $user = User::where('id', '=', $id)->get()[0];

        $query = Post::query()
            ->select(
                'posts.content',
                'postusers.id as postuserid',
                'replyuser.id as replyuserid',
                'postusers.name as postuser',
                'replyuser.name as replyuser'
            )
            ->leftJoin('users as postusers', 'postusers.id', '=', 'posts.user_id')
            ->leftJoin('users as replyuser', 'replyuser.id', '=', 'posts.reply_user_id')
            ->whereNull('deleted_at')
            ->where('postusers.id', '=', $id)
            ->orderBy('posts.updated_at', 'DESC');

        $countQueryFollow = Follow::query()
            ->select(DB::raw("count(follows.user_id) as follow"))
            ->where('follows.user_id', '=', $id);

        $countQueryFollower = Follow::query()
            ->select(DB::raw("count(follows.follow_user_id) as follower"))
            ->where('follows.follow_user_id', '=', $id);

        $isFollowQuery = Follow::query()
            ->select('follows.follow_user_id as follower')
            ->where('follows.follow_user_id', '=', $id)
            ->where('follows.user_id', '=', \Auth::id());

        $userPosts = $query->get();

        $followCount = count($countQueryFollow->get()) ? $countQueryFollow->get()[0] : 0;
        $followerCount = count($countQueryFollower->get()) ? $countQueryFollower->get()[0] : 0;
        $isFollow = count($isFollowQuery->get());

        return [
            'user' => $user,
            'userPosts' => $userPosts,
            'followCount' => $followCount,
            'followerCount' => $followerCount,
            'isFollow' => $isFollow
        ];
    }
}
