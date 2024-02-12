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
クラスに記述する内容例。  
handleメソッドに処理を記述する。
```
// コマンド名を指定
protected $signature = 'hello:class';
// コマンドの説明を指定
protected $description = 'サンプルコマンド（クラス）';
```

# 8-2 Commandの実装


# 8-3 バッチ処理の実装


# 不明点
- [ ] 

# 調べたこと

