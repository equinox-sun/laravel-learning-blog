<?php

namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Dflydev\ApacheMimeTypes\PhpRepository;

/**
 * 	
 */
class UploadsManager
{
	protected $disk;
	protected $mimeDetect;

	function __construct(PhpRepository $mimeDetect)
	{
		$this->disk = Storage::disk(config('blog.uploads.storage'));
		$this->mimeDetect = $mimeDetect;
	}

	public function folderInfo($folder)
	{
		$folder = $this->cleanFolder($folder);
		$breadcrumbs = $this->breadcrumbs($folder);
		$slice = array_slice($breadcrumbs, -1);//array_slice() 返回根据 offset 和 length 参数所指定的 array 数组中的一段序列。
		$folderName = current($slice);//返回当前被内部指针指向的数组单元的值，并不移动指针
		$breadcrumbs = array_slice($breadcrumbs, 0,-1);

		$subfolders =[];
		foreach (array_unique($this->disk->directories($folder)) as $subfolder) {
			$subfolders["/$subfolder"] = basename($subfolder);
		}

		$files=[];
		foreach ($this->disk->files($folder) as $path) {
			$files[] = $this->fileDetails($path);
		}

		return compact('folder','folderName','breadcrumbs','subfolders','files');
	}

	protected function cleanFolder($folder)
	{
		return '/'.trim(str_replace('..', '', $folder),'/');
	}

	/**
	 * 返回当前目录路径
	 * @Author   sunmc
	 * @DateTime 2020-05-22
	 * @param    [type]     $folder [description]
	 * @return   [type]             [description]
	 */
	protected function breadcrumbs($folder)
	{
		$folder = trim($folder,'/');
		$crumbs = ['/'=>'root'];

		if (empty($folder)) {
			return $crumbs;
		}
		$folders = explode('/', $folder);

		$build = '';
		foreach ($folders as $folder) {
			$build .= '/'.$folder;
			$crumbs[$build] = $folder;
		}

		return $crumbs;
	}

	protected function fileDetails($path)
	{
		$path = '/'.ltrim($path.'/');
		return [
			'name'=>basename($path),
			'fullPath'=>$path,
			'webPath'=>$this->fileWebpath($path),
			'mimeType'=>$this->fileMimeType($path),
			'size'=>$this->fileSize($path),
			'modified'=>$this->fileModified($path),
		];
	}

	public function fileWebpath($path)
	{
		$path = rtrim(config('blog.uploads.webpath'),'/').'/'.ltrim($path,'/');
		return url($path);
	}

	public function fileMimeType($path)
	{
		return $this->mimeDetect->findType(pathinfo($path,PATHINFO_EXTENSION));
	}

	public function fileSize($path)
	{
		return $this->disk->size($path);
	}

	public function fileModified($path)
	{
		return Carbon::createFromTimestamp($this->disk->lastModified($path));
	}

	/**
	 * 创建新目录
	 * @Author   sunmc
	 * @DateTime 2020-05-26
	 * @param    [type]     $folder [description]
	 * @return   [type]             [description]
	 */
	public function createDirectory($folder)
	{
		$folder = $this->cleanFolder($folder);
		if ($this->disk->exists($folder)) {
			return "Folder '$folder' already exists.";
		}
		return $this->disk->makeDirectory($folder);
	}

	/**
	 * 删除目录
	 * @Author   sunmc
	 * @DateTime 2020-05-26
	 * @param    [type]     $folder [description]
	 * @return   [type]             [description]
	 */
	public function deleteDirectory($folder)
	{
		$folder = $this->cleanFolder($folder);
		$filesFolders = array_merge($this->disk->directories($folder),$this->disk->files($folder));
		if (!empty($filesFolders)) {
			return "Directory must be empty to delete it";
		}
		return $this->disk->deleteDirectory($folder);
	}

	/**
	 * 删除文件
	 */
	public function deleteFile($path)
	{
		$path = $this->cleanFolder($path);
		if (!$this->disk->exists($path)) {
			return "File does not exist.";
		}
		return $this->disk->delete($path);
	}

	/**
	 * 保存文件
	 */
	public function saveFile($path,$content)
	{
		$path = $this->cleanFolder($path);
		if ($this->disk->exists($path)) {
			return "file already exists.";
		}
		return $this->disk->put($path,$content);
	}
}