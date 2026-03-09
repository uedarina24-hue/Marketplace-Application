@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/index.css') }}">
@endsection

@section('content')
<div class="purchase">
    <div class="purchase__container">

    @if(session('error'))
        <div class="text-red-500 mb-4">
            {{ session('error') }}
        </div>
    @endif

        <!-- 左カラム -->
        <div class="purchase__main">

            <div class="purchase__item">
                @if($item->firstImage)
                    <img src="{{ asset('storage/' . $item->firstImage->image_path) }}"
                        class="purchase__item-image">
                @else
                    <div class="purchase__item-image">商品画像</div>
                @endif

                <div class="purchase__item-info">
                    <h2 class="purchase__item-name">{{ $item->name }}</h2>
                    <p class="purchase__item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <hr class="purchase__divider">

            <form id="purchase-form" method="POST"
                action="{{ route('purchase.store', $item) }}"
                class="purchase__form">
                @csrf

                <!-- 支払い方法 -->
                <div class="purchase__section">
                    <label class="purchase__label">支払い方法</label>

                    <select name="payment_method" id="payment_method" class="purchase__select">
                        <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>
                            選択してください
                        </option>
                        <option value="convenience" {{ old('payment_method') == 'convenience' ? 'selected' : '' }}>
                            コンビニ支払い
                        </option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>
                            カード支払い
                        </option>
                    </select>

                    @error('payment_method')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="purchase__divider">

                <!-- 配送先 -->
                <div class="purchase__section purchase__section--border">
                    <div class="purchase__address-header">
                        <h4 class="purchase__address-title">配送先</h4>
                        <a href="{{ route('purchase.address.edit', $item) }}"
                        class="purchase__address-edit">変更する</a>
                    </div>

                    <p class="purchase__address-text">
                        〒{{ $user->postal_code }}<br>
                        {{ $user->address }}<br>
                        {{ $user->building_name }}
                    </p>
                </div>
            </form>

        </div>

        <!-- 右カラム -->
        <div class="purchase__sidebar">

            <div class="purchase__summary">
                <div class="purchase__summary-row">
                    <span>商品代金</span>
                    <span class="purchase__summary-price">¥{{ number_format($item->price) }}</span>
                </div>

                <div class="purchase__summary-row">
                    <span>支払い方法</span>
                    <span id="method_label">未選択</span>
                </div>
            </div>

            <button type="submit" form="purchase-form" class="purchase__submit">
                購入する
            </button>

        </div>
    </div>
</div>

<script>
document.getElementById('payment_method').addEventListener('change', function() {
    document.getElementById('method_label').innerText =
        this.options[this.selectedIndex].text;
});

</script>

@endsection