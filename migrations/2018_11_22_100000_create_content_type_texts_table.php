<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentTypeTextsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('content_type_texts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('linkable');
            $table->string('locale', 5);
            $table->string('group', 75)->nullable();
            $table->string('key', 75)->nullable();
            $table->longText('value')->nullable();
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
        Schema::dropIfExists('content_type_texts');
    }
}
