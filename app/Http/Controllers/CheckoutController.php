<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\PengelolaOrder;
use App\Models\TourPlace;
use App\Models\UserOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index($id_cart)
    {
        // dd($id);
        return Inertia::render('Dashboard/Pengunjung/CheckoutPage', [
            'title' => 'Checkout',
            'item' => Cart::with('tour_place')->find($id_cart),
        ]);
    }

    public function prosesCheckout($id_cart)
    {
        $cart = Cart::find($id_cart);

        if (UserOrder::all()->count() == 0) {
            $no_order = 1;
        } else {
            $no_order = UserOrder::select("id")->orderBy("id", "DESC")->get()->first()->id + 1;
        }

        UserOrder::create([
            'no_order' => 'SP' . sprintf("%07d", $no_order),
            'user_id' => auth()->user()->id,
            'tour_place_id' => $cart->tour_place_id,
            'quantity' => $cart->quantity,
            'status' => 'pending',
            'total_payment' => $cart->total_payment,
        ]);
        PengelolaOrder::create([
            'no_order' => 'SP' . sprintf("%07d", $no_order),
            'user_id' => auth()->user()->id,
            'tour_place_id' => $cart->tour_place_id,
            'quantity' => $cart->quantity,
            'status' => 'pending',
            'total_payment' => $cart->total_payment,
        ]);

        Cart::destroy($id_cart);

        return Inertia::render('Dashboard/Pengunjung/CheckoutSuccess');
    }

    public function orderNowShow(Request $request, $id_place)
    {
        $place = TourPlace::where('id', $id_place)->get()->first();
        $data = [
            'qty' => $request->qty,
            'price_kamera' => null,
            'total_payment' => null,
        ];

        if ($request->sewaKamera == true) {
            $data['price_kamera'] = 50000;
            $data['total_payment'] = ($place->price * $request->qty) + $data['price_kamera'];
            $data['sewa_kamera'] = true;
        } else {
            $data['price_kamera'] = 0;
            $data['total_payment'] = ($place->price * $request->qty);
            $data['sewa_kamera'] = false;
        }

        return Inertia::render('Dashboard/Pengunjung/CheckoutNow', [
            'title' => 'Checkout',
            'place' => $place,
            'data' => $data,
        ]);
    }

    public function orderNowStore(Request $request)
    {
        if ($request->rental == true) {
            $totalPayment = $request->total_payment + $request->price_kamera;
        } else {
            $totalPayment = $request->total_payment;
        }

        if (UserOrder::all()->count() == 0) {
            $no_order = 1;
        } else {
            $no_order = UserOrder::select("id")->orderBy("id", "DESC")->get()->first()->id + 1;
        }

        UserOrder::create([
            'no_order' => 'SP' . sprintf("%07d", $no_order),
            'user_id' => auth()->user()->id,
            'tour_place_id' => $request->tour_place_id,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'total_payment' => $totalPayment,
        ]);
        PengelolaOrder::create([
            'no_order' => 'SP' . sprintf("%07d", $no_order),
            'user_id' => auth()->user()->id,
            'tour_place_id' => $request->tour_place_id,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'total_payment' => $totalPayment,
        ]);

        return Inertia::render('Dashboard/Pengunjung/CheckoutSuccess');
    }
}
