<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cookie;
use App\Models\Product;
use Illuminate\Http\Response;
use Log;

class CartController extends Controller
{
    const COOKIE_NAME = "StoreCart";

    public function addToCart(Request $request)
    {
        $cart = json_decode(Cookie::get(self::COOKIE_NAME),true);
        if($cart==null) $cart = [];

        $itemCount = (isset($cart[$request->id]) ? $cart[$request->id] : null);

        if($itemCount==null)
        {
            $itemCount = $request->numberOf;
        }
        else{
            $itemCount += $request->numberOf;
        }
        $cart[$request->id] = $itemCount;
        Cookie::queue(Cookie::forever(self::COOKIE_NAME, json_encode($cart)));

        return redirect($request->url())->with("addedToCart", "addedToCart");
    }

    public function removeFromCart(Request $request)
    {
        $cart = json_decode(Cookie::get(self::COOKIE_NAME),true);
        unset($cart[$request->id]);
        Cookie::queue(Cookie::forever(self::COOKIE_NAME, json_encode($cart)));
        return redirect('/cart');
    }

    public function deleteCart() //testam
    {
        $resp = new Response("Cart izdzēsts");
        $resp->withCookie(Cookie::forget(self::COOKIE_NAME));
        return $resp;
    }

    public function indexCart()
    {
        $cart = json_decode(Cookie::get(self::COOKIE_NAME),true);
        $items = [];
        if($cart == null) return view('cart', compact('items'));

        $totalPrice = 0.0;

        foreach($cart as $id => $itemCount)
        {
            $product = Product::firstWhere('id', '=', $id);
            if($product!= null && $product->active) {
                $product->{'count'} = $itemCount;
                array_push($items, $product);
                $totalPrice += $product->price * $itemCount;
            }
        }

        return view('cart', compact('items', 'totalPrice'));
    }

    public function succeedOrder()
    {
        return response(view("orderSuccess"))->withCookie(Cookie::forget(self::COOKIE_NAME));
    }
}
