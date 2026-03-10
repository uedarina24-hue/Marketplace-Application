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
    //商品一覧
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $keyword = $request->query('keyword');

        if ($keyword) {
            session(['keyword' => $keyword]);
        }

        $query = Item::query()
            ->with(['firstImage', 'purchase'])
            ->withCount(['likes', 'comments'])
            ->latest();

        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        // マイリストタブ
        if ($tab === 'mylist') {

            if (!Auth::check()) {
                $items = collect();

                return view('items.index', [
                    'items' => $items,
                    'tab' => $tab,
                    'keyword' => session('keyword')
                ]);
            }

            $query->whereHas('likes', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $items = $query->get();

        return view('items.index', [
            'items' => $items,
            'tab' => $tab,
            'keyword' => $keyword ?? session('keyword')
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
        $item->likedUsers()->toggle(Auth::id());

        return back();
    }

    //コメント投稿
    public function commentStore(CommentRequest $request, Item $item)
    {
        $item->comments()->create([
            'user_id' => auth()->id(),
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

        if($item->purchase){
            return redirect()->route('items.index');
        }

        if(!$user->postal_code || !$user->address){
            return redirect()
                ->route('purchase.address.edit',$item)
                ->with('error','配送先を登録してください');
        }

        return redirect()->route('payment.checkout',[
            'item'=>$item,
            'payment_method'=>$request->payment_method
        ]);

    }

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


    //マイページ
    public function mypage(Request $request)
    {
        $user = Auth::user();

        $page = $request->query('page', 'sell');

        if ($page === 'sell') {

            $items = Item::with('firstImage')
                ->where('user_id', $user->id)
                ->latest()
                ->get();

        } else {

            $items = Item::with('firstImage')
                ->whereHas('purchase', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->latest()
                ->get();
        }

        return view('mypage.index', compact('items','page'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $isFirstProfileSetup = is_null($user->postal_code);

        if ($request->hasFile('profile_image')) {

            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')
                ->store('profiles', 'public');

            $user->profile_image = $path;
        }

        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->building_name = $request->building_name;

        $user->save();

        if ($isFirstProfileSetup) {
            return redirect()
                ->route('items.index');
        }

        return redirect()
            ->route('mypage');
    }


    //出品画面
    public function create()
    {
        $categories = Category::all();

        return view('items.sell', compact('categories'));
    }


    //出品保存
    public function store(ExhibitionRequest $request)
    {
        $item = Item::createWithRelations(
            $request->validated(),
            $request->file('image')
        );

        return redirect()->route('items.show', $item);
    }


}