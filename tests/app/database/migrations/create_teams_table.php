<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use ShabuShabu\ParadeDB\Indices\Bm25;
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
            $table->vector('embedding', 3)->nullable();
            $table->timestamps();

            $table->index('embedding vector_cosine_ops')->algorithm('hnsw');
        });

        Bm25::index('teams')
            ->addNumericFields(['max_members'])
            ->addBooleanFields(['is_vip'])
            ->addDateFields(['created_at'])
            ->addJsonFields(['options'])
            ->addTextFields([
                'name',
                'description' => [
                    'tokenizer' => [
                        'type' => 'default',
                    ],
                ],
            ])
            ->create(drop: true);
    }
};
