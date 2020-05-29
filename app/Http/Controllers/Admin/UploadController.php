<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UploadsManager;
use Illuminate\Http\Request;
use App\Http\Requests\UploadFileRequest;
use App\Http\Requests\UploadNewFolderRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    protected $manager;

    // 构造方法中注入了 UploadsManager 依赖
    function __construct(UploadsManager $manager)
    {
    	$this->manager = $manager;
    }

    public function index(Request $request)
    {
    	$folder = $request->get('folder');
    	$data = $this->manager->folderInfo($folder);

    	return view('admin.upload.index',$data);
    }

    public function createFolder(UploadNewFolderRequest $request)
    {
        $new_folder = $request->get('new_folder');
        $folder = $request->get('folder').'/'.$new_folder;
        $result = $this->manager->createDirectory($folder);

        if ($result===true) {
            return redirect()->back()->with('success','folder'.$new_folder.'create success');
        }
        $error = $result?:'创建目录出错';
        return redirect()->back()->withErrors([$error]);
    }

    public function deleteFile(Request $request)
    {
        $del_file = $request->get('del_file');
        $path = $request->get('folder').'/'.$del_file;

        $result = $this->manager->deleteFile($path);
        if ($result===true) {
            return redirect()->back()->with('success','file['.$del_file.']  has been deleted');
        }
        $error = $result?:"An error occurred deleting file.";
        return redirect()->back()->withErrors([$error]);
    }

    public function deleteFolder(Request $request)
    {
        $del_folder = $request->get('del_folder');
        $folder = $request->get('folder').'/'.$del_folder;

        $result = $this->manager->deleteDirectory($folder);
        if ($result===true) {
            return redirect()->back()->with('success','folder['.$del_folder.']  has been deleted');
        }
        $error = $result?:"An error occurred deleting directory.";
        return redirect()->back()->withErrors([$error]);
    }

    public function uploadFile(UploadFileRequest $request)
    {
        $file = $_FILES['file'];
        $fileName = $request->get('file_name');
        $fileName = $fileName?:$file['name'];
        $path = Str::finish($request->get('folder'),'/').$fileName;
        $content = File::get($file['tmp_name']);

        $result = $this->manager->saveFile($path,$content);
        if ($result===true) {
            return redirect()->back()->with('success','file['.$fileName.']  has been uploaded');
        }
        $error = $result?:"An error occurred uploading file.";
        return redirect()->back()->withErrors([$error]);
    }
}

