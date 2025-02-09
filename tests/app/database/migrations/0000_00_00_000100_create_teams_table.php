<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

use function ShabuShabu\ParadeDB\text_config;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->integer('max_members')->nullable();
            $table->jsonb('options')->nullable();
            $table->integerRange('size')->nullable();
            $table->vector('embedding', 3)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('embedding vector_cosine_ops')->algorithm('hnsw');
            $table->index(['id', 'name', 'description', 'is_vip', 'max_members', 'options', 'size', 'user_id', 'created_at', 'deleted_at'], 'teams_bm25_idx')
                ->algorithm('bm25')
                ->with([
                    'key_field' => 'id',
                    'text_fields' => text_config([
                        'description' => [
                            'stored' => true,
                        ],
                    ]),
                ]);
        });
    }
};
