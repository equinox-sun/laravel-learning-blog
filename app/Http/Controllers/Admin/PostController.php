<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Jobs\PostFormFields;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * 将表单验证分散到表单请求类中完成，控制器的代码量将会很小
 * 就是引入的有点多（狗头
 */
class PostController extends Controller
{
    protected $fieldList = [
        'title' => '',
        'subtitle' => '',
        'page_image' => '',
        'content' => '',
        'meta_description' => '',
        'is_draft' => "0",
        'publish_date' => '',
        'publish_time' => '',
        'layout' => 'blog.layouts.post',
        'tags' => [],
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.post.index',['posts'=>Post::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fields = $this->fieldList;
        // $when = Carbon::now()+addHour();
        $when = Carbon::now();
        $fields['publish_date'] = $when->format('Y-m-d');
        $fields['publish_time'] = $when->format('g:i A');

        foreach ($fields as $fieldName => $fieldValue) {
            $fields[$fieldName] = old($fieldName,$fieldValue);
        }
        $data = array_merge($fields,['allTags'=>Tag::all()->pluck('tag')->all()]);
        return view('admin.post.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostCreateRequest $request)
    {
        $post = Post::create($request->postFillData());
        $post->syncTags($request->get('tags',[]));
        return redirect()->route('post.index')->with('success','success.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fields = $this->fieldsFromModel($id,$this->fieldList);
        foreach ($fields as $fieldName => $fieldValue) {
            $fields[$fieldName] = old($fieldName,$fieldValue);
        }
        $data = array_merge($fields,['allTags'=>Tag::all()->pluck('tag')->all()]);
        return view('admin.post.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostCreateRequest $request, $id)
    {
        $post = Post::findorFail($id);
        $post->fill($request->postFillData());
        $post->save();
        $post->syncTags($request->get('tags',[]));

        if ($request->action === 'continue') {
            return redirect()->back()->with('success','had saved.');
        }
        return redirect()->route('post.index')->with('success','had saved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findorFail($id);
        $post->tags()->detach();
        $post->delete();

        return redirect()->route('post.index')->with('success','has deleted');
    }

    private function fieldsFromModel($id,array $fields)
    {
        $post = Post::findorFail($id);
        $fieldName = array_keys(array_except($fields,['tags']));

        $fields = ['id'=>$id];
        foreach ($fieldName as $field) {
            $fields[$field] = $post->{$field};
        }

        $fields['tags'] = $post->tags->pluck('tag')->all();

        return $fields;
    }
}
