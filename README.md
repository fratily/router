I am using Google Translate to create this README.
The original is README_ja.

# Fratily Router

The Fratily Router is a URL router whose execution speed does not depend on
the number ob routing rules.

Can also do reverse routing as well as routing.

> **Notice**
> Namespace is omitted in this README.

## Install

``` bash
$ composer require 'fratily/router'
```

## Define routing rule

Need to define routing rule before doing routing.

TO define routing rule, you need to register an instance of the Route class
with ` RouteCollector::add()` as an instance of RouteCollector class.

The simplest definition method is as follows:

```php
$routes = new RouteCollector();

$routes
    ->add(Route::newInstance(
        "/authorizations",  // Matching URL path
        "*",                // Matching host name
        ["GET"]             // Matching HTTP method
        ["any", "value"],   // Any values
    ))
;
```
> `Route::newInstance()`はRouteクラスのコンストラクタのショートカットです。

- ホスト名はワイルドカード構文を用いて指定できます。
- 一致するHTTPメソッドについては、許容するHTTPメソッド名の配列か文字列を指定できます。
- 任意の値とは、ルーティングに成功した際に取得可能な値です。
コントローラクラス名などの情報を格納するとよいでしょう。

### パラメーター

URLパスにコロンから始まるセグメントを含めると、それはパラメーターとして扱われます。

例として第2セグメントにnameというパラメータを埋め込んでみます。

```php
$routes = new RouteCollector();

$routes
    ->add(Route::newInstance(
        "/users/:name",
        "*",
        ["GET"]
        ["any", "value"],
    ))
;
```

パラメータはなにも指定しなければ空文字を除く全ての文字列に一致します。
何に一致するかはパラメータ名の後ろにアットマークから始まるタイプ名を指定することで設定可能です。

数字に一致するセグメントを含めたルーティングルールを登録してみます。

```php
$routes = new RouteCollector();

$routes
    ->add(Route::newInstance(
        "/users/:name/entry/:entry_id@int",
        "*",
        ["GET"]
        ["any", "value"],
    ))
;
```

デフォルトで設定可能なタイプは以下の通りです。

- any
- bool
- int
- octal
- hex
- float

#### any

空文字を除く全ての文字列に一致します。

#### bool

以下の値に一致し、パラメータとして取得する際にはbool型に変換される。

- `0` / `1`
- `false` / `true`
- `off` / `on`
- `no` / `yes`

#### int

整数値に一致し、パラメータとして取得する際にはint型に変換される。

#### octal

0から始まる8進数値に一致し、パラメータとして取得する際にはint型に変換される。

#### hex

0xから始まる16進数値に一致し、パラメータとして取得する際にはint型に変換される。

#### float

実数地に一致し、パラメータとして取得する際にはfloat型に変換される。

#### タイプを追加する

`on / off`にだけ一致するタイプが必要になった場合、タイプを追加することで解決することができます。

タイプの実態は`Segment\Type\TypeInterface`を実装したクラスで、`Segment\Segment::addType()`で追加できます。

詳しくはコードを読んでください。

> 正規表現で一致確認を行うものをデフォルトでは実装していません。
> このレベルの判断は、コントローラー層の入力値バリデーションとともに行うべきと考えたからです。  
> もし正規表現を用いて一致確認をする必要があるのであれば、正規表現ごとにタイプを追加する必要があります。

## ルーティング

ルーティングは以下のようにして行います。

```php
$host   = $_SERVER["HTTP_HOST"];
$method = $_SERVER["REQUEST_METHOD"];
$path   = explode("?", $_SERVER["REQUEST_URI"], 2)[0];

// 指定したホスト名とHTTPメソッドで有効なURLルーターを生成する
$router = $routes->router($host, $method);

// ルーティングを行う
$result = $router->search($path);

if($result->found === true){
    // found
    $result->name;      // ルーティングルール名(後述)
    $result->path;      // ルーティングルール
    $result->params;    // パラメーター名をキーとした連想配列
    $result->data;      // ルートに登録した任意の値
}else{
    // not found
}
```

## リバースルーティング

fratily/routerはリバースルーティング機能も備えています。

これは特に説明することはありません。とても単純な機能だからです。
以下のようにすることで使用できます。

```php
$routes = new RouteCollector();

$routes
    ->add(Route::newInstance(
        "/users/:name/entry/:entry_id@int",
        "*",
        ["GET"]
        ["any", "value"],
    ))->withName("user_entry_detail")
;

$reverseRouter  = $routes->reverseRouter("user_entry_detail");

$path   = $reverseRouter->createPath([
    "name"     => "username",
    "entry_id" => "123",
]);

echo $path;
// display: /users/username/entry/123
```

### ルーティングルールへの名前付け

`Route::withName()`を使用することで、ルーティングルールに名前を付けることができます。

もし名前のないルールをRouteCollectorに登録しようとした場合、自動的に名前が付与されます。