# CHAPTER4 HTTPリクエストとレスポンス
4-1 リクエストハンドリング  
4-2 バリデーション  
4-3 レスポンス  
4-4 ミドルウェア  

# 4-1 リクエストハンドリング
　ユーザーからのリクエストは、public/index.php内でIlluminate\Http\Requestクラスのインスタンスとして取得する。  
public/index.phpクラス内に、HTTPカーネルのhandleメソッドを通っている処理がある。  
リクエストは以下の情報が含まれている。  

・$_GET  
・$_POST  
・$_COOKIE  
・$_FILES  
・$_SERVER  

これらのリクエストを参照する方法は以下の3つ  
・Requestファサードを利用する  
・Requestクラスのインスタンスをコンストラクタインジェクション、メソッドインジェクションを介して利用する  
・フォームリクエストを利用する  

1.Requestファサード  
使用例
```
use Illuminate\Support\Facades\Request;

$name = Request::get('name');

$file = Request::file('material');

$name = Request::cookie('name');

$acceptLangs = Request::header('Accept-Language');

$serverInfo = Request::server();
```

2.Requestオブジェクト  
　コンストラクタインジェクション、メソッドインジェクションを使ってIlluminate\Http\Requestクラスのインスタンスを直接使用する。  
Requestファサードで使えるメソッドは全て使える。  

　JSONのリクエストも扱える。  
その場合は、クライアントのヘッダリクエストでContent-Type:application/json、または+jsonが指定されているとgetメソッドなどで値を取得できる。  
Content-typeが指定されていないリクエストでも、jsonメソッドで取得できる。
```
$result_json = $request->json('nested');
```

3.フォームリクエスト
　Illuminate\Http\Requestを継承したクラスで、入力値の取得に加えてバリデーションルールや認証機能などを定義できる機能。
バリデーションロジックをコントローラクラスから分離できる。

フォームリクエストクラス生成コマンド
```
$ php artisan make:request UserRegistPort
```

コマンド実行後の初期状態
```
// 省略
class UserRegistPort extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            // 省略
        ];
    }
}
```
　authorizeはリクエストに対する権限を、rulesにはバリデーションルールを設定する。  
フォーム処理へのアクセス権下がある場合はauthorizeメソッドでtrueを返すようにする。  
コントローラでこのフォームリクエストクラスをメソッドインジェクションでインスタンス化し、Requestオブジェクトと同じ方法で取得する。

# 4-2 バリデーション
　適切ではないデータを排除するだけでなく、クロスサイトスクリプティングなどの攻撃を防ぐ意味でも重要。  
ルールを指定する際は、配列で指定とパイプラインで区切る方法があるが、正規表現のメタ文字（パイプライン）と重複して不具合を起こす可能性があるので、配列で指定するのが良い。

バリデーションの利用  
バリデーション機能の利用方法は以下の2つ  
1. コントローラでのバリデーション
2. フォームリクエストを使ったバリデーション

1.コントローラでのバリデーション  
　ルールを指定し、validateメソッドでバリデーションを実行する。  
直前の画面ではなく、専用のエラー画面への遷移や独自の処理を実行したい場合は、validatorクラスのインスタンスを生成し、failsメソッドを呼び出す。

2.フォームリクエストを使ったバリデーション  
　フォームリクエストのrulesメソッドに配列でルールを指定する。  
コントローラを通る時にはすでにバリデーション判定が行われている。  

バリデーション失敗時の処理  
　ビューでは常に$errorsの名前でIlluminate\Support\MessageBagクラスのインスタンスが用意されている。  
all, get, first, hasなどのメソッドを利用できる。  
バリデーションメッセージをカスタマイズする場合は、フォームリクエストクラスでmessagesメソッド内に連想配列でメッセージを指定する。

バリデーションルールのカスタマイズ  
ルールの追加  
　Validatorクラスのextendメソッドを使う。
コントローラで使用する。
```
// 省略
// ascii_alphaを追加
$rules = [
    'name' => ['required', 'max:20', 'ascii_alpha'],
]

$inputs = $request::all();

// バリデーションルールにascii_alphaを追加
Validator::extend('ascii_alpha', function ($attribute, $value, $parameters) {
    // 半角アルファベットならOK
    return preg_match('/^[a-zA-Z]+$/', $value);
});

$validator = Validator::make($inputs, $rules);
```
　このやり方は複数フォームで使用するケースには適さない。  
汎用的に使用する場合は、独自のバリデータクラスを使用する方が良い。

条件によるルールの追加  
　特定の条件のみバリデーションを追加するには、Validatorクラスのsometimesメソッドを使う。
```
$validator->sometimes(
    'age',
    'integer|min:18',
    function ($inputs) {
        return $inputs->mailmagazine === 'allow';
    }
);
```



# 4-3 レスポンス
　Laravelでレスポンス処理を受け持つのはResponseクラスになる。  
Responseファサードも用意されており、返却するデータによって使い分ける。

・文字列返却  
　Responseファサードやヘルパー関数を使って、引数に入れた文字列をそのまま返す。  
・テンプレート出力  
　Bladeテンプレートなどを出力する場合は、viewヘルパーやViewファサードを使ってテンプレートを指定して返す。  
・JSON出力  
　JSONレスポンスを生成する場合、Responseファサードのjsonメソッドやjsonヘルパー関数を使う。  
　JSONPも可能。  
・ダウンロードレスポンス  
　Responseファサードやヘルパ関数のdownloadメソッドを使う。  
・リダイレクトレスポンス  
　Responseファサードやヘルパ関数のredirectToメソッド、redirectメソッドを使う。  
　withメソッドで一時的なエラーメッセージを追加したりできる。  

リソースクラスを組み合わせたREST APIレスポンスパターン  
　書籍を元に実際に実装済み。
コマンド例
```
php artisan make:resource CommentResource
php artisan make:resource CommentResourceCollection
```



# 4-4 ミドルウェア
Laravelにおけるミドルウェアとは  
　コントローラクラスの処理前後に位置し、HTTPリクエストのフィルタリングやHTTPレスポンスの変更を担う。  
Laravelでは以下の3通りある。  

・システム全体で使用するミドルウェア（グローバルミドルウェア）  
・特定のルートに対して適用するミドルウェア（ルートミドルウェア）  
・コントローラクラスのコンストラクタで指定するミドルウェア（コンストラクタ内ミドルウェア）

HTTPリクエストが来たら以下の図の順でミドルウェアを通り、HTTPレスポンスを返す。  
https://www.google.com/url?sa%253Di%2526url%253Dhttps%253A%252F%252Fstorehouse-techhack.com%252Flarval-middleware%252F%2526psig%253DAOvVaw3orx3sGjRfSxEr_KQ6pJtL%2526ust%253D1705313535340000%2526source%253Dimages%2526cd%253Dvfe%2526opi%253D89978449%2526ved%253D0CBIQjRxqFwoTCOiXxJbS3IMDFQAAAAAdAAAAABAU

デフォルトで用意されているミドルウェア  
App\Http\kernel.phpクラスで用意されている。  

独自のミドルウェアの実装  
　リクエストヘッダとレスポンスヘッダのログを書き出すミドルウェアを実装済み（グローバルミドルウェア）。
コマンド例
```
php artisan make:middleware HeaderDumper
```

作成したミドルウェアはApp\Http\Kernelクラスで定義する。  
プロパティ  
・$middleware  
　グローバルミドルウェアを定義  
・$middlewareGroups  
　複数のミドルウェアをまとめて扱いたい場合  
・$routeMiddleware  
　特定のルート、コントローラに適用したい場合

・webグループ  
　routes\web.phpを通るものに適用される  
・apiグループ  
　routes\api.phpを通るものに適用される


# 不明点
- [x] HATEOASとは
- [x] sprintf
- [x] リソースコレクションについてもう少し詳しい理解（必要な場面は？）

# 調べたこと
HATEOASとは  
　RESTful原則を拡張する追加の制約。  
例えば、ブログアプリケーションでidやtitleの要素を返却するだけでなく、投稿者やコメント投稿者の情報にアクセスしたいときにAPIレスポンスに含まれていなければならない。  
また、Eloquentモデルをそのまま返すと、カラム変更したときに影響を受けてしまうので、JSONフォーマットを用いて、データベースとレスポンスの世界を切り分けることが必要。  
JSONフォーマットには、JSON APIやHAL、JSON-LDなどがある。

sprintf  
　PHPのメソッドで、指定のフォーマットを作ることができ、使い回すのに便利。  
例）
```
sprintf("%s 君は %s を %d 個食べました。", "太郎", "りんご", 7)
```

リソースコレクションについてもう少し詳しい理解（必要な場面は？）  
　リソースクラスは、モデルを渡すと望み通りのJSONデータに整形してくれる。  
ただ、リソースクラスは基本的に一つのデータ位して整形するようにっているため、検索によって複数データが取得できた場合は望み通りの形に整形してくれない。  
そのためリソースコレクションを使う。
