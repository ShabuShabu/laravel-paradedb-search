<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Tests\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShabuShabu\ParadeDB\Tests\Database\Factories\UserFactory;

class User extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory(): UserFactory
    {
        return new UserFactory;
    }
}
