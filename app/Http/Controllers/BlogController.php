<?php

/**
 * 在控制器中，我们使用 Eloquent ORM 与数据库进行交互，并使用辅助函数 view() 渲染视图。
 */
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Services\PostService;
// use Carbon\Carbon; 不需要了，在 PostService 里
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
    	/**$posts = Post::where('published_at','<=',Carbon::now())
    				->orderBy('published_at','desc')
    				->paginate(config('blog.posts_per_page'));
    	return view('blog.index',compact('posts'));**/

        $tag = $request->get('tag');
        $postService = new PostService($tag);
        $data = $postService->lists();//改成从 postService 中获取
        $layout = $tag ? Tag::layout($tag) : 'blog.layouts.index';
        return view($layout,$data);
    }

    /**
     * 使用了渴求式加载获取指定文章标签信息（渴求式加载有效减少了必须要被执行用以加载模型关联的 SQL 查询）
     * 渴求式加载=>https://www.cnblogs.com/lxwphp/p/10727259.html
     * 5.7 模型关联关系 https://xueyuanjun.com/post/9584.html
     */
    public function showPost($slug)
    {
    	$post = Post::with('tags')->where('slug',$slug)->firstOrFail();
        $tag = $request->get('tag');
        if ($tag) {
            $tag = Tag::where('tag',$tag)->firstOrFail();
        }
    	return view($post->layout,compact('post', 'tag'));
    }
}
