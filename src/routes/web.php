<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PaymentController;


/*
|--------------------------------------------------------------------------
| メール認証
|--------------------------------------------------------------------------
*/

// 認証メール案内
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {

    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');

})->middleware(['auth','throttle:6,1'])->name('verification.send');


// メール認証処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {

    $request->fulfill();

    $user = $request->user();

    // 初回プロフィール未登録ならプロフィールへ
    if (is_null($user->postal_code)) {
        return redirect()->route('mypage.profile');
    }

    // 登録済みなら商品一覧
    return redirect()->route('items.index');

})->middleware(['auth','signed'])->name('verification.verify');



/*
|--------------------------------------------------------------------------
| 未ログインでも閲覧可能
|--------------------------------------------------------------------------
*/

Route::get('/', [MarketplaceController::class,'index'])
    ->name('items.index');

Route::get('/item/{item}', [MarketplaceController::class,'show'])
    ->name('items.show');



/*
|--------------------------------------------------------------------------
| ログイン必須
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // コメント
    Route::post('/comments/{item}', [MarketplaceController::class,'commentStore'])
        ->name('comments.store');

    // いいね
    Route::post('/likes/{item}', [MarketplaceController::class,'toggleLike'])
        ->name('likes.toggle');

    // 出品
    Route::get('/sell', [MarketplaceController::class,'create'])
        ->name('items.create');

    Route::post('/sell', [MarketplaceController::class,'store'])
        ->name('items.store');

});



/*
|--------------------------------------------------------------------------
| メール認証済みユーザー
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','verified'])->group(function () {

    //マイページ
    Route::get('/mypage', [MarketplaceController::class,'mypage'])
        ->name('mypage');

    Route::get('/mypage/profile', [MarketplaceController::class,'edit'])
        ->name('mypage.profile');

    Route::put('/mypage/profile', [MarketplaceController::class,'update'])
        ->name('mypage.profile.update');


    //購入
    Route::get('/purchase/{item}', [MarketplaceController::class,'purchaseIndex'])
        ->name('purchase.index');

    Route::post('/purchase/{item}', [MarketplaceController::class,'purchaseStore'])
        ->name('purchase.store');


    //住所変更
    Route::get('/purchase/address/{item}', [MarketplaceController::class,'addressEdit'])
        ->name('purchase.address.edit');

    Route::put('/purchase/address/{item}', [MarketplaceController::class,'addressUpdate'])
        ->name('purchase.address.update');


    //stripe決済
    Route::get('/payment/checkout/{item}', [PaymentController::class,'checkout'])
        ->name('payment.checkout');

    Route::get('/payment/success/{item}', [PaymentController::class,'success'])
        ->name('payment.success');

});
