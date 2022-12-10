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
        Schema::create('txn', function (Blueprint $table) {
            $table->id();
            $table->string('txn_reference')->unique();
            $table->foreignId('user_id')->index();
            $table->decimal('amount', 10 , 2)->unsigned();
            $table->json('txn_details');
            $table->enum('expense' , ['EQUAL','EXACT','PERCENT']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('txn');
    }
};
