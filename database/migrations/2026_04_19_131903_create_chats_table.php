<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            // Ubah baris ini agar tidak otomatis mencari kolom 'id'
            $table->unsignedBigInteger('user_id'); 
            $table->text('message');
            $table->text('response');
            $table->timestamps();

            // Jika kolom di tabel user Anda namanya bukan 'id', sesuaikan di sini:
            // Contoh: references('id_user')->on('users')
                        $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
