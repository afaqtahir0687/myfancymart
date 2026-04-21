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
        Schema::table('products', function (Blueprint $table) {
            $table->date('discount_start_date')->nullable()->after('discount');
            $table->date('discount_end_date')->nullable()->after('discount_start_date');
            $table->boolean('discount_is_active')->default(true)->after('discount_end_date');
            $table->text('discount_note')->nullable()->after('discount_is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'discount_start_date',
                'discount_end_date', 
                'discount_is_active',
                'discount_note'
            ]);
        });
    }
};
