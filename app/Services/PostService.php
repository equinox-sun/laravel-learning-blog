<?php
/**
 * 
 */

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;

class PostService
{
	protected $tag;
	function __construct($tag)
	{
		$this->tag = $tag;
	}

	public function lists()
	{
		//如果传入标签，那么调用 tagIndexData 方法返回根据标签进行过滤的文章列表
		if ($this->tag) {
			return $this->tagIndexData($this->tag);
		}
		//否则调用 normalIndexData 返回正常文章列表
		return $this->normalIndexData();
	}

	protected function normalIndexData()
	{
		$posts = Post::with('tags')
					->where('published_at','<=',Carbon::now())
					->where('is_draft',0)
					->orderBy('published_at','desc')
					->simplePaginate(config('blog.posts_per_page'));

		return [
            'title' => config('blog.title'),
            'subtitle' => config('blog.subtitle'),
            'posts' => $posts,
            'page_image' => config('blog.page_image'),
            'meta_description' => config('blog.description'),
            'reverse_direction' => false,
            'tag' => null,
		];
	}

	protected function tagIndexData($tag)
	{
		$tag = Tag::where('tag',$tag)->firstOrFail();
		$reverse_direction = (bool)$tag->reverse_direction;
		$posts = Post::where('published_at','<=',Carbon::now())
						->whereHas('tags',function ($q) use ($tag)
						{
							$q->where('tag','=',$tag->tag);
						})
						->where('is_draft',0)
						->orderBy('published_at',$reverse_direction?'asc':'desc')
						->simplePaginate(config('blog.posts_per_page'));
		$posts->append('tag',$tag->tag);

		$page_image = $tag->page_image?:config('blog.page_image');

		return [
            'title' => $tag->title,
            'subtitle' => $tag->subtitle,
            'posts' => $posts,
            'page_image' => $page_image,
            'tag' => $tag,
            'reverse_direction' => $reverse_direction,
            'meta_description' => $tag->meta_description ?: config('blog.description'),
		];
	}
}