<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Message;
use Illuminate\Support\Facades\File;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text("content")->nullable();
            $table->text("image")->nullable();
            $table->dateTime("posted");
            $table->unsignedBigInteger("parent");
            $table->integer("author")->nullable();

            $table->foreign('parent')->references('id')->on('conversations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $imgs = Message::select('image')->whereNotNull('image')->get();
        foreach($imgs as $img) {
            $path = public_path('uploads/').$img;
            if(File::exists($path))
                File::delete($path);
        }

        Schema::dropIfExists('messages');
    }
}
