<?php

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
            $table->date('date');
            $table->string('stock_code');
            $table->integer('quantity');
            $table->decimal('price', 20, 2);
            $table->decimal('fee', 20, 2);
            $table->decimal('ir', 20, 2);
            $table->string('note_id')->index();
            $table->enum('operation', ['buy', 'sell']);
            $table->foreignIdFor(Broker::class)->index();
            $table->foreignIdFor(User::class)->index();
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
