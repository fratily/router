<?php
include __DIR__ . "/../vendor/autoload.php";

class NumSegment implements \Fratily\Router\SegmentInterface
{
    public function getName(): string
    {
        return "num";
    }

    public function match(string $segment): bool
    {
        return false !== filter_var($segment, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    }
}

class AnySegment implements \Fratily\Router\SegmentInterface
{
    public function getName(): string
    {
        return "any";
    }

    public function match(string $segment): bool
    {
        return "" !== $segment;
    }
}

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

$segmentManager->addSegment(new NumSegment())->addSegment(new AnySegment(), true);

$router = new \Fratily\Router\Router($collector, $segmentManager);

var_dump($router->match(\Fratily\Router\Route::GET, "/users/123/books/abcd"));
