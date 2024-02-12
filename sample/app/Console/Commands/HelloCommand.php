<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HelloCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hello:class';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'サンプルコマンド（クラス）';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment('Hello class command');
        return 0;
    }
}
