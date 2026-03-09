@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
@endsection

@section('content')
<div class="profile">

    <h1 class="profile__title">プロフィール設定</h1>

    <form action="{{ route('mypage.profile.update') }}"
        method="POST"
        class="profile__form"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <!-- 画像 -->
        <div class="profile__image-section">

            <div class="profile__image-preview" id="imagePreview">

                @if($user->profile_image)
                    <img
                        src="{{ asset('storage/' . $user->profile_image) }}"
                        class="profile__image"
                        alt="プロフィール画像">
                @else
                    <div class="profile__image-placeholder"></div>
                @endif

            </div>

            <label for="image" class="profile__image-button">
                画像を選択する
            </label>

            <input
                type="file"
                id="image"
                name="profile_image"
                accept="image/jpeg,image/png"
                style="display:none;">

            @error('profile_image')
                <div class="profile__error">{{ $message }}</div>
            @enderror

        </div>

        <!-- 名前 -->
        <div class="profile__group">
            <label class="profile__label">ユーザー名</label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="profile__input">

            @error('name')
                <div class="profile__error">{{ $message }}</div>
            @enderror
        </div>

        <!-- 郵便番号 -->
        <div class="profile__group">
            <label class="profile__label">郵便番号</label>

            <input
                type="text"
                name="postal_code"
                value="{{ old('postal_code', $user->postal_code) }}"
                class="profile__input">

            @error('postal_code')
                <div class="profile__error">{{ $message }}</div>
            @enderror
        </div>

        <!-- 住所 -->
        <div class="profile__group">
            <label class="profile__label">住所</label>

            <input
                type="text"
                name="address"
                value="{{ old('address', $user->address) }}"
                class="profile__input">

            @error('address')
                <div class="profile__error">{{ $message }}</div>
            @enderror
        </div>

        <!-- 建物名 -->
        <div class="profile__group">
            <label class="profile__label">建物名</label>

            <input
                type="text"
                name="building_name"
                value="{{ old('building_name', $user->building_name) }}"
                class="profile__input">

            @error('building_name')
                <div class="profile__error">{{ $message }}</div>
            @enderror
        </div>

        <!-- ボタン -->
        <div class="profile__button-wrapper">
            <button type="submit" class="profile__button">
                更新する
            </button>
        </div>

    </form>
</div>


<!-- 画像プレビューJS（完成版） -->
<script>

document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');

    input.addEventListener('change', function(e) {

        const file = e.target.files[0];

        if (!file) return;

        const img = document.createElement('img');

        img.src = URL.createObjectURL(file);
        img.className = 'profile__image';

        preview.innerHTML = '';
        preview.appendChild(img);

    });

});

</script>

@endsection