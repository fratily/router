<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento.oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\RouterTest;

use Fratily\Router\Dispatcher;
use Fratily\Router\RouteCollector;
use Fratily\Router\ShortRegexInterface;

/**
 *
 */
class DispatcherTest extends \PHPUnit\Framework\TestCase{
    
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    
    public function setup(){
        $collection = new RouteCollector();
        
        $collection->addRoute("GET", "multi/method/", []);
        $collection->addRoute("POST", "multi/method/", []);
        
        $collection->addRoute(["GET"], "", ["name" => "root"]);
        
        $collection->addGroup("users/", function($c){
            $c->addGroup(["name" => "user"], function($c){
                $c->get("my/", [
                    "name" => "mypage"
                ]);

                $c->addGroup("{uid|d}/", function($c){
                    $c->get("");
                    $c->get("{page:[1-9][0-9]*}/", ["name" => "userindex"]);
                });
            });
        });
        
        $collection->get("/users/{uid:[1-9][0-9]*}/profile/", ["name" => "profile"]);
        
        $this->dispatcher   = new Dispatcher($collection);
        
        Dispatcher::addShortRegex(
            "d",
            new class implements ShortRegexInterface{
                public function match(string $segment): bool{
                    return (bool)preg_match("/\A[1-9][0-9]*\z/", $segment);
                }

                public function convert(string $segment){
                    return (int)$segment;
                }
            }
        );
    }

    /**
     * ShortRegexの登録から削除までの正常な動作のテスト
     */
    public function testShortRegex(){
        $shortRegex = $this->createMock(ShortRegexInterface::class);

        Dispatcher::addShortRegex("defined", $shortRegex);

        //  'defined'は登録済みだからtrueを返す
        $this->assertEquals(true, Dispatcher::hasShortRegex("defined"));

        //  'undefined'は未登録だからfalseを返す
        $this->assertEquals(false, Dispatcher::hasShortRegex("undefined"));

        //  登録したものは内部で置き換えられたりしないから同じオブジェクト
        $this->assertSame($shortRegex, Dispatcher::getShortRegex("defined"));

        Dispatcher::removeShortRegex("defined");

        //  登録抹消したものは存在しないことになる
        $this->assertEquals(false, Dispatcher::hasShortRegex("defined"));

        Dispatcher::addShortRegex("defined", $shortRegex);

        //  再度登録したからtrueを返す
        $this->assertEquals(true, Dispatcher::hasShortRegex("defined"));
        
        Dispatcher::removeShortRegex("defined");
    }

    /**
     * 登録されていないShortRegexの取得テスト
     *
     * @expectedException   \InvalidArgumentException
     */
    public function testGetUndefineShortRegex(){
        Dispatcher::getShortRegex("undefined");
    }

    /**
     * 登録されていないShortRegexの削除テスト
     *
     * @expectedException   \InvalidArgumentException
     */
    public function testRemoveUndefinedShortRegex(){
        Dispatcher::removeShortRegex("undefined");
    }
    
    public function testConfrictRoute(){
        $this->assertEquals(Dispatcher::FOUND, $this->dispatcher->dispatch("GET", "/users/123/")[0]);
        $this->assertEquals(Dispatcher::FOUND, $this->dispatcher->dispatch("GET", "/users/123/profile/")[0]);
    }
    
    /**
     * @dataProvider provideDispatchTestcase
     */
    public function testDispatch($method, $url, $expected){
        $result = $this->dispatcher->dispatch($method, $url);
        $this->assertEquals($expected[0], $result[0]);
        $this->assertEquals($expected[1], $result[1]);
    }
    
    public function provideDispatchTestcase(){
        return [
            ["GET", "/", [Dispatcher::FOUND,["name" => "root"]]],
            ["HEAD", "/", [Dispatcher::FOUND, ["name" => "root"]]],
            ["GET", "", [Dispatcher::FOUND, ["name" => "root"]]],
            ["HEAD", "", [Dispatcher::FOUND, ["name" => "root"]]],
            ["GET", "multi/method/", [Dispatcher::FOUND, []]],
            ["POST", "multi/method/", [Dispatcher::FOUND, []]],
            ["get", "users/my/", [Dispatcher::FOUND, ["name" => "mypage"]]],
            ["get", "users/123/", [Dispatcher::FOUND, ["uid" => 123, "name" => "user"]]]
        ];
    }
}