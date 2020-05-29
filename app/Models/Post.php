<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Services\Markdowner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $dates = ['published_at'];
    protected $fillable = ['title', 'subtitle', 'content_raw', 'page_image', 'meta_description','layout', 'is_draft', 'published_at',];

    public function setTitleAttribute($value)
    {
    	$this->attributes['title'] = $value;

    	if (!$this->exists) {
    		// $this->attributes['slug'] = Str::slug($value);
    		$value=uniqid(str_random(8));
    		$this->setUniqueSlug($value,0);
    	}
    }

    public function tags()
    {
    	return $this->belongsToMany(Tag::class,'post_tag_pivot');
    }

    protected function setUniqueSlug($title,$extra)
    {
    	$slug = Str::slug($title.'-'.$extra);
    	if (static::where('slug',$slug)->exists()) {
    		$this->setUniqueSlug($title,$extra+1);
    		return;
    	}
    	$this->arrtibutes['slug'] = $slug;
    }

    public function setContentRawAttribute($value)
    {
    	$markdown = new Markdowner();
    	$this->attributes['content_raw'] = $value;
    	$this->attributes['content_html'] = $markdown->toHTML($value);
    }

    public function syncTags(array $tags)
    {
    	Tag::addNeededTags($tags);
    	if (count($tags)) {
    		$this->tags()->sync(
    			Tag::whereIn('tag',$tags)->get()->plunk('id')->all()
    		);
    		return;
    	}
    	$this->tags()->detach();
    }

    /**
     * 返回 published_at 字段的日期部分
     */
    public function getPublishDateAttribute($value)
    {
        return $this->published_at->format('Y-m-d');
    }

    /**
     * 返回 published_at 字段的时间部分
     */
    public function getPublishTimeAttribute($value)
    {
        return $this->published_at->format('g:i A');
    }

    /**
     * content_raw 字段别名
     * 使用 $post->content 就会执行该方法
     */
    public function getContentAttribute($value)
    {
        return $this->content_raw;
    }
}
