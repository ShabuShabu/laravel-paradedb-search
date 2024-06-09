<?php

namespace ShabuShabu\ParadeDB\Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShabuShabu\ParadeDB\Tests\Database\Factories\TeamFactory;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'max_members' => 'integer',
        'is_vip' => 'boolean',
        'options' => 'json',
    ];

    protected static function newFactory(): TeamFactory
    {
        return new TeamFactory();
    }
}
