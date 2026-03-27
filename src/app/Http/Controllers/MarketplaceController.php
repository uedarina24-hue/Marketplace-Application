<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class MarketplaceController extends Controller
{
    // 商品一覧
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');

        $query = Item::query()
            ->with(['firstImage', 'purchase'])
            ->withCount(['likes', 'comments'])
            ->search($keyword)
            ->latest();

        if ($tab === 'mylist') {

            if (!Auth::check()) {
                $items = collect();
            } else {
                $items = $query->likedBy(Auth::id())->get();
            }

        } else {

            $items = $query
                ->when(Auth::check(), fn($q) => $q->excludeOwn(Auth::id()))
                ->get();
        }

        return view('items.index', [
            'items' => $items,
            'tab' => $tab,
            'keyword' => $keyword
        ]);
    }


    //商品詳細
    public function show(Item $item)
    {
        $item->load([
            'firstImage',
            'categories',
            'purchase',
            'comments' => function ($query) {
                $query->latest()->with('user');
            }
        ])->loadCount([
            'likes',
            'comments'
        ]);

        return view('items.show', compact('item'));
    }

    //いいね切り替え
    public function toggleLike(Item $item)
    {
        if (!Auth::check()) {
            abort(403);
        }

        if ($item->user_id === Auth::id()) {
            abort(403);
        }

        $item->likedUsers()->toggle(Auth::id());

        return back();
    }

    //コメント投稿
    public function commentStore(CommentRequest $request, Item $item)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $item->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->validated()['content']
        ]);

        return back();
    }

    //購入画面
    public function purchaseIndex(Item $item)
    {
        $user = Auth::user();

        if($item->purchase){
            return redirect()->route('items.index');
        }

        return view('purchase.index',compact('item','user'));
    }

    public function purchaseStore(PurchaseRequest $request, Item $item)
    {
        $user = Auth::user();

        if ($item->purchase) {
            return redirect()->route('items.index');
        }


        if (!$user->postal_code || !$user->address) {
            return redirect()
                ->route('purchase.address.edit', $item)
                ->with('error','配送先を登録してください');
        }

        if (!$item->isPurchasableBy($user)) {
            return redirect()->route('items.index');
        }

        return redirect()->route('payment.checkout', [
            'item' => $item->id,
            'payment_method' => $request->payment_method
        ]);
    }

    //住所変更
    public function addressEdit(Item $item)
    {
        $user = Auth::user();
        return view('purchase.address', compact('item', 'user'));
    }

    public function addressUpdate(AddressRequest $request, Item $item)
    {
        $user = Auth::user();
        $user->update($request->only(['postal_code','address','building_name']));

        return redirect()->route('purchase.index', $item);
    }

    // マイページ
    public function mypage(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell');

        if ($page === 'sell') {

            $items = Item::with('firstImage')
                ->ownedBy($user->id)
                ->latest()
                ->get();

        } else {

            $items = Item::with('firstImage')
                ->purchasedBy($user->id)
                ->latest()
                ->get();
        }

        return view('mypage.index', compact('items','page','user'));
    }

    // マイページプロフィール編集
    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $isFirstProfileSetup = empty($user->postal_code);

        $data = $request->validated();

        if ($request->hasFile('profile_image')) {

            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $data['profile_image'] = $request->file('profile_image')
                ->store('profiles', 'public');
        }

        $user->update($data);

        if ($isFirstProfileSetup) {
            return redirect()->route('items.index');
        }

        return redirect()->route('mypage');
    }


    //出品画面
    public function create()
    {
        $categories = Category::all();
        $images = [];
        $item = null;
        return view('items.sell', compact('categories', 'images', 'item'));
    }


    //出品保存
    public function store(ExhibitionRequest $request)
    {
        $item = Item::createWithRelations(
            $request->validated(),
            $request->file('image') ?? $request->input('existing_image')
        );

        return redirect()->route('items.show', $item);
    }

}