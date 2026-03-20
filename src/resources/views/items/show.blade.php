@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('content')

<div class="item-detail">

    <div class="item-detail__container">

        {{-- 商品画像 --}}
        <div class="item-detail__image-area">

            @if($item->firstImage)
                <img
                    src="{{ $item->firstImage->image_url }}"
                    class="item-detail__image"
                    alt="{{ $item->name }}"
                >
            @else
                <div class="item-detail__image item-detail__image--placeholder">
                    商品画像
                </div>
            @endif


            {{-- SOLD --}}
            @if($item->purchase)
                <div class="item-detail__sold">
                    SOLD
                </div>
            @endif

        </div>



        {{-- ===============================
            商品情報
        =============================== --}}
        <div class="item-detail__info">

            {{-- 商品名 --}}
            <h1 class="item-detail__name">
                {{ $item->name }}
            </h1>

            {{-- ブランド --}}
            @if($item->brand_name)
                <div class="item-detail__brand">
                    {{ $item->brand_name }}
                </div>
            @endif

            {{-- 価格 --}}
            <div class="item-detail__price">
                ¥{{ number_format($item->price) }}
                <span class="item-detail__tax">(税込)</span>
            </div>



            {{-- ===============================
                いいね / コメント
            =============================== --}}
            <div class="item-detail__actions">

                {{-- いいね --}}
                <form method="POST" action="{{ route('likes.toggle', $item) }}">
                    @csrf

                    <button type="submit" class="item-detail__like-button" aria-label="いいね"
                        @if($item->is_sold) disabled @endif>

                        @if(auth()->check() && $item->isLikedBy(auth()->user()))
                            <img
                            src="{{ asset('images/heart_pink.png') }}"
                            class="item-detail__like-icon"
                            alt="いいね"
                            >
                        @else
                            <img src="{{ asset('images/heart_default.png') }}"
                                class="item-detail__like-icon"
                                alt="いいね"
                            >
                        @endif

                        <span>
                            {{ $item->likes_count }}
                        </span>

                    </button>

                </form>


                {{-- コメント数 --}}
                <div class="item-detail__comment-count">

                    <img
                        src="{{ asset('images/speech_bubble.png') }}"
                        class="item-detail__comment-icon"
                        alt="コメント"
                    >

                    <span>
                        {{ $item->comments_count }}
                    </span>

                </div>

            </div>

            {{-- ===============================
                購入ボタン
            =============================== --}}
            @if(!$item->is_sold)

                <a
                    href="{{ route('purchase.index', $item) }}"
                    class="item-detail__purchase-button"
                >
                    購入手続きへ
                </a>

            @else

                <div class="item-detail__sold-text">
                    売り切れました
                </div>

            @endif



            {{-- ===============================
                商品説明
            =============================== --}}
            <div class="item-detail__section">

                <h2 class="item-detail__section-title">
                    商品説明
                </h2>

                <p class="item-detail__description">
                    {{ $item->description }}
                </p>

            </div>



            {{-- ===============================
                商品情報
            =============================== --}}
            <div class="item-detail__section">

                <h2 class="item-detail__section-title">
                    商品の情報
                </h2>


                {{-- カテゴリー --}}
                <div class="item-detail__meta">

                    <span class="item-detail__meta-label">
                        カテゴリー
                    </span>

                    <div class="item-detail__categories">

                        @foreach($item->categories as $category)

                            <span class="item-detail__category">
                                {{ $category->name }}
                            </span>

                        @endforeach

                    </div>

                </div>


                {{-- 商品状態 --}}
                <div class="item-detail__meta">

                    <span class="item-detail__meta-label">
                        商品の状態
                    </span>

                    <span>
                        {{ $item->condition }}
                    </span>

                </div>

            </div>




            {{-- ===============================
                コメント
            =============================== --}}
            <div class="item-detail__section">

                <h2 class="item-detail__section-title">
                    コメント ({{ $item->comments_count }})
                </h2>


                {{-- コメント一覧 --}}
                <div class="item-detail__comments">

                    @forelse($item->comments as $comment)

                        <div class="item-detail__comment">

                            <div class="item-detail__comment-user">

                                <div class="item-detail__comment-user-icon">

                                    @if($comment->user->profile_image_url)
                                        <img
                                            src="{{ $comment->user->profile_image_url }}"
                                            class="item-detail__comment-user-image"
                                            alt="ユーザーアイコン"
                                        >
                                    @else
                                    <div class="item-detail__comment-user-placeholder"></div>
                                    @endif

                                </div>

                                <span class="item-detail__comment-user-name">
                                    {{ $comment->user->name }}
                                </span>

                            </div>

                            <div class="item-detail__comment-text">
                                {{ $comment->content }}
                            </div>

                        </div>

                    @empty

                        <p class="item-detail__no-comments">
                            まだコメントはありません
                        </p>

                    @endforelse

                </div>



                {{-- ===============================
                    コメント投稿
                =============================== --}}
                @if($item->is_sold)
                    {{-- Sold の場合は入力不可 --}}
                    <div class="item-detail__comment-form--disabled">
                        この商品は売り切れです。コメントはできません。
                    </div>
                @else

                <form
                    method="POST"
                    action="{{ route('comments.store', $item) }}"
                    class="item-detail__comment-form"
                >
                    @csrf

                    <label for="content" class="item-detail__comment-label">
                        商品へのコメント
                    </label>

                    <textarea
                        id="content"
                        name="content"
                        class="item-detail__comment-input"
                        maxlength="255"
                        @guest
                            placeholder="ログイン後にコメントできます"
                            readonly
                        @endguest
                    >{{ old('content') }}</textarea>

                    @error('content')
                    <p class="item-detail__error">
                        {{ $message }}
                    </p>
                    @enderror

                    <button
                        type="submit"
                        class="item-detail__comment-submit"
                        @guest disabled @endguest
                    >
                        コメントを送信する
                    </button>

                </form>
                @endif

            </div>

        </div>

    </div>

</div>

@endsection