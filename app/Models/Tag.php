<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['tag','title','subtitle','page_image','meta_description','reverse_direction'];

    public function posts()
    {
    	return $this->belongsToMany(Post::class,'post_tag_pivot');
    }

    public static function addNeededTags(array $tags)
    {
    	if (count($tags) === 0) {
    		return;
    	}
    	$found = static::whereIn('tag',$tags)->get()->pluck('tag')->all();

    	foreach (array_diff($tags,$found) as $tag) {
    		static::create([
    			'tag' => $tag,
    			'title' => $tag,
    			'subtitle' => 'subtitle for'.$tag,
    			'page_image' => '',
    			'meta_description' => '',
    			'reverse_direction' => false,
    		]);
    	}
    }
}
