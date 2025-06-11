<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('province')->nullable()->after('address');  // Menambahkan kolom province setelah address
            $table->string('city')->nullable()->after('province');     // Menambahkan kolom city setelah province
            $table->string('subcity')->nullable()->after('city');     // Menambahkan kolom subcity setelah city
            $table->string('postalcode')->nullable()->after('subcity'); // Menambahkan kolom postalcode setelah subcity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['province', 'city', 'subcity', 'postalcode']); // Menghapus kolom province, city, subcity, postalcode
        });
    }
};
