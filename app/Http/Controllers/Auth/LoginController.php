<?php
/**
 * 如果查看 LoginController 类（app/Http/Controllers/Auth/LoginController）的 logout() 方法，你会发现并没有该方法，在其父类中也没有 logout() 方法，这是因为 LoginController 使用了 AuthenticatesUsers Trait，logout() 方法正是定义在了  AuthenticatesUsers Trait 中。
 */
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

	public function showLoginForm()
	{
		return view('admin.auth.login');
	}

    // 退出后重定向到登录页
    // 其余操作都在 AuthenticatesUsers 的loggedOut 中完成
    public function loggedOut(Request $request)
    {
        return redirect('/login');
    }
}