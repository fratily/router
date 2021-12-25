<?php

namespace Fratily\Tests\Router\Router;

use Fratily\Router\Route;
use Fratily\Router\Router;
use Fratily\Router\RouterBuilder;
use PHPUnit\Framework\TestCase;

class MatchTest extends TestCase
{
    /**
     * @dataProvider dataProviderRouting
     */
    public function testRouting(
        Router $router,
        string $path,
        ?string $expectedRoutePath,
        array $expectedParams
    ): void
    {
        $result = $router->match($path);

        self::assertIsArray($result);
        self::assertArrayHasKey('route', $result);
        self::assertArrayHasKey('params', $result);

        if ($expectedRoutePath === null) {
            self::assertNull($result['route']);
        } else {
            self::assertInstanceOf(Route::class, $result['route']);
            self::assertSame($expectedRoutePath, $result['route']->getPath());
        }

        self::assertIsArray($result['params']);
        self::assertSame($expectedParams, $result['params']);
    }

    /**
     * @return array[]
     * @phpstan-return iterable<array{Router,string,string|null,array<mixed>}>
     */
    public function dataProviderRouting(): iterable
    {
        $router = (new RouterBuilder([
            (new Route('/')),
            (new Route('/foo'))->strictCheckTrailing(false),
            (new Route('/bar/'))->strictCheckTrailing(false),
            (new Route('/:param1')),
            (new Route('/:param1/foo/bar/:param2')),
            (new Route('/baz/:param2(\d+)')),
            (new Route('/baz/:param2(\d+)/foo')),
        ]))->build();

        yield from [
            [$router, '', null, []],
            [$router, '/', '/', []],

            [$router, '/foo', '/foo', []],
            [$router, '/foo/', '/foo', []],

            [$router, '/bar', '/bar/', []],
            [$router, '/bar/', '/bar/', []],

            [$router, '/baz/123', '/baz/:param2(\d+)', ['param2' => '123']],
            [$router, '/baz/aaa', null, []],

            [$router, '/baz/123/foo', '/baz/:param2(\d+)/foo', ['param2' => '123']],
            [$router, '/baz/123/foo/', null, []],

            [$router, '/any', '/:param1', ['param1' => 'any']],

            [$router, '/any/foo/bar/any2', '/:param1/foo/bar/:param2', [
                'param1' => 'any',
                'param2' => 'any2'
            ]],
        ];
    }
}
