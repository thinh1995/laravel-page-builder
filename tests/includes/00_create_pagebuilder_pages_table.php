<?php

namespace Thinhnx\LaravelPageBuilder\Tests\includes;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagebuilderPagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('pagebuilder_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagebuilder_pages');
    }
}

;
