<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VersionInfo extends Command
{
    protected $signature = 'paradedb:version';

    protected $description = 'Gets versioning info about your ParadeDB installation';

    public function __invoke(): int
    {
        $info = (array)DB::table(
            new \ShabuShabu\ParadeDB\Expressions\v1\VersionInfo()
        )->first();

        $this->table(
            ['Version', 'Git hash', 'Build mode'],
            [
                [$info['version'], $info['githash'], $info['build_mode']]
            ],
        );

        return self::SUCCESS;
    }
}
