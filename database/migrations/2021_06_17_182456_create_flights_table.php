<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('airline');
            $table->integer('flight_number');
            $table->string('departure_airport');
            $table->string('departure_time');
            $table->string('arrival_airport');
            $table->string('arrival_time');
            $table->string('duration');
            $table->string('price');
            $table->timestamps();

            $table->index(['airline', 'flight_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flights');
    }
}
