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
　



# 4-4 ミドルウェア


```


# 不明点
- [ ] HATEOASとは
- [ ] sprintf

# 調べたこと

