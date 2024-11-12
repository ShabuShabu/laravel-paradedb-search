<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use JsonException;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;
use function Laravel\Prompts\warning;

class Help extends Command
{
    protected $signature = 'paradedb:help {--dry : Does not actually create a discussion}';

    protected $description = 'Opens a GitHub discussion for the ParadeDB team.';

    /**
     * @throws JsonException
     */
    public function __invoke(): int
    {
        $confirmed = confirm(
            label: 'This command will eventually create a new GitHub discussion for the ParadeDB team. Are you sure you want to continue?',
            default: false,
            yes: 'Yes, I need help',
            no: 'No, I am just looking around',
        );

        if (! $confirmed) {
            info('You have cancelled the request to create a new GitHub discussion!');

            return self::INVALID;
        }

        $subject = text(
            label: 'Subject',
            placeholder: 'What is the subject for this discussion?',
            required: 'A subject is required.'
        );

        $body = textarea(
            label: 'Body',
            placeholder: 'Describe the issue you are having in more detail!',
            required: 'A body is required.'
        );

        if ($this->option('dry')) {
            $result = DB::pretend(fn () => $this->runQuery($subject, $body));

            info('Here is the result of the dry-run:');

            warning(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        } else {
            $this->runQuery($subject, $body);

            info('A new GitHub discussion has been created! Go view it at: https://github.com/orgs/paradedb/discussions');
        }

        return self::SUCCESS;
    }

    protected function runQuery(string $subject, string $body): void
    {
        DB::select(
            (new \ShabuShabu\ParadeDB\Expressions\Help($subject, $body))->getValue(
                DB::connection()->getQueryGrammar()
            )
        );
    }
}
