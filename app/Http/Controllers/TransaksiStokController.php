<?php

namespace App\Http\Controllers;

use App\Models\Pemilik;
use App\Models\Produk;
use App\Models\Tag;
use App\Models\TransaksiStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiStokController extends Controller
{
    public function index()
    {
        $tags = Tag::get();
        $produk =  Produk::getAllProduk();

        $data = [
            'title' => 'Transaksi Stok',
            'tags' => $tags,
            'produk' => $produk
        ];
        return view('transaksi_stok.penjualan.index', $data);
    }

    public function save_pembayaran(Request $r)
    {
        try {
            DB::beginTransaction();

            $orderDetails = $r->orderDetails;
            $totalPrice = $r->totalPrice;
            $keterangan = $r->keterangan;
            $dijual_ke = $r->dijual_ke;

            $admin = auth()->user()->name;
            $urutan = 1001 + TransaksiStok::where('jenis_transaksi', 'penjualan')->count();
            $no_invoice = 'K-' . $urutan;

            for ($i = 0; $i < count($r->id_produk); $i++) {
                $id_produk = $r->id_produk[$i];
                $qty = $r->qty[$i];
                $price = $r->price[$i];
                $produk = Produk::find($id_produk);
                $stok = $produk->stok - $qty;

                $pemilik = Pemilik::find($produk->pemilik_id);
                $ttl_rp = $pemilik->pemilik == $dijual_ke ? 0 : $qty * $price;

                TransaksiStok::create([
                    'produk_id' => $produk->id,
                    'jenis_transaksi' => 'penjualan',
                    'urutan' => $urutan,
                    'no_invoice' => $no_invoice,
                    'jumlah' => $qty,
                    'stok_sebelum' => $produk->stok,
                    'stok_setelah' => $stok,
                    'keterangan' => $keterangan,
                    'dijual_ke' => $dijual_ke,
                    'ttl_rp' => $ttl_rp,
                    'tanggal' => now(),
                    'admin' => $admin
                ]);
                $produk->update(['stok' => $stok]);
            }

            DB::commit();
            return redirect()->route('transaksi.history', ['no_invoice' => $no_invoice])->with('sukses', 'Data Berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function history(Request $r)
    {
        $datas = TransaksiStok::getHistory();
        $data = [
            'title' => 'History Penjualan',
            'datas' => $datas
        ];
        return view('transaksi_stok.penjualan.history', $data);
    }

    public function dataDetail($no_invoice)
    {
        $no_invoice = $no_invoice;
        $datas = TransaksiStok::with('produk')->where('no_invoice', $no_invoice)->get();
        $totalPrice = $datas->sum(fn($item) => $item->produk->harga * $item->jumlah);
        $totalQty = $datas->sum(fn($item) => $item->jumlah);

        $data = [
            'title' => 'Detail Penjualan',
            'datas' => $datas,
            'no_invoice' => $no_invoice,
            'totalPrice' => $totalPrice,
            'totalQty' => $totalQty,
        ];
        return $data;
    }

    public function detail_penjualan(Request $r)
    {
        $data = $this->dataDetail($r->no_invoice);
        return view('transaksi_stok.penjualan.detail', $data);
    }
    public function print_penjualan(Request $r)
    {
        $data = $this->dataDetail($r->no_invoice);

        return view('transaksi_stok.penjualan.print', $data);
    }

    public function stok_masuk()
    {
        $data = [
            'title' => 'Stok Masuk'
        ];
        return view('transaksi_stok.stok_masuk.index', $data);
    }
    public function opname()
    {
        $produk =  Produk::getAllProduk();

        $data = [
            'title' => 'Opname Stok',
            'produk' => $produk
        ];
        return view('transaksi_stok.opname.index', $data);
    }

    public function save_opname(Request $r)
    {
        try {
            DB::beginTransaction();
            $urutan = 1001 + TransaksiStok::where('jenis_transaksi', 'opname')->count(); // urutan opname otomatis
            $no_invoice = 'O-' . $urutan;
            $admin = auth()->user()->name;
            for ($i = 0; $i < count($r->id_produk); $i++) {
                $id_produk = $r->id_produk[$i];
                $produk = Produk::find($id_produk);

                $stok_sistem = $produk->stok;
                $stok_fisik = $r->stok_fisik[$i];
                $selisih = $r->selisih[$i];
                $keterangan = $r->keterangan[$i];
                
                if ($selisih != 0) {
                    TransaksiStok::create([
                        'produk_id' => $produk->id,
                        'jenis_transaksi' => 'opname',
                        'urutan' => $urutan,
                        'no_invoice' => $no_invoice,
                        'jumlah' => $stok_fisik, // stok baru hasil opname
                        'stok_sebelum' => $stok_sistem,
                        'stok_setelah' => $stok_fisik,
                        'keterangan' => $keterangan,
                        'tanggal' => now(),
                        'admin' => $admin
                    ]);

                    $produk->update(['stok' => $stok_fisik]);
                }
            }
            DB::commit();
            return redirect()->route('transaksi.opname')->with('sukses', 'Data Berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
