@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/index.css') }}">
@endsection

@section('content')

<div class="mypage">

    {{-- プロフィール --}}
    <div class="mypage__profile">

        <div class="mypage__profile-left">

            @if($user->profile_image)
                <img
                    src="{{ asset('storage/' . $user->profile_image) }}"
                    class="mypage__profile-image"
                    alt="{{ $user->name }}"
                >
            @else
                <div class="mypage__profile-image mypage__profile-image--placeholder"></div>
            @endif

            <div class="mypage__profile-name">
                {{ $user->name }}
            </div>

        </div>

        {{-- 編集ボタン --}}
        <div class="mypage__profile-right">

            <a
                href="{{ route('mypage.profile.edit') }}"
                class="mypage__edit-button"
            >
                プロフィールを編集
            </a>

        </div>

    </div>

    {{-- タブ --}}
    <nav class="mypage__tabs">

        <a
            href="{{ route('mypage', ['page'=>'sell']) }}"
            class="mypage__tab {{ $page !== 'buy' ? 'mypage__tab--active' : '' }}"
        >
            出品した商品
        </a>

        <a
            href="{{ route('mypage', ['page'=>'buy']) }}"
            class="mypage__tab {{ $page === 'buy' ? 'mypage__tab--active' : '' }}"
        >
            購入した商品
        </a>

    </nav>

    {{-- 商品一覧 --}}
    <div class="mypage__items">

        @forelse($items as $item)

            <a
                href="{{ route('items.show', $item) }}"
                class="mypage__item-card"
            >

                <div class="mypage__item-image-wrapper">

                    @if($item->firstImage)
                        <img
                            src="{{ asset('storage/' . $item->firstImage->image_path) }}"
                            class="mypage__item-image"
                            alt="{{ $item->name }}"
                        >
                    @else
                        <div class="mypage__item-image mypage__item-image--placeholder">
                            商品画像
                        </div>
                    @endif

                </div>

                <div class="mypage__item-name">
                    {{ $item->name }}
                </div>

            </a>

        @empty

            <p class="mypage__empty">
                商品がありません
            </p>

        @endforelse

    </div>

</div>

@endsection