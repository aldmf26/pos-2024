<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiStokController;

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::controller(ProdukController::class)
        ->prefix('produk')
        ->name('produk.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'create')->name('create');
            Route::get('/daftar_rak', 'daftar_rak')->name('daftar_rak');

            Route::get('/satuan', 'satuan')->name('satuan');
            Route::get('/create_satuan', 'create_satuan')->name('create_satuan');

            Route::get('/rak', 'rak')->name('rak');
            Route::get('/create_rak', 'create_rak')->name('create_rak');

            Route::get('/pemilik', 'pemilik')->name('pemilik');
            Route::get('/create_pemilik', 'create_pemilik')->name('create_pemilik');
        });

    Route::controller(TransaksiStokController::class)
        ->prefix('produk/transaksi')
        ->name('transaksi.')
        ->group(function () {
            Route::get('/', 'index')->name('penjualan');
            Route::post('/', 'save_pembayaran')->name('save_pembayaran');
            Route::get('/history', 'history')->name('history.penjualan');
            Route::get('/void', 'void')->name('void.penjualan');
            Route::get('/detail', 'detail_penjualan')->name('detail.penjualan');
            Route::get('/print', 'print_penjualan')->name('print.penjualan');
            
            Route::get('/stok_masuk', 'stok_masuk')->name('stok_masuk');
            Route::get('/history_stok_masuk', 'history_stok_masuk')->name('history.stok_masuk');

            Route::get('/opname', 'opname')->name('opname');
            Route::post('/opname', 'save_opname')->name('save_opname');
            Route::get('/history_opname', 'history_opname')->name('history.opname');
            Route::get('/print_opname', 'print_opname')->name('print.opname');
            Route::get('/void_opname', 'void_opname')->name('void.opname');


        });
});
