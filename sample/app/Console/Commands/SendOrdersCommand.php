<?php

namespace App\Console\Commands;

use App\UseCases\SendOrdersUseCase;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class SendOrdersCommand extends Command
{
    private $useCase;
    private $logger;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-orders {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '購入情報を送信する';

    public function __construct(SendOrdersUseCase $useCase, LoggerInterface $logger)
    {
        parent::__construct();

        $this->useCase = $useCase;
        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // バッチ処理開始ログ
        $this->logger->info(__METHOD__ . '' . 'start');

        // 引数dateの値を取得する
        $date = $this->argument('date');
        // $dateの値からCarbonインスタンスを生成
        $targetDate = CarbonImmutable::createFromFormat('Ymd', $date);

        // バッチコマンド引数を出力
        $this->logger->info('TargetDate:' . $date);

        // ユースケースクラスに日付を渡す
        $count = $this->useCase->run($targetDate);

        // バッチ処理終了ログ
        $this->logger->info(__METHOD__ . '' . 'done sent_count:' . $count);

        return 0;
    }
}
