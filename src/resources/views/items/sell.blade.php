@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/sell.css') }}">
@endsection

@section('content')
<div class="sell">
    <h1 class="sell__title">{{ isset($item) ? '商品の編集' : '商品の出品' }}</h1>

    <form
        action="{{ isset($item) ? route('items.update', $item) : route('items.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="sell__form"
    >
        @csrf
        @if(isset($item))
            @method('PUT')
        @endif

        {{-- 商品画像 --}}
        <div class="sell__section">
            <label class="sell__label">商品画像</label>

            <input type="hidden" name="existing_image" id="existing-image-input"
                value="{{ old('existing_image', $item->image ?? '') }}">

            <div id="image-preview" class="sell__image-preview">
                @if($item?->image)
                    <img id="preview-img" class="sell__preview-img"
                        src="{{ asset('storage/' . $item->image) }}">
                @else
                    <img id="preview-img" class="sell__preview-img" style="display:none;">
                @endif

                <label for="image" class="sell__image-button" >画像を選択する</label>
            </div>

            <input type="file" name="image" id="image" class="sell__image-input" accept="image/jpeg,image/png">

            @error('image')
                <p class="sell__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 商品詳細 --}}
        <div class="sell__section">
            <h2 class="sell__section-title">商品詳細</h2>

            {{-- カテゴリー --}}
            <div class="sell__form-group">
                <span class="sell__label">カテゴリー</span>
                <div class="sell__categories">
                    @foreach ($categories as $category)
                        <label class="sell__category">
                            <input type="checkbox"
                                name="categories[]"
                                value="{{ $category->id }}"
                                class="sell__category-input"
                                {{ (isset($item) && $item->categories->contains($category->id)) || (is_array(old('categories')) && in_array($category->id, old('categories'))) ? 'checked' : '' }}>
                            <span class="sell__category-label">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('categories')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 商品状態 --}}
            <div class="sell__form-group">
                <label for="condition" class="sell__label">商品の状態</label>
                <select id="condition" name="condition" class="sell__select">
                    <option value="">選択してください</option>
                    @foreach(['新品・未使用','未使用に近い','目立った傷や汚れなし','やや傷や汚れあり','傷や汚れあり','全体的に状態が悪い'] as $state)
                        <option value="{{ $state }}"
                            {{ (isset($item) && $item->condition == $state) || old('condition') == $state ? 'selected' : '' }}>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>
                @error('condition')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- 商品名・説明・ブランド・価格 --}}
        <div class="sell__section">
            <h2 class="sell__section-title">商品名と説明</h2>

            <div class="sell__form-group">
                <label for="name" class="sell__label">商品名</label>
                <input type="text" name="name" class="sell__input"
                    value="{{ old('name', $item->name ?? '') }}">
                @error('name')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="sell__form-group">
                <label for="brand_name" class="sell__label">ブランド名</label>
                <input type="text" name="brand_name" class="sell__input"
                    value="{{ old('brand_name', $item->brand_name ?? '') }}">
            </div>

            <div class="sell__form-group">
                <label for="description" class="sell__label">商品の説明</label>
                <textarea name="description" class="sell__textarea">{{ old('description', $item->description ?? '') }}</textarea>
                @error('description')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>

            <div class="sell__form-group">
                <label for="price" class="sell__label">販売価格</label>
                <div class="sell__price">
                    <span class="sell__price-symbol">¥</span>
                    <input type="text" name="price" class="sell__input sell__input--price"
                        value="{{ old('price', $item->price ?? '') }}">
                </div>
                @error('price')
                    <p class="sell__error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- 出品ボタン --}}
        <div class="sell__submit">
            <button type="submit" class="sell__submit-button">
                {{ isset($item) ? '更新する' : '出品する' }}
            </button>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('image');
    const previewImg = document.getElementById('preview-img');
    const hiddenInput = document.getElementById('existing-image-input');
    const imageButton = document.querySelector('.sell__image-button');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        previewImg.src = URL.createObjectURL(file);
        previewImg.style.display = 'block';

        hiddenInput.value = '';
        imageButton.style.display = 'none';
    });
});
</script>
@endsection