<?php

namespace App\Http\Controllers;

use App\Models\PengelolaOrder;
use App\Models\UserOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;


class UserOrderController extends Controller
{
    public function index()
    {
        if (auth()->user()->role_id == 1) {
            $this->authorize('pengunjung');
        } else if (auth()->user()->role_id == 2) {
            $orders = UserOrder::with('tour_place')->where('user_id', auth()->user()->id)->orderBy('no_order')->get();
        } else if (auth()->user()->role_id == 3) {
            $orders = PengelolaOrder::with('tour_place')->where('tour_place_id', auth()->user()->id)->orderBy('no_order')->get();
        }

        return Inertia::render('Dashboard/Pengunjung/Pesanan', [
            'orders' => $orders,
        ]);
    }

    public function show($id)
    {
        if (auth()->user()->role_id == 2) {
            $order = UserOrder::with('tour_place', 'user')->find($id);
            $date = $order->created_at->format('Y-m-d H:i:s');
        } else if (auth()->user()->role_id == 3) {
            $order = PengelolaOrder::with('tour_place', 'user')->find($id);
            $date = $order->created_at->format('Y-m-d H:i:s');
        }

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

        $imgUrlPengunjung = cloudinary()->upload($request->file('image')->getRealPath(), [
            'folder' => 'kwu-ganjil/bukti-tf/pengunjung'
        ])->getSecurePath();
        UserOrder::find($id)->update([
            'image_tf' => $imgUrlPengunjung,
            'image_tf_public_id' => cloudinary()->getPublicId($imgUrlPengunjung),
        ]);

        $imgUrlPengelola = cloudinary()->upload($request->file('image')->getRealPath(), [
            'folder' => 'kwu-ganjil/bukti-tf/pengelola'
        ])->getSecurePath();
        PengelolaOrder::find($id)->update([
            'image_tf' => $imgUrlPengelola,
            'image_tf_public_id' => cloudinary()->getPublicId($imgUrlPengelola),
        ]);

        return redirect()->route('pesanan');
    }

    public function orderConfirm($id)
    {
        // dd(request()->all());
        if (request()->status == 'selesai') $status = 'selesai';
        else if (request()->status == 'gagal') $status = 'gagal';

        $userOrder = UserOrder::find($id);
        $pengelolaOrder = PengelolaOrder::find($id);

        $userOrder->update([
            'status' => $status,
        ]);
        $pengelolaOrder->update([
            'status' => $status,
        ]);

        return redirect()->route('pesanan');
    }

    public function delete($id)
    {

        $userOrder = UserOrder::find($id);
        $pengelolaOrder = PengelolaOrder::find($id);

        if (auth()->user()->role_id == 2) {
            // $imgName = $userOrder->image_tf;
            // unlink('bukti-tf/pengunjung/' . $imgName);
            cloudinary()->destroy($userOrder->image_tf_public_id);
            $userOrder->delete();
        } else {
            // $imgName = $pengelolaOrder->image_tf;
            // unlink('bukti-tf/pengelola/' . $imgName);
            cloudinary()->destroy($pengelolaOrder->image_tf_public_id);
            $pengelolaOrder->delete();
        }

        return redirect()->route('pesanan');
    }
}
