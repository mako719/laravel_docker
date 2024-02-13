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
　


# 8-3 バッチ処理の実装


# 不明点
- [ ] 

# 調べたこと

