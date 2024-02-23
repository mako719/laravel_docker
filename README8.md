# CHAPTER8 コンソールアプリケーション
8-1 Commandの基礎  
8-2 Commandの実装  
8-3 バッチ処理の実装  

# 8-1 Commandの基礎
　Webアプリケーションでは、HTTPリクエストをトリガーに動作する処理が主だが、コマンドラインから実行するコマンドや定期的に実行するバッチ処理が必要になる。  
Commandを実装するにはクロージャで実装する方法とCommandクラスを実装する方法がある。

route/console.phpに以下のようにクロージャで実装できる。  
return 0;は省略できる。
```
Artisan::command('hello:closure', function () {
    $this->comment('hello closure command');  // 文字列出力
    return 0;                                 // 正常終了なら0を返す
})->describe('サンプルコマンド（クロージャ）');    // コマンド説明
```
コマンドを実行するには以下の通り
```
$ php artisan hello:closure
```

クラスによるCommand作成  
 make:commandで雛形を作成する。
```
$ php artisan make:command HelloCommand
```
commandクラスに記述する内容例。  
handleメソッドに処理を記述する。
```
// コマンド名を指定
protected $signature = 'hello:class';
// コマンドの説明を指定
protected $description = 'サンプルコマンド（クラス）';
```

Commandへの入力  
　コマンドラインで実行する場合、任意の引数を受け取ることが可能である（コマンド引数）。  
その場合、$signatureプロパティに受け取る可能性がある引数を指定する。
```
protected $signature = 'hello:class {name}';
protected $signature = 'hello:class {name?}';               // 引数省略可能
protected $signature = 'hello:class {name=default}';        // デフォルト値指定
protected $signature = 'hello:class {name*}';               // 引数を配列として取得
protected $signature = 'hello:class {name:description}';    // 説明を追加できる

// 引数はhandle()内で以下のように取得
$name = $this->argument('name');
```

　オプション引数を指定することもできる。  
オプション引数とは、スイッチのように指定した項目を有効にする場合などに利用する。  
コマンド引数同様、$signatureプロパティに指定する。
```
protected $signature = 'hello:class {--switch}';
protected $signature = 'hello:class {--switch=}';              // 引数を文字列として取得
protected $signature = 'hello:class {--switch=default}';       // 引数デフォルト値
protected $signature = 'hello:class {--switch=*}';             // 引数を配列として取得
protected $signature = 'hello:class {--switch:description}';   // 説明
protected $signature = 'hello:class {--S|--switch}';           // ショートカットオプション追加可能

// 引数はhandle()内で以下のように取得
$switch = $this->option('switch');
```

Commandからの出力  
　Commandからコマンドラインに出力するためのメソッドがいくつか用意されている。  
出力スタイルがいくつか存在する。
```
info($string, $verbosity = null)     // infoスタイル（文字色：緑）
comment($string, $verbosity = null)  // commentスタイル（文字色：黄色）

// $verbosityには以下のような出力レベルを指定する
VERBOSITY_QUIET // 常に出力
VERBOSITY_DEBUG // -vvvのみで出力（php artisan output -vvv）
```

Commandの実行  
　コマンドラインでCommandを叩いて実行するだけではなく、Laravelアプリケーション内部から直接Commandを実行する方法は以下の通り。

```
// Artisanファサードで実行
Route::get('/with_args', function () {
    Artisan::call('with-args-command', [
        'name' => 'Johann',
        '--switch' => true
    ]);
})

// Illuminate\Contracts\Console\Kernelをインジェクトする方法
Route::get('/no_args_di, function (Kernel $artisan) {
    $artisan->call('no-args-command');
})
```

# 8-2 Commandの実装
　今回の実装において、ユースケースとサービスクラスの分離を実現している。  
詳しくは、ユースケースクラスExportOrdersUseCaseにて実処理を、ExportOrdersServiceクラスでデータベースから値を読み取る処理を担っている。  

　処理ごとのクラスを分離する狙いは、各クラスの役割を明確にし、それぞれ定められた役割のみを担うようにするためである。  
各々の役割が明確で独立したクラスは再利用性が高まるので、別の箇所からの利用が容易になる。  
　また、独立したクラスでは、テストコードの記述も容易になる。  
Laravelではコンソールアプリケーションをテストできるが、ユースケースクラスはその仕組みを使わなくても単体でテスト可能となる（コンソールアプリケーションに限らず）。

# 8-3 バッチ処理の実装
　バッチ処理はcronなどで自動起動し、バックグラウンドで実行される処理を指す。

cronの設定  
・コンソールでcrontab -eでインサートモードにする  
・登録するバッチ実行のコマンドを入力する  
・escキーを押しインサートモードを終了し、:wqと入力して保存しつつvimを終了する

　バッチ処理は自動でバックグラウンド実行するため、処理結果をリアルタイムで確認できない。  
そのため、ログに実行状況を出力することが重要。  
webアプリケーションとは別のバッチ専用ログファイルにログ出力し、実行の可否、実行結果や発生したエラーなどをあとで確認できる。  

出力内容の例  
・バッチ処理開始時  
・バッチ処理終了時  
　→エラーが発生していなくても正常に実行されていないケースもあるので、処理件数も表示させるとよい  
・外部システムとの通信時  
　→バッチが正常に実行されても、外部システムの障害が原因で異常終了する可能性もあるため

スケジュールタスクによるバッチ処理実行  
　cronでは定時実行するコマンドや頻度をサーバにSSHなどでログインして設定する必要がある。  
こうした設定をLaravelアプリケーションのコードで記述可能にしたのがスケジュールタスクという機能。  
　利点としては、アプリケーションをデプロイすることで実行するタスクの設定が可能になる。  
また、アプリケーションと同じGitリポジトリで設定を管理できる。  
　タスクの実行はApp\Console\Kernelクラスのscheduleメソッドで指定する。

```
// 下記コマンドで設定タスクの確認ができる
$ php artisan schedule:list

// 下記コマンドで設定したタスクをコマンドラインで起動できる
$ php artisan schedule:test

// scheduleメソッド内では、call()、exec()で以下のことができる。
// call() callable型で実行コードを指定できる
// exec() コマンドラインで実行可能なコマンドを文字列で指定できる
```

　分、時、日、曜日、月、年などの頻度を指定できるメソッドが用意されているが、cronメソッドでcrontabと同じ形式で実行頻度を文字列で指定できる。
```
// 毎分実行の場合
$schedule->command(HelloCommand::class)->cron('* * * * * ');

// 午前1:00に実行
$schedule->command(HelloCommand::class)->cron('0 1 * * * ');
```

なぜ対象日を引数にするのか  
php artisan app:send-orders 20240215 のように、引数を日付にする理由  
　通信経路や通信先システムの障害、不具合などによって正常に処理が実行されない場合がある。  
その時にコマンドラインから手動で実行ができるため。（イレギュラー対応を想定している）

# 不明点
- [x] 8-2で使用したコマンド  
- [x] Guzzleとは  
- [x] cronとは  

# 調べたこと
8-2で使用したコマンド  
・ls ディレクトリやファイルの情報を一覧表示する  
・ls -la -lオプションと-aオプションを一度に指定している。-lはファイルの詳細情報を表示、-aはドットで始まるファイルも含めて表示する。  
・cat ファイルの中身を見るために使う。今回は「cat /tmp/orders.tsv」でorders.tsvファイルの中にある情報を出力している。

Guzzleとは  
　HTTPクライアントライブラリ。  
POSTリクエストやJSONデータアップロードなどいろいろなことができる。  
並行処理ができ、速度を短縮できる。
　
cronとは  
　UNIX系OSに入っているプログラムで、時間指定したプログラムを実行してくれるもの。  
指示する時は、crontabというコマンドを使う。
