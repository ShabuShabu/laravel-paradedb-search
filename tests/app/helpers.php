<?php

declare(strict_types=1);

use ShabuShabu\ParadeDB\Indices\Bm25;

function create_teams_index(bool $drop = true): void
{
    Bm25::index('teams')
        ->addNumericFields(['max_members'])
        ->addBooleanFields(['is_vip'])
        ->addDateFields(['created_at', 'deleted_at'])
        ->addJsonFields(['options'])
        ->addRangeFields(['size'])
        ->addTextFields([
            'name',
            'description' => [
                'tokenizer' => [
                    'type' => 'default',
                ],
            ],
        ])
        ->create($drop);
}
