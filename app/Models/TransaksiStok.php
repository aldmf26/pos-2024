<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiStok extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function produk()
    {
        return $this->belongsTo(Produk::class)
            ->join('pemiliks', 'pemiliks.id', '=', 'produks.pemilik_id')
            ->selectRaw('produks.*, pemiliks.pemilik as pemilik');
    }

    public static function getHistory($no_invoice = null, $jenis_transaksi = 'penjualan')
    {
        $query = TransaksiStok::selectRaw('jenis_transaksi,no_invoice,tanggal as tgl,admin,dijual_ke,keterangan as ket, SUM(ttl_rp) as ttl_rp, SUM(jumlah) as qty')
            ->groupBy('no_invoice')
            ->where('jenis_transaksi', $jenis_transaksi);

        if ($no_invoice) {
            $query->where('no_invoice', $no_invoice);
        }

        return $query->orderBy('no_invoice', 'desc')->get();

    }

}
