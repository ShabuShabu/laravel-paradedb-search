<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ShabuShabu\ParadeDB\Expressions\v1;

use function Laravel\Prompts\table;

class VersionInfo extends Command
{
    protected $signature = 'paradedb:version';

    protected $description = 'Gets versioning info about your ParadeDB installation';

    public function __invoke(): int
    {
        $info = DB::table(new v1\Inspection\VersionInfo)->value('version_info');

        [$version, $type] = explode(',', trim($info, '()'));

        table(
            headers: ['Version', 'Build mode'],
            rows: [
                [$version, $type],
            ],
        );

        return self::SUCCESS;
    }
}
