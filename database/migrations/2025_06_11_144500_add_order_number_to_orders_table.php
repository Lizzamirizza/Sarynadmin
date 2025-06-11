<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->after('user_id')->unique();
        });

        // Generate the order_number for existing orders (optional, if there are any existing records)
        $orders = DB::table('orders')->get();
        foreach ($orders as $order) {
            $lastOrder = DB::table('orders')->orderBy('id', 'desc')->first();
            $lastOrderNumber = $lastOrder ? $lastOrder->order_number : 'ORDER-00000000';
            $nextOrderNumber = 'ORDER-' . str_pad((intval(substr($lastOrderNumber, 6)) + 1), 8, '0', STR_PAD_LEFT);

            DB::table('orders')
                ->where('id', $order->id)
                ->update(['order_number' => $nextOrderNumber]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
};
