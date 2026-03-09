<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        // ログアウト後はログイン画面へリダイレクト
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {

            public function toResponse($request)
            {
                return redirect()->route('login');
            }

        });

        // 登録後はメール認証画面へリダイレクト
        $this->app->instance(RegisterResponseContract::class, new class implements RegisterResponseContract {
            public function toResponse($request)
            {
                return redirect()->route('verification.notice');
            }
        });

        // ログイン後はメール認証完了していなければメール認証案内へ、完了していれば商品一覧へリダイレクト
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $user = $request->user();

                // intended を無効化
                $request->session()->forget('url.intended');

                if (! $user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }

                return redirect()->route('items.index');
            }
        });
    }


    public function boot(): void
    {

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::registerView(function () {return view('auth.register');});
        Fortify::loginView(function () {return view('auth.login');});
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        Fortify::authenticateUsing(function (Request $request) {

            $user = Auth::getProvider()->retrieveByCredentials(
            $request->only('email')
            );
            if (! $user || ! Auth::validate($request->only('email', 'password'))) {
                throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
                ]);
            }

        return $user;
        });

        $this->app->bind(\Laravel\Fortify\Http\Requests\LoginRequest::class,
        \App\Http\Requests\LoginRequest::class);

    }
}
