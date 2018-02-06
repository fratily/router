# Fratily Router

Fratily RouterはFratilyPHPで使用される(予定の)URLルーターです。

FratilyPHPに依存しているわけではないので、
PHP7.0以上の環境さえあればどこでも使えます。

## インストール

このライブラリあなたのプロジェクトで使用する前に、
以下のコマンドを実行してください。

``` bash
$ composer require 'fratily/router'
```

> cmoposer が使用可能でない場合は使用できるように努力してください。

## 基本的な使い方

```php
$method = $_SERVER["REQUEST_METHOD"];
$url    = $_SERVER["REQUEST_URI"];

//  すべてのルーティングルールは、このオブジェクトを使用して定義します。
$collector  = new Fratily\Router\RouteCollector();

//  ルーティング処理はこのオブジェクトで行います。
$dispatcher = new Fratily\Router\Dispatcher($collector);

//  GET http://example.com/　にマッチします
$collector->addRoute("GET", "/", [
    "controller"    => "index",
    "action"        => "index"
]);

//  GET http://example.com/users/　にマッチします
$collector->addRoute("GET", "/users/", [
    "controller"    => "user",
    "action"        => "index"
]);

//  GET http://example.com/users/my/　と
//  POST http://example.com/users/my/　にマッチします
$collector->addRoute(["GET", "POST"], "users/my/", [
    "controller"    => "user",
    "action"        => "mypage"
]);

//  GET http://example.com/users/123/　にマッチします
$collector->addRoute("GET", "/users/{uid:[1-9][0-9]*}/", [
    "controller"    => "user",
    "action"        => "page"
]);

$result = $dispatcher->dispatch($method, $url);

switch($result[0]){
    case Fratily\Router\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case Fratily\Router\Dispatcher::METHOD_NOT_ALLOWED:
        // ... 405 Method Not Allowed
        $allowedMethods = $result[1];
        break;
    case Fratily\Router\Dispatcher::FOUND:
        $params = $result[1];   //  パラメータ
        $data   = $result[2];   //  ルートデータ
        //  何らかの処理
        break;
}
```

### ルート定義

ルーティングルールは`RouteCollector::addRoute()`で定義します。

第1引数には許可するHTTPメソッド。  
第2引数にはマッチするURIのルール。  
第3引数にはルートデータを定義します。

第2引数の先頭のスラッシュは省略することができます。

> *ルートデータとは*  
> ルート定義者が自由に扱える配列です。  
> MVCモデルにおけるコントローラー名や、ルート名等に使用できます。

```php
$collector->addRoute("GET", "/", [
    "controller"    => "index",
    "action"        => "index"
]);

$collector->addRoute("GET", "/users/", [
    "controller"    => "user",
    "action"        => "index"
]);

$collector->addRoute(["GET", "POST"], "users/my/", [
    "controller"    => "user",
    "action"        => "mypage"
]);
```

`RouteCollector::get()`,`RouteCollector::post()`,`RouteCollector::put`,
`RouteCollector::delete`メソッドでHTTPメソッドの指定を省略できます。

```php
$collector->get("/users/", [
    "controller"    => "user",
    "action"        => "index"
]);
```

#### 正規表現の使用

構文`{id:regex}`を使用することで正規表現を埋め込むことができます。
*id* はパラメータ名、*regex* はPHPのpreg_match関数で使用する正規表現を指定します。

```php
$collector->addRoute("GET", "/users/{uid:[1-9][0-9]*}/", [
    "controller"    => "user",
    "action"        => "page"
]);
```

もし第3引数で同名のパラメータが指定されている場合は、第2引数の値が優先されます。

#### ShortRegexの使用

IPアドレスにマッチするルーティングルールを追加するのに、
その正規表現を`RouteCollector::addRoute()`に記述する人はいないでしょう。
さらにそれがIPv4射影IPv6アドレスだった場合、
ルーティングの時点でIPv4に変換できると便利です。

ShortRegexはそれらを可能にします。

自然数だけに一致しINT型に変換するShortRegexは以下の通りです。

```php
Fratily\Router\Dispatcher::addShortRegex("d",
    new class implements Fratily\Router\ShortRegexInterface{
        public function match(string $segment): bool{
            return (bool)preg_match("/\A[1-9][0-9]*\z/", $segment);
        }

        public function convert(string $segment){
            return (int)$segment;
        }
    }
);

$collector->addroute("GET", "/users/{uid|d}/", [
    "controller"    => "user",
    "action"        => "page"
]);
```

ShortRegexは`Dispatcher::addShortRegex()`で登録できます。

第1引数は修飾名。  
第2引数は`Fratily\Router\ShortRegexInterface`を実装したクラスのインスタンス。

#### グループ

ここまでの例で、いくつかの例に共通するパーツがありました。

user に関連するルールを例にとるとそれらのURIの先頭は必ず /users/になり、
controllerの値は userになっていました。

前の例程度では共通パーツを記述することはそれほど苦にはなりません。
しかし実際にはもっと多くのルールが必要になります。

共通のルールを定義するために`RouteCollector::addGroup()`で
複数のルールをグループ化することができます。

```php
$collector->addGroup("/users", function($collector){
    $collector->get("/", [
        "controller"    => "user",
        "action"        => "index"
    ]);

    $collector->addGroup(["Controller" => "user"], function($collector){
        $r->get("/my/", [
            "action"    => "mypage"
        ]);

        $r->get("/{uid:[1-9][0-9]*}/", [
            "action"    => "page"
        ]);
    });
});
```

グループ化による共通ルールは、
第二引数のコールバック関数が実行される間だけ適用されます。

第一引数が文字列の場合はURLの先頭への追加。配列の場合はパラメータへの追加です。

ここで共通化されたパラメータは最も優先度が低く、
ほかの定義に上書きされうることに注意してください。

### 返り値

`Dispatcher::dispatch()`は配列を返します。

インデックス0はルーティング結果が格納されており、`Dispatcher::NOT_FOUND`,
`Dispatcher::METHOD_NOT_ALLOWED`もしくは`Dispatcher::FOUND`の値をとります。

インデックス1はパラメータリストが格納されており、正規表現などでルート内に
埋め込んだパラメータ名をキーとする連想配列をとります。

インデックス2はルートデータが格納されており、ルート定義時に第3引数で指定した値と
グループ化で定義したデータのマージした結果をとります。

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
Fratily\Router\Dispatcher::addShortRegex("d", new NaturalNumber());

$collector->get("/users/{id:[1-9][0-9]*}/", []);
$collector->get("/users/{id|d}/profile/", []);

$dispatcher->dispatch("GET", "/users/123/");
$dispatcher->dispatch("GET", "/users/123/profile/");
```

Fratily/Routerではこの問題が解決され、どちらのルールも正しくマッチします。

> 正常に動作しなかった理由はshortRegexと通常の正規表現の一致確認の順番でした。