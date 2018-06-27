# Fratily Router

Fratily Routerは実行速度がルーティングルールの数に依存しないURLルーターです。

ルーティングだけでなくリバースルーティングを行うこともできます。

> **注意**  
> この文章中ではネームスペースを省略しています。

## インストール

``` bash
$ composer require 'fratily/router'
```

## ルーティングルール定義

ルーティングを行うにはまずルーティングルールを定義する必要があります。

ルーティングルールを定義するには、
RouteクラスのインスタンスをRouteCollectorクラスのインスタンスに`RouteCollector::add()`で
登録する必要があります。

もっとも単純な定義方法は以下の通りです。

```php
$routes = new RouteCollector();

$routes
    ->add(Route::newInstance(
        "/authorizations",  // 一致するURLパス
        "*",                // 一致するホスト名
        ["GET"]             // 一致するHTTPメソッド
        ["any", "value"],   // 任意の値
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