<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('country_code', 255);
            $table->string('vat_number', 255);
            $table->date('request_date');
            $table->boolean('valid');
            $table->string('name', 255);
            $table->string('address', 255);
            $table->softDeletes();
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
        Schema::dropIfExists('vat');
    }
}
