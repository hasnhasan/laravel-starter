<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function(Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 250)->nullable()->index('slug');
            $table->string('namespace', 250)->nullable()->index('namespace');
            $table->integer('parameter')->nullable();
            $table->string('title', 250)->nullable();
            $table->string('description', 1200)->nullable();
            $table->string('keywords', 250)->nullable();
            $table->string('filters', 1750)->nullable();
            $table->tinyInteger('robots')->nullable()->default(0);

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routes');
    }
}
