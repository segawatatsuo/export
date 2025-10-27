<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImpersonateController extends Controller
{
/*
    public function login(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect('/login')->with('error', '無効なアクセスです');
        }

            // トークンをハッシュ化して検証
    $hashedToken = hash('sha256', $token);

        $record = DB::table('impersonate_tokens')
            ->where('token', $hashedToken)
            ->where('expires_at', '>', Carbon::now())
            ->whereNull('used_at') // 未使用のトークンのみ
            ->first();

        if (!$record) {
            abort(403, 'このURLは無効または期限切れです');
        }

        // 顧客としてログイン
        Auth::loginUsingId($record->user_id);

        // セッションに「管理者モード」情報を保存
        session(['impersonated_by_admin' => true]);

        // 一度使ったトークンは削除
        DB::table('impersonate_tokens')->where('token', $token)->delete();

        return redirect('/account');
    }
*/

public function login(Request $request)
{

    $token = $request->query('token');
    
    if (!$token) {
        return redirect('/login')->with('error', '無効なアクセスです');
    }
    
    // トークンをハッシュ化して検証
    $hashedToken = hash('sha256', $token);
    
    $impersonateToken = DB::table('impersonate_tokens')
        ->where('token', $hashedToken)
        ->where('expires_at', '>', now())
        ->whereNull('used_at') // 未使用のトークンのみ
        ->first();
    
    if (!$impersonateToken) {
        return redirect('/login')->with('error', 'トークンが無効または期限切れです');
    }
    
    // トークンを使用済みにマーク
    DB::table('impersonate_tokens')
        ->where('token', $hashedToken)
        ->update(['used_at' => now()]);

    // ユーザーとしてログイン
    Auth::loginUsingId($impersonateToken->user_id);
    
    // セッションに管理者なりすましフラグを設定
    session([
        'impersonating' => true,
        'impersonated_at' => now(),
        'impersonate_token_id' => $impersonateToken->id ?? null
    ]);
    
    return redirect('/account');
}

}