<?php
/**
 * 表单请求的神奇之处在于会在表单请求类实例化的时候对请求进行验证，如果验证失败，会直接返回表单提交页面并显示错误信息。这意味着如果将表单请求作为控制器方法参数，那么验证工作将会在执行对应方法第一行代码之前进行。
 */
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * 验证用户是否经过登录认证
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 返回验证规则数组
     * @return array
     */
    public function rules()
    {
        return [
            'tag' => 'bail|required|unique:tags,tag',
            'title'=> 'required',
            'subtitle' => 'required',
            'layout' => 'required'
        ];
    }
}
