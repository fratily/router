# Fratily Router

`fratily/router` is url routing library.
Can also do reverse routing as well as routing.

## Install

``` bash
$ composer require fratily/router
```

## Usage

```php
$collector = new \Fratily\Router\RouteCollector();
$segmentManager = new \Fratily\Router\SegmentManager();

$collector
    ->get("users", "/users")
    ->get("user", "/users/:id@num")
    ->get("user_books", "users/:id@num/books")
    ->get("user_book", "users/:id@num/books/:title")
    ->post("user_book_edit", "users/:id@num/books/:title")
    ->put("user_book_new", "users/:id@num/books")
    ->delete("user_book_delete", "users/:id@num/books/:title")
;

$segmentManager
    ->addSegment(new \Fratily\Router\Segments\NumSegment()) // `num`
    ->addSegment(new \Fratily\Router\Segments\AnySegment(), true) // `any` default rule
;

$router = new \Fratily\Router\Router($collector, $segmentManager);

$router->match(\Fratily\Router\Route::GET, "/users/123/books/abcd");
$router->reverseRoute(
    "user_book",
    [
        "id" => 123,
        "title" => "abcd",
        "a" => "456",
        "b" => ["e", "f", "g"]
    ],
    true
);
```
