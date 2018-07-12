<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('type');
            $table->integer('et')->nullable()->default(5);
            $table->integer('importance')->nullable()->default(1);
            $table->integer('urgency')->nullable()->default(1);
            $table->datetime('deadline')->nullable()->default(Carbon::now());
            $table->string('camp')->nullable();
            $table->string('status')->nullable()->default('open');
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
        Schema::dropIfExists('tasks');
    }
}