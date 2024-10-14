<?php

use App\Models\Darf;
use App\Models\User;
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
        Schema::create('darfs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->date('due_date');
            $table->decimal('brazilian_stock_profit', 20, 2);
            $table->decimal('fii_profit', 20, 2);
            $table->decimal('bdr_and_etf_profit', 20, 2);
            $table->decimal('day_trade_profit', 20, 2);
            $table->foreignIdFor(User::class);
            $table->decimal('value', 20, 2);
            $table->enum('status', Darf::STATUSES);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('darfs');
    }
};
