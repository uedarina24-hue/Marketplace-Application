@extends('layouts.guest')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css')}}">
@endsection


@section('content')
    <div class="email-content">

        <p class="text-center">
            登録いただいたメールアドレスに<br>
            認証メールを送信しました。<br>
            メール認証を完了してください。
        </p>


        @if (config('app.mailhog_url'))
            <a href="{{ config('app.mailhog_url') }}"
                target="_blank"
                class="mail-button">
                    認証はこちらから
            </a>
        @endif


        <form class="form" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="resend-link-button">
                認証メールを再送する
            </button>
        </form>
    </div>
@endsection