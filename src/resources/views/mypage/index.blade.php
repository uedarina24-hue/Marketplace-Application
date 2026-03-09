@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/index.css') }}">
@endsection

@section('content')

<div class="mypage">

    <!-- =========================
    プロフィール上部
    ========================== -->
    <div class="mypage__profile">

        <!-- プロフィール画像 -->
        <div class="mypage__profile-left">

            @if(Auth::user()->profile_image)
                <img
                    src="{{ asset('storage/' . Auth::user()->profile_image) }}"
                    class="mypage__profile-image"
                >
            @else
                <div class="mypage__profile-image mypage__profile-image--placeholder"></div>
            @endif

            <div class="mypage__profile-name">
                {{ Auth::user()->name }}
            </div>

        </div>


        <!-- 編集ボタン -->
        <div class="mypage__profile-right">

            <a
                href="{{ route('mypage.profile.edit') }}"
                class="mypage__edit-button"
            >
                プロフィールを編集
            </a>

        </div>

    </div>


    <!-- =========================
    タブ
    ========================== -->

    <nav class="mypage__tabs">

        <a
            href="{{ route('mypage', ['page'=>'sell']) }}"
            class="mypage__tab {{ request('page') !== 'buy' ? 'mypage__tab--active' : '' }}"
        >
            出品した商品
        </a>

        <a
            href="{{ route('mypage', ['page'=>'buy']) }}"
            class="mypage__tab {{ request('page') === 'buy' ? 'mypage__tab--active' : '' }}"
        >
            購入した商品
        </a>

    </nav>



    <!-- =========================
    商品一覧
    ========================== -->

    <div class="mypage__items">

        @forelse($items as $item)

            <a
                href="{{ route('items.show', $item) }}"
                class="mypage__item-card"
            >

                <!-- 画像 -->
                <div class="mypage__item-image-wrapper">

                    @if($item->images->first())
                        <img
                            src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                            class="mypage__item-image"
                        >
                    @else
                        <div class="mypage__item-image mypage__item-image--placeholder">
                            商品画像
                        </div>
                    @endif

                </div>


                <!-- 商品名 -->
                <div class="mypage__item-name">
                    {{ $item->name }}
                </div>

            </a>

        @empty

            <div class="mypage__empty">
                商品がありません
            </div>

        @endforelse

    </div>

</div>

@endsection