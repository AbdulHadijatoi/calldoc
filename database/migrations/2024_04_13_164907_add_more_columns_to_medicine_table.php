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
        Schema::table('medicine', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->string('dci1')->nullable();
            $table->string('dosage1')->nullable();
            $table->string('unit_dosage1')->nullable();
            $table->string('shape')->nullable();
            $table->string('presentation')->nullable();
            $table->string('ppv')->nullable();
            $table->string('ph')->nullable();
            $table->string('price_br')->nullable();
            $table->string('princeps_generique')->nullable();
            $table->string('refund_rate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medicine', function (Blueprint $table) {
            //
        });
    }
};
