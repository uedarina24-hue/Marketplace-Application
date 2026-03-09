@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/sell.css') }}">
@endsection


@section('content')

<div class="sell">

    <h1 class="sell__title">
        商品の出品
    </h1>


    <form
        action="{{ route('items.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="sell__form"
    >
        @csrf


        <!-- 商品画像 -->
        <div class="sell__section">

            <label class="sell__label">
                商品画像
            </label>

            <div class="sell__image-upload">

                <!-- input -->
                <input
                    type="file"
                    name="image"
                    id="image"
                    class="sell__image-input"
                    accept="image/*"
                >

                <!-- プレビュー表示 -->
                <div
                    id="image-preview"
                    class="sell__image-preview"
                    style="display:none;"
                >
                    <img
                        id="preview-img"
                        class="sell__preview-img"
                        alt="preview"
                    >
                </div>

                <!-- 初期表示ボタン -->
                <label
                    for="image"
                    id="preview-text"
                    class="sell__image-button"
                >
                    画像を選択する
                </label>

                @error('image')
                    <div class="sell__error">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>



        <!-- 商品の詳細 -->
        <div class="sell__section">

            <h2 class="sell__section-title">
                商品の詳細
            </h2>

            <div class="sell__form-group">

                <label class="sell__label">
                    カテゴリー
                </label>

                <div class="sell__categories">

                    @foreach ($categories as $category)

                    <label>

                        <input
                            type="checkbox"
                            name="categories[]"
                            value="{{ $category->id }}"
                            class="sell__category-input"
                        >

                        <span class="sell__category-label">
                            {{ $category->name }}
                        </span>

                    </label>

                    @endforeach

                </div>

                @error('categories')
                    <div class="sell__error">
                        {{ $message }}
                    </div>
                @enderror

            </div>



            <div class="sell__form-group">

                <label class="sell__label">
                    商品の状態
                </label>

                <select
                    name="condition"
                    class="sell__select"
                >
                    <option value="">選択してください</option>
                    <option value="新品・未使用">新品・未使用</option>
                    <option value="未使用に近い">未使用に近い</option>
                    <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                    <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                    <option value="傷や汚れあり">傷や汚れあり</option>
                    <option value="全体的に状態が悪い">全体的に状態が悪い</option>
                </select>

                @error('condition')
                    <div class="sell__error">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>



        <!-- 商品名と説明 -->
        <div class="sell__section">

            <h2 class="sell__section-title">
                商品名と説明
            </h2>


            <div class="sell__form-group">

                <label class="sell__label">
                    商品名
                </label>

                <input
                    type="text"
                    name="name"
                    class="sell__input"
                    value="{{ old('name') }}"
                >

                @error('name')
                    <div class="sell__error">
                        {{ $message }}
                    </div>
                @enderror

            </div>



            <div class="sell__form-group">

                <label class="sell__label">
                    ブランド名
                </label>

                <input
                    type="text"
                    name="brand_name"
                    class="sell__input"
                    value="{{ old('brand_name') }}"
                >

            </div>



            <div class="sell__form-group">

                <label class="sell__label">
                    商品の説明
                </label>

                <textarea
                    name="description"
                    class="sell__textarea"
                >{{ old('description') }}</textarea>

                @error('description')
                    <div class="sell__error">
                        {{ $message }}
                    </div>
                @enderror

            </div>



            <div class="sell__form-group">

                <label class="sell__label">
                    販売価格
                </label>

                <div class="sell__price">

                    <span class="sell__price-symbol">
                        ¥
                    </span>

                    <input
                        type="text"
                        name="price"
                        class="sell__input sell__input--price"
                        value="{{ old('price') }}"
                    >

                </div>

                @error('price')
                    <div class="sell__error">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>



        <!-- 出品ボタン -->
        <div class="sell__submit">

            <button
                type="submit"
                class="sell__submit-button"
            >
                出品する
            </button>

        </div>


    </form>

</div>

@endsection



{{-- ========================================
    画像プレビューJS（blade内）
======================================== --}}
@section('js')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('image');
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const previewText = document.getElementById('preview-text');

    input.addEventListener('change', function(e) {

        const file = e.target.files[0];

        if (!file) return;

        const reader = new FileReader();

        reader.onload = function(e) {

            previewImg.src = e.target.result;

            preview.style.display = 'block';

            previewText.style.display = 'none';

        };

        reader.readAsDataURL(file);

    });

});

</script>

@endsection