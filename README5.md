# CHAPTER5 データベース
5-1 マイグレーション  
5-2 シーダー  
5-3 Eloquent  
5-4 クエリビルダ  
5-5 リポジトリパターン

# 5-1 マイグレーション
　テーブル作成や、定義変更を担う。  
PHPのソースコードで管理するため、本番環境へデプロイするときにデータベースの定義変更漏れが発生することがなくなる。  

テーブルの定義は実装の通り（authors, books, publishers, bookdetailsテーブル）

テーブルにインデックスの付与や削除も可能。  
・->primary(カラム名)  プライマリキーを付与する。  
・->primary([カラム名1, カラム名2])  複合プライマリーキーを付与する。  
・->unique(カラム名)  ユニークキーを付与する。  
・->index(カラム名)  通常のインデックスを付与する。  
・->dropPrimary(プライマリキー名)  プライマリキーを削除する。  
・->dropUnique(ユニークキー名)  ユニークキーを削除する。  
・->dropIndex(インデックス名)  通常のインデックスを削除する。  

下記の通り2通りの方法で実装できる。
```
$table->integer('id')->index();

$table->integer('id');
$table->index();
```

# 5-2 シーダー
シーダー  
　マスタデータなど初期のデータの投入が必要なケース、動作テストを行う際にテストデータが必要なケースに実行する仕組み。  
シーダークラスの命名に指定はないが、authorsテーブルならAuthorTableSeeder.phpなどと指定するとわかりやすい。

　コマンドを実行するとIlluminate\Database\Seederクラスを継承したクラスが作成され、runメソッドが用意されている。  
ここに登録処理を記述するが、DBファサードやEloquentが使用できる。  
データ挿入の記述ができたら、DatabaseSeeder.phpのrunメソッドにデータ登録を行う処理を記述する。  
```
publish function run()
{
    $this->call(AuthorsTableSeeder::class);
}
```
下記コマンドで実行する
```
php artisan db:seed
```

Faker  
　より現実に近いテストデータが必要な場合、簡単にテストデータが作れるライブラリ「Faker」が使用できる。  
seederクラスのrunメソッドに記述する。  
authorsテーブルに10件データを登録する処理を実装。

Factory  
　大量のデータの投入にはFactoryが便利になる。  
database/factories/ModelFactory.php内にEloquentクラスごとのファクトリーを記述する。  
なお、今回はFakerを使用する。  
Publisherクラスで実装。

手順  
1. モデルクラス作成
2. ファクトリークラスを作成し、データ投入処理定義
3. DatabaseSeederクラスのrunメソッドで、2の処理を呼び出す

下記コマンドで実行する
```
php artisan db:seed
```

# 5-3 Eloquent
　モデルクラスのルール  
1. テーブル名は複数形、モデル名は単数形にする。異なる名前のテーブル名と紐づける時は、$tableプロパティに指定する。
2. プライマリキーを任意のカラム名にする場合は、$primaryKeyプロパティに指定する。
3. デフォルトのcreated_at, updated_atを必要としない場合は、$timestampsプロパティをfalseにする。
4. 変更を想定していないカラムの値が渡された場合、脆弱性につながるため、MassAssignmentは無効となっているが、$fillable、$guarderプロパティで設定できる。

データ操作の応用  
1. toJSON()で抽出結果をJSON形式で返す
2. カラムの値に対して固定の編集を加える→Modelクラスに（アクセサ：取得時）getカラム名Attribute(), （ミューテタ：登録時）setカラム名Attribute()
3. データがない場合のみ登録→firstOrCreate(), firstOrNow()を使用する。

リレーション  
　一対一の関係  
　BookとBookdetailクラスの場合（リレーションdetail()をBookモデルで定義している）  
書籍から書籍詳細を経由してISBNを取得する例
```
$book = \App\Models\Book::find(1);
echo $book->detail->isbn;
```

　一対他の関係  
　Author（1）とBook（他）の場合  
Authorから書籍名を取得する例
　```
$author = \App\Models\Author::find(1);

foreach ($author->books as $book) {
    echo $book->name;
}
```

実行されるSQLの確認  
　EloquentでSQLを発行した際のSQLは以下の2通りの方法で確認することができる。
1. toSql()を使う
2. getQueryLog()を使う  
　→DBファサードのメソッド。バインドされた変数も確認できる。

# 5-4 クエリビルダ
　メソッドチェーンを使用し、SQLを組み立てて発行する仕組み。  
クエリビルダを使用し、get()を使って取得すると、stdClassオブジェクトのコレクションとして返され、first()を使うとオブジェクト単体が返される。

取得する際のメソッドはお馴染みのものなので省略。

トランザクションとテーブルロック  
　クエリビルダには、トランザクション処理やテーブルロックのメソッドも用意されている。  
この処理はEloquentでも利用可能となる。

・DB::beginTransaction()　手動でトランザクションを開始する  
・DB::rollback()　手動でロールバックを実行する  
・DB::commit()　手動でトランザクションをコミットする  
・DB::transaction(クロージャ)　クロージャの中でトランザクションを実施する

・sharedLock　selectされた行に共有ロックを掛け、トランザクションコミットまで更新を禁止する  
・selectされた行に排他ロックを掛け、トランザクションコミットまで読み取りを更新する

# 5-5 リポジトリパターン
　アプリケーションでのデータのストア先はさまざまで、RDB、NoSQL、キャッシュやファイルなど。  
テストでは本番と違うDBを使用することもある。  
　しかし、ストア先が変わってもプログラムの変更範囲は限定的なものにしたい。  
リポジトリパターンは、ビジネスロジックからデータの保存や復元を別レイヤに分離することで、コードのメンテナンス性やテストの容易性を高める実装パターン。




# 不明点
- [ ] stdClassとは
- [ ] トランザクション、テーブルロックとは何か
- [ ]　トランザクション処理の使い所

# 調べたこと
