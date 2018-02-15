# Fratily Router

Fratily RouterはFratilyPHPで使用されるURLルーターです。

~~ルーティングだけでなくリバースルーティングにも対応しています。~~

> **注意**  
> 基本的にこの文中ではネームスペースを省略してクラス名を記述します。

## インストール

このライブラリをあなたのプロジェクトで使用する前に、以下のコマンドを実行
してください。

``` bash
$ composer require 'fratily/router'
```

## ルート定義方法

```php
$c  = new RouteCollector();

$c->addRoute("home", "/", ["GET"]);
$c->addRoute("mypage", "/users/my", ["GET"]);
$c->addRoute("userpage", "/users/{id:[1-9][0-9]*}", ["GET"]);
$c->addRoute("userupload", "/users/{id:[1-9][0-9]*}/upload", ["GET"]);
```

ルートは`RouteCollector::addRoute()`を使用して定義します。

4つの引数を取り先頭の2つ以外はオプションです。先頭からルート名・一致パス
・許容メソッド・ルートデータです。

許容メソッドはこのルートが許容するHTTPメソッド名を格納した配列であり、nullを指定
することですべてのメソッドを許容します。

ルートデータはルート定義者が自由に扱える配列です。ルーティング結果にこの値が追加
されるため、コントローラー名やアクション関数を保存しておく等に使用できます。

### 許容メソッド省略方法

すべてのHTTPメソッドを許容するパスは現実的ではありません。しかしルートを定義する
たびに許容メソッド名を格納した配列を記述するのも面倒です。

そんな時は`RouteCollector::get()`をはじめとした`post()`・`put()`・`patch()`
・`delete()`といったショートカットメソッドを利用しましょう。

これらのメソッドはメソッド名に応じた許容メソッドとともに
`RouteCollector::addRoute()`を実行します。

引数は先頭からルート名・一致パス・ルートデータの3つを取ります。

### グループ化

最初に示したルート定義方法では第1セグメントがusersのパスを3つ定義しています。
これらをまとめて定義するために`RouteCollector::group()`が存在します。

このメソッドは第一引数に共通値、第二引数にコールバック関数を取ります。
コールバックが実行されている間だけ共通値が適用される実装になっています。

共通値には2種類あり文字列が指定された場合は、コールバック内の`addRoute()`で
定義された一致パスの先頭に共通値を結合します。
配列が指定された場合はルートデータに共通値を追加します。

コールバックは引数にRouteCollectorを受けます。

### 正規表現の埋め込み

`{name:regex}`構文で複数のルートに一致させることができます。

nameはパラメータ名として使用され、ルーティング結果にパラメータリストとして
取得できます。regexはその名の通り正規表現を記述する場所です。

### 正規表現の埋め込み part2

正規表現の埋め込みは先ほど話しましたが、パラメータとして取得する値を
自由に書き換える方法もあります。それが**ShortRegex**です。

これを使用するには`{name|sregex}`構文を記述する必要があります。

nameは通常の正規表現と同じくパラメータ名ですがsregexはShortRegexの名前です。

ShortRegexの実態は`ShortRegexInterface`を実装したクラスです。
これはユーザーが自由に定義します。

クラスの定義がすんだら`Router::addShortRegex()`で登録する必要があります。

```php
class DigitShortRegex implements ShortRegexInterface{
    
    public static function match(string $segment): bool{
        return (bool)preg_match("/\A[1-9][0-9]*\z/");
    }

    public static function convert(string $segment){
        return (int)$segment;
    }
}

Router::addShortRegex("d", DigitShortRegex::class);

$c->get("userpage", "/users/{id|d}");
```

## ルーティング

ルーティングは以下のようにして行います。

```php
$method = $_SERVER["REQUEST_METHOD"];
$path   = $_SERVER["REQUEST_URI"];

$router = $c->createRouter($method);
$result = $router->search($path);

switch($result["result"]){
    case Router::NOT_FOUND:
        //  一致するルートがなかった
        break;
        
    case Router::FOUND:
        //  一致するルートがあった
        $params = $result["params"];
        $data   = $result["data"];
}
```

## Note

このライブラリの前身である`kento-oka/roust`は、
以下の定義が行われた場合正常に動作しないという不具合がありました。

```php
$route->addShortRegex("d", new NaturalNumber());
$router->get("/users/{id:[1-9][0-9]*}/", []);
$router->get("/users/{id:|d}/profile/", []);

$router->search("GET", "/users/123/");          // Not Found
$router->search("GET", "/users/123/profile");   // Found
```

これをFratily/Router風に書き換えると次のようになります。

```php
Router::addShortRegex("d", new NaturalNumber());

$collector->get("userpage", "/users/{id:[1-9][0-9]*}/", []);
$collector->get("userprofile", "/users/{id|d}/profile/", []);

$router = $collector->createRouter("GET");
$router->search("/users/123/");
$router->search("/users/123/profile/");
```

Fratily/Routerではこの問題が解決され、どちらのルールも正しくマッチします。

> 正常に動作しなかった理由はshortRegexと通常の正規表現の一致確認の順番でした。