<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentTypeStringsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('content_type_strings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('linkable');
            $table->string('locale', 5);
            $table->string('group', 75)->nullable();
            $table->string('key', 75)->nullable();
            $table->string('value')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['linkable_type', 'linkable_id', 'locale', 'group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('content_type_strings');
    }
}
