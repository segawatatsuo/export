<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Model\User;
use App\Model\Userinformation;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        //Home(index)はログイン後の表示なのでユーザー情報を持っている
        $user_id = Auth::id();
        $user_name = User::find($user_id)->name;
        $ui = Userinformation::where('user_id', $user_id)->get();
        if (count($ui)>0) {
            $country_codes = $ui[0]['country_codes'];
            session()->put('country_codes', $country_codes);
        }
        //顧客であるフラフを立てる（管理画面用)
        $user = User::find($user_id);

        $user->group = "user";
        $user->save();

        return view('home', compact('user_name'));
    }

    public function top()
    {
        return view('top');
    }
}
