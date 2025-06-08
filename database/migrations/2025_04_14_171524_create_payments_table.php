<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();  // ID order unik
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('amount', 15, 2); // nominal pembayaran, 15 digit total, 2 desimal
            $table->string('snap_token')->nullable();
            $table->string('status')->default('pending'); // pending, paid, failed, expired
            $table->timestamp('payment_date')->nullable(); // waktu pembayaran berhasil
            $table->string('midtrans_transaction_id')->nullable(); // ID transaksi dari Midtrans
            $table->string('midtrans_status')->nullable(); // status transaksi Midtrans (settlement, capture, etc)
            $table->timestamps();

            // Jika kamu punya relasi ke order:
            // $table->foreignId('order_id')->constrained()->onDelete('cascade');
            // Tapi karena kamu punya order_id string, mungkin relasi manual
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
