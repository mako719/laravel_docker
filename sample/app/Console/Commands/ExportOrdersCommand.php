<?php

namespace App\Console\Commands;

use App\UseCases\ExportOrdersUseCase;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class ExportOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-orders {date} {--output=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '購入情報を出力する';

    protected $useCase;

    public function __construct(ExportOrdersUseCase $useCase)
    {
        parent::__construct();

        $this->useCase = $useCase;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 引数dateの値を取得する
        $date = $this->argument('date');
        // $dateの値からCarbonImmutableインスタンスを生成
        $targetDate = CarbonImmutable::createFromFormat('Ymd', $date);

        // UseCaseクラスに日付を渡す
        $tsv = $this->useCase->run($targetDate);

        // outputオプションの値を取得
        $outputFilePath = $this->option('output');
        // nullであれば未指定なので、標準出力に出力
        if (is_null($outputFilePath)) {
            echo $tsv;
            return 0;
        }

        // ファイルに出力
        file_put_contents($outputFilePath, $tsv);

        return 0;
    }
}
