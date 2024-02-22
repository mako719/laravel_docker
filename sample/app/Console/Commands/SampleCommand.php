<?php

namespace App\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class SampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'スケジュールタスク動作確認用';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = CarbonImmutable::now()->toDateTimeString();

        file_put_contents('/tmp/sample.log', $now . PHP_EOL, FILE_APPEND);

        return 0;
    }
}
