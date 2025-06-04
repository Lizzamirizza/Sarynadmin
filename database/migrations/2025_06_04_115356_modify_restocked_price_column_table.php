<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRestockedPriceColumnTable extends Migration
{
    public function up(): void
    {
        Schema::table('restockeds', function (Blueprint $table) {
            // Menambahkan kolom 'price' dengan tipe decimal yang sesuai dengan 'products'
            $table->decimal('price', 10, 2)->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('restockeds', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
}
