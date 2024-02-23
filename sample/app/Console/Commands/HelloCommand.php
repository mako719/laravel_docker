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
    // コマンド引数
    // protected $signature = 'hello:class {name}';
    // オプション引数
    protected $signature = 'hello:class {--switch}';

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
        // コマンド引数
        // $name = $this->argument('name');
        // $this->comment('Hello '.$name);
        // return 0;

        // オプション引数
        $switch = $this->option('switch');
        $this->comment('hello '. ($switch ? 'ON' : 'OFF'));
        return 0;
    }
}
