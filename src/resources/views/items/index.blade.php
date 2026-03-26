@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('content')

<div class="items">

    <h1 class="visually-hidden">商品一覧</h1>

    {{-- タブ --}}
    <nav class="items__tabs">
        <a
            href="{{ route('items.index', ['keyword' => $keyword]) }}"
            class="items__tab {{ request('tab') !== 'mylist' ? 'items__tab--active' : '' }}"
        >
            おすすめ
        </a>

        <a
            href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => $keyword]) }}"
            class="items__tab {{ request('tab') === 'mylist' ? 'items__tab--active' : '' }}"
        >
            マイリスト
        </a>
    </nav>

    {{-- 商品一覧 --}}
    <ul class="items__grid">
        @forelse($items as $item)

            <li class="item-card">
                <a
                    href="{{ route('items.show', $item) }}"
                    class="item-card__link"
                >
                    <div class="item-card__image-wrapper">

                        @if($item->firstImage?->image_url)
                            <img
                                src="{{ $item->firstImage->image_url }}"
                                class="item-card__image"
                                alt="{{ $item->name }}"
                            >
                        @else
                            <div class="item-card__image item-card__image--dummy">
                                商品画像
                            </div>
                        @endif

                        @if($item->is_sold)
                            <span class="item-card__sold">
                                Sold
                            </span>
                        @endif

                    </div>

                    <div class="item-card__name">
                        {{ $item->name }}
                    </div>

                </a>
            </li>

        @empty
            <li class="items__empty">
                商品が見つかりませんでした
            </li>
        @endforelse
    </ul>

</div>
@endsection