# CHAPTER2 Laravelのアーキテクチャ
2-1 ライフサイクル  
2-2 サービスコンテナ  
2-3 サービスプロバイダ  
2-4 コントラクト

# 2-1 ライフサイクル
Laravelアプリケーション実行の流れ

①エントリポイント⇄②HTTPカーネル⇄③ルータ⇄④ミドルウェア⇄⑤コントローラ、クロージャ

エントリポイント  
Laravelアプリケーションの起点となり、public/index.phpがそれに相当する。  
内容  
1. オートローダーの読み込み
2. フレームワークの起動  
bootstrap/app.phpが読み込まれることで実行され、結果としてIlluminate\Foundation\Applicationのインスタンス（サービスコンテナ）が返される
3. アプリケーションの実行およびHTTPレスポンスの送信  
生成されたサービスコンテナを利用してHTTPカーネルを生成し、handleメソッドを実行することでアプリケーションが実行される
4. 終了処理

HTTPカーネル  
内容  
1. ルータを実行
2. 例外発生時処理
3. イベント発火  
これに対するリス名を設定すればこの時点で呼び出される

# 2-2 サービスコンテナ
サービスコンテナとは  
アプリケーション内の複数のクラスが同じ機能を利用する場合、利用するクラスに応じてインスタンスが生成される。  
また、設定値など初期処理を必要とするものもあ離、必要なクラスが個別に処理を実行するのは効率が悪い。  
そのため、サービスコンテナに対しインスタンスを呼び出すだけでいい。  
また、DIなどもサービスコンテナが担う。  
簡単にまとめると、サービスコンテナは「依存注入」と「インスタンスか方法のカスタマイズ」ができる。

バインドと解決  
インスタンスの生成方法を登録する処理は「バインド」、指定されたインスタンスをサービスコンテナが生成して返すことを「解決する」という。  
バインドと解決の例  
Numberクラスのインスタンスが返される。
```
// バインド
app()->bind(Number::class, function () {
    return new Number();
});

// 解決
$numcles = app()->make(Number::class)
```

バインド  
バインドの定義場所はServiceProviderクラスを作成して定義する。  
また、バインドにはいくつかの方法がある。
1. bindメソッド  
最もよく使用される
2. bindifメソッド  
同名のバインドが存在しない場合のみバインド処理を行う
3. singletonメソッド  
インスタンスを1つ飲みにする場合に使用する
4. instanceメソッド  
既に生成したインスタンスをサービスコンテナにバインドする。
5. whenメソッド

解決  
解決の方法は2通りある。  
バインドしていなくても、具象クラスであれば解決できる。
1. makeメソッド
```
$number = app()->make(Number::class);
```
2. appヘルパ関数
```
$number = app(Number::class);
```

ファサード  
クラスメソッド形式でフレームワークの機能を簡単に利用できるもので、裏側ではサービスコンテナの機能が使われている。  
config/app.phpのaliasキーの定義に従って、クラスに対して別名をつけるclass_alias関数で実現されている。  

例
```
$debug = \Config::get('app.debug');
```
実はConfigクラスがあるのではなく、config/app.phpのaliasキーに定義されている
```
'config' => Illuminate\Support\Facades\Config::class,
```
のこと。

ファサードが動く仕組み（例）  
1. Config::get('app.debug')がコールされる
2. Configの実体であるIlluminate\Support\Facades\Configクラスのgetメソッドを呼び出す
3. Illuminate\Support\Facades\Configクラスにはgetメソッドがないため、スーパークラスの__callStaticメソッドを呼び出す
4. __callStaticメソッドでは、getFacaderootメソッドで操作対象のインスタンスを取得し、getメソッドを実行する。

# 2-3 サーヒスプロバイダ
サービスコンテナのバインド処理を記述する時などに利用する機能。  
Laravelのライフサイクルではビジネスロジックが実行される前にサービスプロバイダのメソッドが呼ばれる。  
ミドルウェアなどもあるが、サービスプロバイダはフレームワークやアプリケーションに含まれる機能の初期勝利を行う目的で用意されている。

サービスプロバイダの役割  
1. サービスコンテナへのバインド
2. イベントリスナーやミドルウェア、ルーティングの登録
3. 外部コンポーネントを組み込む

サービスプロバイダはconfig/app.php内の'prividers'に定義する。  
Laravelの初期処理でサービスプロバイダのregisterメソッド→bootメソッドの順で呼び出される。
registerメソッドは実装必須で、バインド処理をかく。

# 2-4 コントラクト
コントラクトの基本  
コントラクト=Laravelのコアコンポーネントで利用されている関数を"インターフェース"として定義したもの。  
機能を簡単に差し替えることができるLaravelフレームワークにおいて、疎結合であるために役割を果たしているのがコントラクト。  

疎結合とは  
疎結合は、各コンポーネントが他のコンポーネントの具体的な実装や動作に依存しない状態のこと。  
コントラクトのように、インターフェースや抽象クラスを使用して依存関係を管理する。  
これにより、あるコンポーネントの実装が変更されても、他のコンポーネントに影響を与えることなく、独立して動作できます。





# 不明点
[ ]　メソッドインジェクション、コンストラクタインジェクションの利点、メリット
[ ]　DeferrableProviderの使い方について
[x]　コントラクトの使い所、メリット

# 調べたこと
コントラクトの使い所、メリット  
独自コンポーネントを作成する場合、コントラクトを実装すればフレームワークが要求するメソッドを実装できる。  
コントラクトに依存させることで、同じコントラクトを実装したクラスへと柔軟に差し替え可能で、サービスコンテナで解決するので、サービスプロバイダでバインドを変更すれば、フレームワーク全体でその機能を利用できる。
