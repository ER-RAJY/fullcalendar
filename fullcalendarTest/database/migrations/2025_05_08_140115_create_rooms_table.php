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
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->unsignedBigInteger('type_room_id');
        $table->foreign('type_room_id')->references('id')->on('type_rooms')->onDelete('cascade');
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
        Schema::dropIfExists('rooms');
    }
};
