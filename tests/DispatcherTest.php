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

/**
 *
 */
class DispatcherTest extends \PHPUnit\Framework\TestCase{

    /**
     * ShortRegexの登録から削除までの正常な動作のテスト
     */
    public function testShortRegex(){
        $shortRegex = $this->createMock(\Fratily\Router\ShortRegexInterface::class);

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
}