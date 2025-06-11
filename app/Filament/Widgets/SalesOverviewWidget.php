<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format(
                Payment::where('status', 'settlement')->sum('amount'),
                0, ',', '.'
            ))->description('Akumulasi semua pembayaran sukses')->color('success'),

            Stat::make('Produk Terjual', OrderDetail::sum('quantity'))
                ->description('Total unit terjual sepanjang waktu')->color('info'),

            Stat::make('Total Order', Order::count())
                ->description('Semua order yang pernah masuk')->color('primary'),

            Stat::make('Pengiriman Berhasil', Shipment::where('status', 'delivered')->count())
                ->description('Pengiriman selesai ke pembeli')->color('success'),
        ];
    }
}
