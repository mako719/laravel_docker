# CHAPTER6 認証と認可
6-1 セッションを利用した認証  
6-2 トークン認証  
6-3 JWT認証  
6-4 OAuthクライアントによる認証・認可  
6-5 認可処理

# 6-1 セッションを利用した認証
　認証とは、アプリケーションの機能を提供するため、利用するユーザーを照合して確認する処理。  
LaravelにはBasic認証、デー他ベースを利用した認証、認証機能を利用したログイン・ログアウト、パスワードリマインド、パスワードリセットなどがある。  
　だが、必ずしも開発要件に合致するとは限らず、拡張が必要になるケースがしばしばある。

認証処理の流れ  
Illuminate\Contracts\Auth\Factory  
　認証処理を決定づけるためのインターフェース。  
デフォルトではEloquentを用いたデータベースでのユーザー認証とセッションを用いて認証情報を保持する仕組みが用意されている。

Illuminate\Contracts\Auth\Guard  
　認証情報の操作を提供するインターフェース。  
資格情報の検証やログインユーザーへのアクセス、ユーザーの識別情報へのアクセス方法などが提供されている。  
UserProviderを元に認証情報を取得する。

Illuminate\Contracts\Auth\UserProvider  
　認証機能の実装を提供するインターフェース。

　Laravelでは上記のインターフェースを実装したクラスが提供されているので、要件に応じて対応できる。

　UserProviderインターフェースに記述されているメソッドは以下のようなものがある

・retrieveById()  
・retrieveByToken()  
・updateRememberToken()  
・retrieveByCredentials()  
・validateCredentials()  

　また、上記メソッドはIlluminate\Contracts\Auth\Authenticatableインターフェースを実装したクラスを引数や戻り値として取る。  
このインターフェースには以下のメソッドがある。

・getAuthIdentifierName()  
・getAuthIdentifier()  
・getAuthPassword()  
・getRemamberToken()  
・gsetRememberToken()  
・getRememberTokenName()  

　これら2つのインターフェースを組み合わせて認証機能を実装する。

データベース・セッションによる認証処理  
　Laravelには標準のUsersテーブルとセッション（Illuminate\Auth\SessionGuardクラス）を組み合わせた認証機能が提供されている。  
config/auth.phpによって組み合わせが指定される。  
「guards」はguardドライバ指定時に利用され、「providers」は認証情報のアクセス方法（eloquentなど）が記述される。

認証処理で使用される代表的なメソッド  
・attemptメソッド　ユーザー情報を利用してログインを行うメソッド（CHAPTER1のログイン処理でも使用）  
・userメソッド　Authファサードなどから認証ユーザー情報にアクセスする場合に利用

　ただ、いちいちDBにアクセスするので、アプリケーションの規模によってはパフォーマンスが気になるため認証処理の検討が必要になる。

laravel/breezeパッケージ  
　Laravelで提供されている認証機能の雛形を利用したい場合は、laravel/breezeパッケージを利用する必要がある。  
インストールコマンドを実行することで、クラスが追加されルートが置き換わる。

代表的には以下が用意されている  
・ユーザー登録処理→App\Http\Controllers\Auth\RegisteredUserController  
・ログイン・ログアウト処理→App\Http\Controllers\Auth\AuthenticatedSessionController  
・デフォルト動作のカスタマイズ→App\Http\Controllers\Auth\AuthenticatedSessionControllerのstoreメソッド、App\Http\Requests\Auth\LoginRequestクラスを変更する

　今回はデータベースアクセスのパフォーマンスの改善のため、キャッシュ機能組み込みの実装をした。（EloquentUserProviderクラスを拡張したCacheUserProviderクラス）  
ユーザーIDを使ってキャッシュを作成し、それが破棄されるまでキャッシュからユーザー情報を取得する。  
キャッシュ削除までDBへのアクセスが発生しないため、パフォーマンスの問題は発生しにくい。

パスワード認証  
　Illumiante\Auth\EloquentUserProviderクラスにあるretrieveByCredentialsメソッドが担っているが、オーバーライドすることで認証処理時に利用する条件を変更することができる。  

パスワードリセット  
　標準で用意されているpassword_resetsテーブルと要件が異なるなどがあったら、Illuminate\Auth\Password\DatabaseTokenRepositoryクラスをオーバーライドすることで操作を変更できる。

つまり、vendor配下に用意されている処理を継承して、オーバーライドすることで処理のカスタマイズができる。



# 6-2 トークン認証


# 6-3 JWT認証


# 6-4 OAuthクライアントによる認証・認可


# 6-5 認可処理



# 不明点
- [ ] AuthServiceProviderで記述した内容の理解

# 調べたこと
AuthServiceProviderで記述した内容の理解
```
    public function boot(): void
    {
        $this->registerPolicies();
        $this->app->make('auth')->provider(
            'cache_eloquent',
            function (Application $app, array $config) {
                return new CacheUserProvider(
                    $app->make('hash'),
                    $config['model'],
                    $app->make('cache')->driver()
                );
            }
        );
    }
```

・registerPolicies()　このメソッドの中身を見てみると、ポリシーをforeachで回している。Gate::policy($model, $policy);が中の処理。  
GateとPolicy→認可に関するもの（アクセス制限）。Policyは特定のモデルに対して行うアクセス制限（ブログの投稿、編集など）、Gateはモデルやリソースに紐づかないアクションも認可できる（管理画面へのアクセスなど）。


