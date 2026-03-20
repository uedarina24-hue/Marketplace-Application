@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css') }}">
@endsection

@section('content')
<div class="address-edit">

    <div class="address-edit__container">
        <h2 class="address-edit__title">住所の変更</h2>
        <form method="POST"
            action="{{ route('purchase.address.update', $item) }}"
            class="address-edit__form"
        >
            @csrf
            @method('PUT')

            <div class="address-edit__group">
                <label for="postal_code" class="address-edit__label">郵便番号</label>
                <input type="text" name="postal_code"
                    value="{{ old('postal_code', $user->postal_code) }}"
                    class="address-edit__input" id="postal_code">
                @error('postal_code')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="address-edit__group">
                <label for="address" class="address-edit__label">住所</label>
                <input type="text" name="address"
                    value="{{ old('address', $user->address) }}"
                    class="address-edit__input" id="address">
                @error('address')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="address-edit__group">
                <label for="building_name" class="address-edit__label">建物名</label>
                <input type="text" name="building_name"
                    value="{{ old('building_name', $user->building_name) }}"
                    class="address-edit__input" id="building_name">
            </div>

            <button class="address-edit__submit">更新する</button>
        </form>
    </div>

</div>
@endsection