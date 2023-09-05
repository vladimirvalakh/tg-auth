<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable();
            $table->string('city')->nullable();
            $table->string('city2')->nullable();
            $table->string('address')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('email')->nullable();
            $table->string('email2')->nullable();
            $table->string('mail_domain')->nullable();
            $table->string('YmetricaId')->nullable();
            $table->text('VENYOOId')->nullable();
            $table->string('GMiframe1')->nullable();
            $table->string('GMiframe2')->nullable();
            $table->string('crm')->nullable();
            $table->string('crm_pass')->nullable();
            $table->text('crm_u')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
