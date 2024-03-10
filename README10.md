# CHAPTER10 エラーハンドリングとログの活用
10-1 エラーハンドリング  
10-2 ログ活用パターン

# 10-1 エラーハンドリング
エラー表示  
　.envファイルに記述されているAPP_DEBUGをtrueにするとエラー内容がブラウザなどの表示画面に出力される。  
プロダクション環境でのエラー表示は脆弱性につながるため、APP_DEBUGはfalseにする。  
開発環境ではtrueで良い。

エラーの種別  
　Laravelには補足しきれなかった例外を処理するためにApp\Exceptions\Handlerクラスが用意されている。  
アプリケーションで発生する例外は大別すると以下の通り。  
・システム例外  
　処理を実行できない例外。アプリケーションそのものに由来するバグ、依存ライブラリのバグ、データベースやキャッシュサーバーなどのハードウェア・ミドルウェアのよる障害、ネットワーク障害など。

・不正リクエスト例外  
　アプリケーションへの不正なリクエストで発生する例外を指す。存在しないURIへのリクエストやバリデーションエラーなどが該当する。  
ミドルウェア、フォームリクエスト、バリデーションなどの機能を利用してエラーハンドリングを行う。

・アプリケーション例外  
　アプリケーションで定義される、ビジネスロジックに関連するエラーが該当する。  
ユーザー登録処理で重複のエラーや在庫不足など、アプリケーションが定めている正常な処理を続行できない例外は、要件に合わせて的確に処理をする必要がある。

エラーハンドリングの基本  
　アプリケーション内でエラーがハンドリングされなかった場合、Laravel標準のApp\Exception\Handlerクラスがエラーハンドリングを担う。  
このクラスには発生した例外を記録としてログに書き込むreportメソッドと、エラー発生時にレスポンスを作成するrenderメソッドがある。  
様々な要因で例外が発生するため、エラー原因をログに残すことは重要。  
　reportメソッドで処理されない例外もいくつかあるので注意が必要。
AuthenticationException、AuthorizationException、HttpException、ModelNotFoundException、MultipleNotFoundException、RecordNotFoundException、SuspiciousOperationException、TokenMismatchException、ValidationExceptionが該当する。

Fluentdの活用  
　特定の例外だけ記録処理を変更する場合は、任意の処理を追記できる。  
Fluentdを使って複数のアプリケーションエラーを収集するケースなどでは、reportメソッドからFluentdサーバに送信することが想定できる。

例外の描画テンプレート変更  
　Laravelではフレームワークで用意されている例外に対応する描画テンプレートが用意されている。  
テンプレートはHTTPステータスコードの対応しているため、アプリケーションに合わせてエラー発生時に描画されるテンプレートを変更する場合は、resources/views/errors配下にテンプレートファイルを設置する。  
例）resources/views/errors/404.blade.php

　Acceptヘッダのメディアタイプにtext/jsonかapplication/json、application/hal+jsonと+json指定がある場合はbaldeテンプレートではなくJSONレスポンスで返却される。

　特定の例外で任意のレスポンスを返却する場合は、renderメソッド内に実装することで対応できる。

エラーハンドリングパターン  
　フレームワークで用意されている例外クラスではなく、アプリケーション固有の例外クラスが多くなると、App\Exceptions\Handlerクラスのrenderメソッド内でエラーの分岐処理が多くなり、レスポンスの処理が複雑になりがちである。  
複雑化を防ぐため、Illuminate\Contracts\Support\Responsableインターフェースを実装した、例外クラスとレスポンスを関連づける方法が用意されている。  
Bladeテンプレートと例外処理、APIレスポンスと例外処理を結びつける方法がある。実装方法はApp\Exceptions\AppException.php、UserResourceException.phpを参照。

# 10-2 ログ活用パターン
　障害検知や障害の原因究明、データ分析での活用など、ログには様々な利用用途があるため、正しいログ生成が重要になる。  
ログに関する操作はLogファサード経由、亜m田はPsr\Log\LoggerInterfaceを実装しているたね、インターでーすをコンテナ経由で利用したり、loggerヘルパを利用できる。

ログ出力設定  
　single、daily、syslog、errorlogが標準で用意されているほか、Laravel8からは複数のログ出力を同時に行うstack、Slackへ通知を行うslackが追加され、さらにログドライバを追加可能なcustomも選択できる。  
ログの設定はconfig/app.phpのlogとlog_levelキーを利用する。

single  
　標準の設定では、ログはstorage/log/laravel.logファイルに出力される。  
ログ出力が多いサービスやアクセスが多いサービスなどでは、サーバのストレージを圧迫させてしまい、容量不足などの障害の原因となる。

daily  
　日単位でログファイルを作成し、指定期間分のログファイルを保持する。（デフォルトでは14日間）

syslog  
　ログをsyslogに出力する。標準的なLinux環境でのsyslogの出力先は/var/log/messagesになる。

errorlog  
　errorlogを指定すると、PHPのerrorlog関数でログ出力を行う。

stack  
　Laravelが利用しているMonologの機能を用いて、config/logging.phpのchannels関数で複数のドライバを指定することで同時に利用できる。

slack  
　Slackの指定チャンネルへログ内容を通知する。デフォルトではcritical以上でなければ通知されない。

papertrail  
　ログ収集などの機能を提供しているPapertrailへのログ送信を行う。  
事前に登録などの手続きが必要になる。

stderr  
　標準エラー出力を行う。  
実際のアプリケーション運用でDockerなどを用いたコンテナ環境を利用する場合は、papertrailなどのログ収集サービスやログ収集サーバへの送信が必須となるので、stderrなどと組み合わせて利用するのが一般的となっている。



# 不明点
- [x] syslog、rsyslogとは  
- [x] Papertrailとは  
- [ ] Monologとは

# 調べたこと
syslog、rsyslogとは  
　UNIX系OSにおけるシステムログの記録を担当する。

Papertrailとは  
　PapertrailはLinuxサーバやWebアプリケーション等の様々なログをオンラインで集約し、閲覧・検索することができるサービスです。rsyslogやsyslog-ng等のsyslogデーモンに数行追記するだけで導入できる

Monologとは  
　
