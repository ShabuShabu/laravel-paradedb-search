<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->integer('max_members')->nullable();
            $table->jsonb('options')->nullable();
            $table->vector('embedding')->nullable();
            $table->timestamps();
        });
    }
};
