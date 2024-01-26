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



# 6-2 トークン認証


# 6-3 JWT認証


# 6-4 OAuthクライアントによる認証・認可


# 6-5 認可処理



# 不明点
- [ ] 6-1の実装例がよくわからない

# 調べたこと
