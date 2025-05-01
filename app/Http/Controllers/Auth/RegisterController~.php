<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Model\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Model\Pic;//Person in charge

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    //protected $redirectTo = '/';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [

            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country' => 'required|string|max:255',
            //'company_name' => 'required|string|max:255',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        //元々はreturn User::create([ だったのを戻り値モデルを変数に入れた
         $userdatamodel=User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'country' => $data['country'],
            'company_name' => $data['company_name'],
        ]);

        //追加 Person in chargeに上記の新規データを登録するようにした 2024-1-5
        $user = new User();
        $last = $user->latest('id')->first();
        $user_id = $last->id;//最後に作成されたuserのレコードID

        Pic::create([
            'default_destination' => '1',
            'user_id' => $user_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'country' => $data['country'],
            'company_name' => $data['company_name'],
        ]);

        $pic = new Pic();
        $pic_id = $pic->latest('id')->first();
        $pic_id = $pic_id->id;
        //PicのレコードIDをセッションで使う
        session(['pic_id' => $pic_id]);

        //user登録されたモデルを返す
        return $userdatamodel;
    }


    // ログイン後のリダイレクト先を記述
    public function redirectPath()
    {
        return '/top';
    }
}
