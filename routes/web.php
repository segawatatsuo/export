<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::get('/', function () {
    return view('welcome');
});
*/

//use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route;

Route::get('/', 'WelcomeController@index')->name('welcome.index');

//サンプルトップ
Route::get('/view', 'ViewController@index')->name('view.index');

//ユーザー登録をした時に入力したメールアドレスを確認する機能（Email Verification）
Auth::routes(['verify' => true]);

//登録完了前のトップ画面
Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');

//登録完了後のトップ画面(登録ありがとうメッセージ)
Route::get('/top', 'HomeController@top')->name('top');


//カテゴリ別アイテム一覧 メール認証後でないと見られないように
//vendor/laravel/framework/src/Illuminate/Routing/Router.phpにrootは以下のように設定されている
//$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
//$this->post('login', 'Auth\LoginController@login');
//$this->post('logout', 'Auth\LoginController@logout')->name('logout');

Route::middleware(['verified'])->group(function () {
    Route::get('plan', 'ProductController@plan')->name('plan');

    Route::get('fedex', 'ProductController@fedex')->name('fedex');
    Route::get('air', 'ProductController@air')->name('air');
    Route::get('ship', 'ProductController@ship')->name('ship');

    Route::post('quotation', 'QuotationController@quotation')->name('quotation');
    Route::get('quotation', 'QuotationController@quotation')->name('quotation');

    //マイページから再度表示
    Route::get('quotation_repeat', 'QuotationController@quotation_repeat')->name('quotation_repeat');
    Route::get('invoice_repeat', 'InvoiceController@invoice_repeat')->name('invoice_repeat');

    Route::get('order_repeat', 'OrderController@order_repeat')->name('order_repeat');
    Route::get('order_confirm_repeat', 'OrderController@order_confirm_repeat')->name('order_confirm_repeat');






    Route::post('invoice', 'InvoiceController@invoice')->name('invoice');

    Route::post('order', 'OrderController@order')->name('order');
    Route::get('order', 'OrderController@order')->name('order');

    Route::post('order_confirm', 'OrderController@order_confirm')->name('order_confirm');
    Route::get('order_confirm', 'OrderController@order_confirm')->name('order_confirm');

    Route::post('order_confirm_repeat', 'OrderController@order_confirm_repeat')->name('order_confirm_repeat');

    Route::post('order_upload', 'OrderController@order_upload')->name('order_upload');
    Route::get('order_upload', 'OrderController@order_upload')->name('order_upload');

    Route::post('order_payment', 'OrderController@order_payment')->name('order_payment');
    Route::get('order_payment', 'OrderController@order_payment')->name('order_payment');

    Route::post('payment_upload', 'OrderController@payment_upload')->name('payment_upload');
    Route::get('payment_upload', 'OrderController@payment_upload')->name('payment_upload');

    //complete
    Route::post('order_complete', 'OrderController@order_complete')->name('order_complete');
    Route::get('order_complete', 'OrderController@order_complete')->name('order_complete');

    Route::post('thanks', 'OrderController@ThanksMail')->name('thanks_mail');

    Route::get('item', 'ItemController@index')->name('item');




    //MyPage
    Route::get('account', 'AccountController@index')->name('account.index');

    Route::get('account/order', 'AccountController@order')->name('account.order');
    Route::post('account/order', 'AccountController@order')->name('account.order');

    Route::get('account/order_each/{id}', 'AccountController@order_each')->name('account.order_each');
    //quotation　pdf再作成 generate_quotation_pdf
    Route::get('account/generate_quotation_pdf2', 'QuotationController@generate_quotation_pdf2')->name('generate_quotation_pdf2');

    Route::get('account/generate_invoice_pdf2', 'InvoiceController@generate_invoice_pdf2')->name('generate_invoice_pdf2');
    Route::get('account/ShowOrderSheet', 'OrderController@ShowOrderSheet')->name('ShowOrderSheet');
    Route::get('account/ShowPaymentSheet', 'OrderController@ShowPaymentSheet')->name('ShowPaymentSheet');

    //Route::get('account/packinglist/{order_number}', 'PackinglistController@index')->name('account.packinglist');
    Route::get('account/packinglist', 'PackinglistController@index')->name('account.packinglist');
    Route::get('account/balance', 'BalanceController@index')->name('account.balance');

    Route::get('account/payment_uploader', 'OrderController@payment_uploader')->name('payment_uploader');
    Route::get('account/payment_up', 'OrderController@payment_up')->name('payment_up');
    Route::post('account/payment_up', 'OrderController@payment_up')->name('payment_up');

    Route::get('account/address', 'AccountController@address')->name('account.address');
    Route::post('account/address', 'AccountController@address')->name('account.address');
    Route::post('account/edit', 'AccountController@edit')->name('account.edit');

    Route::get('account/edit', 'AccountController@edit')->name('account.edit');
    Route::post('account/edit', 'AccountController@edit')->name('account.edit');

    Route::get('account/update', 'AccountController@update')->name('account.update');
    Route::post('account/update', 'AccountController@update')->name('account.update');

    Route::get('account/quotation', 'AccountController@quotation')->name('account.quotation');
    Route::post('account/quotation', 'AccountController@quotation')->name('account.quotation');

    Route::get('account/invoice', 'AccountController@invoice')->name('account.invoice');
    Route::post('account/invoice', 'AccountController@invoice')->name('account.invoice');


    Route::get('account/img_store', 'AccountController@img_store')->name('account.img_store');
    Route::post('account/img_store', 'AccountController@img_store')->name('account.img_store');

    Route::get('account/consignee', 'AccountController@consignee')->name('account.consignee');
    Route::get('account/importer', 'AccountController@importer')->name('account.importer');
    Route::get('account/add', 'AccountController@add')->name('account.add');
    Route::post('account/add_store', 'AccountController@add_store')->name('account.add_store');
    Route::get('account/change', 'AccountController@change')->name('account.change');
    Route::post('account/change_update', 'AccountController@change_update')->name('account.change_update');

    Route::post('account/importer_update', 'AccountController@importer_update')->name('account.importer_update');
});

//住所未登録ユーザーの場合に入力フォームへ移動
Route::get('entryform', 'UserinformationController@entryform');
Route::post('entryform', 'UserinformationController@entryform');

//住所未登録ユーザーの住所を登録
Route::get('entry', 'UserinformationController@entry');
Route::post('entry', 'UserinformationController@entry');


//インボイスの住所2回目入力
Route::post('invoice_entry', 'UserinformationController@invoice_entry');

//住所2をDBに入れてからインボイス画面へ移動
Route::post('invoice_entry_and_go', 'UserinformationController@invoice_entry_and_go');

//インボイスが初めてかどうか確認
Route::post('invoice_confirm', 'UserinformationController@invoice_confirm');
Route::get('invoice_confirm', 'UserinformationController@invoice_confirm');


//見積書PDFの出力
Route::post('generate_quotation_pdf', 'QuotationController@generate_quotation_pdf');

//インボイスPDFの出力
Route::post('generate_invoice_pdf', 'InvoiceController@generate_invoice_pdf');

Route::get('tcpdf', 'DocumentController@tcpdf');

//注文書PDFの出力
Route::get('purchase', 'PurchaseController@index')->name('purchase.index');


//packinglist
//Route::get('packinglist', 'PackinglistController@index')->name('packinglist.index');

//個別商品
Route::get('item/{id}', 'ItemController@index')->name('item');
