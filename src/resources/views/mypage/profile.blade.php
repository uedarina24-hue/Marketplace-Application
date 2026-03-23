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
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')

        {{--  画像 --}}
        <div class="profile__image-section">

            <div class="profile__image-preview" id="imagePreview">

                @if($user->profile_image_url)
                    <img
                        src="{{ $user->profile_image_url }}"
                        class="profile__image"
                        alt="プロフィール画像"
                    >
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
                style="display:none;"
            >

            <input type="hidden" name="existing_profile_image" id="existing-image-input">

            @error('profile_image')
                <p class="profile__error">{{ $message }}</p>
            @enderror

        </div>

        {{--  名前 --}}
        <div class="profile__group">
            <label for="name" class="profile__label">ユーザー名</label>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="profile__input"
            >

            @error('name')
                <p class="profile__error">{{ $message }}</p>
            @enderror
        </div>

        {{--  郵便番号 --}}
        <div class="profile__group">
            <label class="profile__label">郵便番号</label>

            <input
                type="text"
                name="postal_code"
                value="{{ old('postal_code', $user->postal_code) }}"
                class="profile__input"
            >

            @error('postal_code')
                <p class="profile__error">{{ $message }}</p>
            @enderror
        </div>

        {{--  住所 --}}
        <div class="profile__group">
            <label class="profile__label">住所</label>

            <input
                type="text"
                name="address"
                value="{{ old('address', $user->address) }}"
                class="profile__input"
            >

            @error('address')
                <p class="profile__error">{{ $message }}</p>
            @enderror
        </div>

        {{--  建物名 --}}
        <div class="profile__group">
            <label class="profile__label">建物名</label>

            <input
                type="text"
                name="building_name"
                value="{{ old('building_name', $user->building_name) }}"
                class="profile__input"
            >

            @error('building_name')
                <p class="profile__error">{{ $message }}</p>
            @enderror
        </div>

        {{--  ボタン --}}
        <div class="profile__button-wrapper">
            <button type="submit" class="profile__button">
                更新する
            </button>
        </div>

    </form>
</div>


{{--  画像プレビューJS --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const fileInput = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const hiddenInput = document.getElementById('existing-image-input');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'profile__image';

        preview.innerHTML = '';
        preview.appendChild(img);

        hiddenInput.value = '';
    });

    document.querySelectorAll('.profile__existing-image').forEach(img => {
        img.addEventListener('click', function() {
            const path = img.dataset.path;

            hiddenInput.value = path;

            const previewImg = document.createElement('img');
            previewImg.src = '/storage/' + path;
            previewImg.className = 'profile__image';

            preview.innerHTML = '';
            preview.appendChild(previewImg);

            fileInput.value = '';
        });
    });

});

</script>

@endsection