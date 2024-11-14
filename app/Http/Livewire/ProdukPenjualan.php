<?php

namespace App\Http\Livewire;

use App\Models\Pemilik;
use Livewire\Component;
use App\Models\Produk;
use App\Models\Tag;

class ProdukPenjualan extends Component
{
    public $tags;
    public $search = '';
    public $produk;
    public $selectedTag = null;
    public $orderDetails = [];
    public function mount()
    {
        // Ambil tags dan produk awal
        $this->tags = Tag::get();
        $this->produk = Produk::getAllProduk($this->search, $this->selectedTag);
    }

    // Update produk saat search diubah
    public function updatedSearch()
    {
        $this->produk = Produk::getAllProduk($this->search, $this->selectedTag);
    }

    // Update produk saat tag dipilih
    public function updatedSelectedTag()
    {
        $this->produk = Produk::getAllProduk($this->search, $this->selectedTag);
    }

    public function addToOrder($produkId)
    {
        // Cari produk berdasarkan ID
        $produk = Produk::find($produkId);

        // Cek apakah produk sudah ada dalam keranjang
        if (isset($this->orderDetails[$produkId])) {
            // Jika sudah ada, periksa apakah jumlahnya masih dalam batas stok
            if ($this->orderDetails[$produkId]['quantity'] < $produk->stok) {
                $this->orderDetails[$produkId]['quantity']++; // Tambah jumlah jika ada
            } else {
                session()->flash('error', 'Jumlah produk melebihi stok.');
                return;
            }
        } else {
            // Jika produk belum ada dalam keranjang, tambahkan produk dengan jumlah 1
            $this->orderDetails[$produkId] = [
                'id' => $produkId,
                'name' => $produk->nama_produk,
                'price' => $produk->harga,
                'quantity' => 1, // Default quantity 1
                'image' => $produk->foto,
                'stok' => $produk->stok,
            ];
        }
    }
    
    public function removeFromOrder($produkId)
    {
        unset($this->orderDetails[$produkId]);
    }

    // Fungsi untuk menghitung total harga
    public function getTotalPrice()
    {
        $total = 0;
        foreach ($this->orderDetails as $order) {
            $total += $order['price'] * $order['quantity'];
        }
        return $total;
    }

    public function render()
    {
        // Pastikan produk terbaru diambil setelah ada perubahan pada search atau selectedTag
        $pemilik = Pemilik::orderBy('pemilik', 'asc')->get();
        return view('livewire.produk-penjualan', [
            'produk' => $this->produk,
            'tags' => $this->tags,
            'orderDetails' => $this->orderDetails,
            'totalPrice' => $this->getTotalPrice(),
            'pemilik' => $pemilik
        ]);
    }
}
