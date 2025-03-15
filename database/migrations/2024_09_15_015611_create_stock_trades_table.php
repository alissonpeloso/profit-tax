<?php

use App\Enum\StockTradeClass;
use App\Enum\StockTradeOperation;
use App\Models\Broker;
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
            $table->enum('operation', StockTradeOperation::cases())->index();
            $table->enum('class', array_keys(StockTradeClass::cases()))->nullable()->index();
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
