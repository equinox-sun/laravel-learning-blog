<?php
use Illuminate\Support\Str;

/**
 * @Author   sunmc
 * @DateTime 2020-05-22
 * 返回可读性更好的文件尺寸
 * @param    [type]     $bytes    [description]
 * @param    integer    $decimals [description]
 * @return   [type]               [description]
 */
function human_filesize($bytes,$decimals=2)
{
	$size = ['B','KB','MB','GB','TB','PB'];
	$factor = floor((strlen($bytes)-1)/3);//为什么除以3
	return sprintf("%.{$decimals}f",$bytes/pow(1024,$factor)).@$size[$factor];
}

function is_image($mimeType)
{
	return Illuminate\Support\Str::startsWith($mimeType,'image/');
}

/**
 * 用于在视图的复选框和单选框中设置 checked 属性。
 */
function checked($value)
{
	return $value?'checked':'';
}

/**
 * 用于返回上传图片的完整路径
 */
function page_image($value=null)
{
	if (empty($value)) {
		$value = config('blog.page_image');
	}
	if (!Str::startsWith($value,'http') && !Str::startsWith($value,'/') ) {
		$value = config('blog.uploads.webpath').'/'.$value;
	}
	return $value;
}

function array_except($array, $keys)
{
	return array_diff_key($array, array_flip((array) $keys));
}