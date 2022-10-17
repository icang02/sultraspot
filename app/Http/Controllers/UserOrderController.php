<?php

namespace App\Http\Controllers;

use App\Models\UserOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

use function GuzzleHttp\Promise\all;

class UserOrderController extends Controller
{
    public function index()
    {
        if (auth()->user()->role_id == 1) {
            $this->authorize('pengunjung');
        } else if (auth()->user()->role_id == 2) {
            $orders = UserOrder::with('tour_place')->where('user_id', auth()->user()->id)->get();
        } else if (auth()->user()->role_id == 3) {
            $orders = UserOrder::with('tour_place')->where('tour_place_id', auth()->user()->id)->get();
        }

        return Inertia::render('Dashboard/Pengunjung/Pesanan', [
            'orders' => $orders,
        ]);
    }

    public function show($id)
    {

        $order = UserOrder::with('tour_place')->find($id);
        $date = $order->created_at->format('Y-m-d H:i:s');

        return Inertia::render('Dashboard/Pengunjung/PesananShow', [
            'title' => 'Detail Pesanan',
            'order' => $order,
            'date_order' => $date,
        ]);
    }

    public function uploadBuktiTf(Request $request, $id)
    {
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $path = $request->file('image')->store('bukti-tf');

        UserOrder::find($id)->update([
            'image_tf' => $path,
        ]);

        return redirect()->route('pesanan');
    }

    public function orderConfirm($id)
    {
        // dd(request()->all());
        if (request()->status == 'selesai') $status = 'selesai';
        else if (request()->status == 'gagal') $status = 'gagal';

        $order = UserOrder::find($id);
        $order->update([
            'status' => $status,
        ]);

        return redirect()->route('pesanan');
    }

    public function delete($id)
    {
        // dd($id);
        $userOrder = UserOrder::find($id);
        $pathName = $userOrder->image_tf;
        // dd($pathName);

        Storage::delete($pathName);
        $userOrder->delete();

        return redirect()->route('pesanan');
    }
}
