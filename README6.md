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
「guards」はguardドライバ指定時に利用され（ログイン状態を管理するもの）、「providers」は認証情報のアクセス方法（eloquentなど）が記述される。

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
　APIアプリケーションを開発する場合は、Webアプリケーションと同じ仕組みが利用できないことがある（クッキー、セッションなど）。  

そのような場合に使用される一つがtokenドライバ。これは大規模なものには向いていないため、実際は他のドライバの方が多い。  
Eloquentを使うため、api_tokenカラムを作るが、usersテーブルとは別に概念データモデルなどに合わせてユーザーIDとtokenを別テーブルで持つことが多い。

トークン認証とは  
　ユーザーの登録時にランダムな英数字で作られたトークンが各ユーザに割り当てられ、割り当てられたトークンを利用して認証する

メモ：実装、動作確認不十分

　実際の使い方は実装を参照。

# 6-3 JWT認証
　実際のAPIアプリケーションで利用されているのがJWT（JSON Web Token、ジョット）認証。こちらの方がよりセキュア。  
JSONに電子署名を用いて必要に応じてJSONを検証して認証の可否を決定する。  
JWTは以下のようなJSONをBase64URLエンコードしたもの。
```
{
  "iss": "io.exact.sample.jwt",
  "sub": "saumple",
  "exp": 1670085336,
  "email": "sample@extact.io",
  "groups": "member, admin"
}
```

　

# 6-4 OAuthクライアントによる認証・認可
　OAuthは、外部サービス（Google、Twitterなど）に認証を委託し、アプリケーション側ではユーザー情報だけを管理する、または外部サービスにある個人情報にアクセスする場合などに使用する技術。  
外部サービスの認可サーバーからトークンを取得し（この時ログイン画面があったりする）、リソースサーバーにトークンを投げて正しければ情報を取得できる。  
Socialiteパッケージを利用することで認証クライアントを利用できる。

　パッケージで利用する設定値は、config/service.phpに以下のような設定キーと値を記述する。  
・client_id　外部サービスではこうされるクライアントID  
・client_secret　外部サービスで発行されたクライアントシークレット  
・redirect　認証後にリダイレクトされる、アプリケーションのコールバックURL  
・guzzle　Socialiteが内部で利用しているGuzzleのコンストラクタに渡す引数（オプション）

　GitHubを利用する場合は、Settings→Developer settingsにてアプリケーションのURL等を入力するとClient IDとClient Secretの値が発行される。

　外部サービスの認証画面へリダイレクトは、Socialiteで用意されているメソッドを利用する。  
```
\Socialite::driver('github')->redirect();
```

　外部サービスからコールバックされると、Socialite経由でユーザー情報が取得できる（$user）。  
そのデータをusersテーブルで格納して、アプリケーション内のユーザーとして使用できる。
```
$user =\Socialite::driver('github')->user();
\Auth::login(
    User::firstOrCreate([
        'name' => $user->getName(),
        'email' => $user->getEmail(),
    ]),
    true
);
```

　通信内容をログに出力する、ユーザー情報にアクセスする際にパラメータを追加するなど、動作を拡張するオプションもいくつかある。



# 6-5 認可処理
　認可処理とは、リソースや機能に利用制限を設けて制御することであり、Gateファサードを通して提供されている。  
標準ではGateもPolicyもAuthServiceProviderクラスに記述して利用する。  

ゲート  
　AuthServiceProviderにて、名前をつけてGateファサードのdefine()に認可ロジックを定義し、コントローラーなどでallow(), denies()メソッドで呼びだす。

　一つの認可処理を、一つのクラスとして表現することもできる。（ロジックをクラスに切り出す）  
つまり、認可処理が実装されているクラスを作成し、サービスプロバイダ（bootメソッド）に登録する。
```
final class UserAccess
{
    public function __invoke(User $user, string $id): bool
    {
        return intval($user->getAuthIdentifier()) === intval($id);
    }
}
```

　beforeメソッドをサービスプロバイダのbootメソッドに記述し、認可処理をする前に動作させたい処理を定義できる。

ポリシー  
　認可のロジックはポリシークラスに記述する。  
ポリシークラスを作成するコマンドを実行すると、viewAny(), view(), create(), update(), delete()などのメソッドを持ったポリシークラス雛形が生成される。  
作成したクラスとEloquentモデルを対応させるためには、AuthServiceProviderに用意されている$policiesに登録する。
```
protected $policies = [
    \App\Models\Content::class => \App\Policies\ContentPolicy::class,   // Eloquentモデルと対応させる
    \stdClass::class => ContentPolicy::class,   // Eloquentモデルを利用しないポリシー（ビルトインクラスstdClass）
];
```
　こちらもallowメソッドを使い呼び出して使用する。

Bladeによる認可処理  
```
@can('edit', $content)
    // 編集ボタンなど
@elsecan('create', \App\Models\Content::class)
    // 作成ボタンなど
@endcan
```

View Composer



# 不明点
- [ ] AuthServiceProviderで記述した内容の理解
- [ ] すでにレコードがあるテーブルにnull許容しないユニーク制約をつけたカラムを追加する方法
- [x] userCurrentOnUpdate()（Migration）
- [ ] FK（外部キー制約）の付け方（Migration）
- [ ] config/auth.phpのそれぞれの項目は何か

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

すでにレコードがあるテーブルにnull許容しないユニーク制約をつけたカラムを追加する方法
https://noauto-nolife.com/post/laravel-notnull-exception/

userCurrentOnUpdate()（Migration）
MySQLのON UPDATE CURRENT_TIMETAMP句は、タイムスタンプが自動で更新されるようになる。

FK（外部キー制約）の付け方（Migration）


config/auth.phpのそれぞれの項目は何か  
・guard　guardによって、アクセスしてくるユーザーをどのように識別するかを定義する  
・driver　どのように認証を行うか  
・provider　認証に必要な情報をどこから取得するか
・hash　tokenを暗号化するかどうか
```
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'token',
        'provider' => 'users',
        'hash' => false,
    ],
],
```
