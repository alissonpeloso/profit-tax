<?php

use App\Models\User;
use App\Models\Broker;
use App\Models\StockTrade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->index();
            $table->foreignIdFor(Broker::class)->index();
            $table->date('date');
            $table->string('stock_symbol')->index();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 20, 2);
            $table->decimal('fee', 20, 2)->default(0);
            $table->decimal('ir', 20, 2)->default(0);
            $table->string('note_id')->index();
            $table->enum('operation', array_keys(StockTrade::OPERATIONS));
            $table->enum('class', array_keys(StockTrade::CLASSES))->nullable();
            $table->boolean('is_day_trade')->default(false);
            $table->boolean('is_exempt')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_trades');
    }
};
