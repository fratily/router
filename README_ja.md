# Fratily Router

Fratily Routerは(恐らく)ルーティング数に依存せず、
高速に動作する(はずの)URLルーティングライブラリです。

ルーティングだけでなくリバースルーティングを行うこともできます。

> **注意**  
> 基本的にこの文中ではネームスペースを省略してクラス名を記述します。
> `Fratily\Router`をベースネームスペースとして記述しています。

## インストール

このライブラリをあなたのプロジェクトで使用する前に、以下のコマンドを実行
してください。

``` bash
$ composer require 'fratily/router'
```

## ルート定義方法

```php
$parser = new Parser\StandardParser();
$routes = new RouteCollector();

$routes
    ->add(Route::newInstance(
        $parser,            // URL parser
        "/authorizations",  // Matching path
        ["any", "value"],   // Any value
        "*",                // Matching host
        ["GET"]             // Allow methods
    ))
    ->add(Route::newInstance(
        $parser,
        "/authorizations/{id:[0-9A-Za-z-]+}",
        ["any", "value"],
        "*",
        ["GET"]
    ))
;
```

このルーティングライブラリは`RouteCollector`を中心として成り立っています。

ルートを定義するには`Route`インスタンスを`RouteCollector::add()`で
登録する必要があります。

### 正規表現の埋め込み

`{name:regex}`構文で複数のルートに一致させることができます。

nameはパラメータ名として使用され、ルーティング結果にパラメータリストとして
取得できます。regexはその名の通り正規表現を記述する場所です。

## ルーティング

ルーティングは以下のようにして行います。

```php
$host   = $_SERVER["REQUEST_HOST"];
$method = $_SERVER["REQUEST_METHOD"];
$path   = $_SERVER["REQUEST_URI"];

$router = $routes->router($host, $method);
$result = $router->search($path);

if($result->found === true){
    // found
    $result->params;    // パスに埋め込んだパラメータ
    $result->data;      // Any data
}else{
    // not found
}
```

## リバースルーティング

リバースルーティングは以下のようにして行います。

```php
$reverseRouter  = $c->reverseRouter("route_name");

$path   = $reverseRouter->createPath(["id" => 123]);
```