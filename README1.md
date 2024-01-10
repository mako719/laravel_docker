# CHAPTER1 Laravelの概要 理解度まとめ
1-1 Laravelとは
1-2 環境構築
1-3 最初のアプリケーション

# 1-1 Laravelとは
Laravelの特徴
1. 容易な学習
2. Symfonyベース
3. 多機能
4. 積極的なバージョンアップ→Laravel9から1年ごとにメジャーリリース
5. 高い拡張性→ディレクトリ構成は開発者が自由に決められる。MVCパターン、ADR、リポジトリパターン、レイヤードアーキテクチャ、ヘキサゴンアーキテクチャなどのドメイン設計駆動。

# 1-2 環境構築
Laravei sail：Laravelが公式に提供している開発環境。Dockerを使用して簡単に環境構築ができる。
laravel_dockerなど任意のディレクトリを用意して移動し、curlコマンドを利用してLaravel sailをダウンロードする。
```
$ curl -s https://laravel.build/sample | bash
```
コマンド操作
エイリアスを登録すれば便利
```
alias sail="./vendor/bin/sail"
```
コンテナを起動
```
$ sail up
```
コンテナをバックグラウンドで起動
```
$ sail up -d
```
コンテナの終了
```
$ sail down
```
コンテナへの接続
```
$ sail shell
```
MySQLへの接続
```
$ sail mysql
```

Homesteadを利用して環境構築することも可能。

# 1-3 最初のアプリケーション
Laravelのディレクトリ構成
・app
コントローラ、ミドルウェア、例外クラス、コンソール、サービスプロバイダなど、アプリケーションの主要な処理はここ。
・bootstrap
アプリケーションで最初に実行される処理やオートローディング設定が入っている。
・config
アプリケーションの設定値を記載したファイルを入れる。
・database
マイグレーションデータ、初期投入データなどのデータベース関連ファイルが入っている。
・public
Webアプリケーションとして公開する場合、このフォルダをドキュメントルートに設定する。
・resource
Viewのテンプレートファイルや、言語ファイル。
・routes
ルート定義ファイル。
・storage
プログラム実行時にLaravelが作成するファイルの出力先。ログやファイルのキャッシュなど。
・tests
テストコードを記載したファイルをおく。
・vendor
Composerによりダウンロードされる各種パッケージのディレクトリ。LaravelやSymfony本体のコードもそこに入る。

ログイン機能
・Auth::attempt() 引数にキーバリューの配列を受け取り、DBから一致するユーザーを見つける認証のメソッド。
・$request->session()->regenerate(); セッションIDを手動で作成する。

ログアウト機能
・Auth::logout() ログアウトする。
・$request->session()->invalidate(); セッションIDを再生成して、全てのデータを削除する。
・$request->session()->regenerateToken(); 二重送信を防ぐ
・Auth::check ログイン状態を確認できる

イベント
サービスプロバイダにリスナーを定義する。
〇〇Listner.phpファイルでイベントの処理を実装する。イベントが発生した場合、handleメソッドが実行される。
下記のようにコードにイベント生成処理を差し込む。
```
event(new Registered($user));
```


# 不明点
[ ]Laravel sailと、Homesteadの違い

# 調べたこと
・Laravel sailとHomesteadの違い
Laravel sail
Docker Compose を含んだPHPのパッケージを composer で取り込んで起動させる形。
プロジェクト単位にインストールをするので気軽に始められる。ただし、他のプロジェクトでも使用して立ち上げている時に Port が重複したり、リソース食いになってマシンが重くなったりすることがある。
Homestead
Vagrant+Virtualbox/Parallels を使い、Ubuntu環境で動かす形。
複数のプロジェクトがあるときに、それらの分を Homestead.yaml に追記すれば動くようになるので、プロジェクトが多い時にマシンリソースが Docker に比べてマシ。
その他
ローカルマシンにHomeBrewなどを使って、PHP/MySQLをインストールする。古典的なやり方で構築が大変。

