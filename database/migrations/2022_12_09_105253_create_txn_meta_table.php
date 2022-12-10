<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('txn_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('txn_reference')->index();
            $table->foreignId('user_id')->index();
            $table->string('split_user_id')->index();
            $table->decimal('split_amount', 10 , 2)->unsigned();
            $table->timestamps();
            // $table->foreignId('txn_reference')->constrained('txn')->unique();
            // $table->foreignId('user_id')->constrained('txn')->index();
        });
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('txn_meta');
    }
};
