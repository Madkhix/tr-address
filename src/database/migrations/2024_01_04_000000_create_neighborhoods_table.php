<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up()
    {
        Schema::create('neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->foreignId('subdistrict_id')->nullable()->constrained('subdistricts')->onDelete('set null');
            $table->string('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('neighborhoods');
    }
}; 