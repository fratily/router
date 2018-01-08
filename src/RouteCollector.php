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
namespace Fratily\Router;

/**
 *
 */
class RouteCollector{

    /**
     * ルート定義をグループ化する
     *
     * @param   string|mixed[]  $common
     *      stringならばurlの先頭に指定文字列を追加し、arrayならパラメーターの
     *      共通値を設定する。arrayの場合の共通パラメーターは最も優先度が低く、
     *      addRoute()で定義されるパラメーターに上書きされる可能性がある。
     * @param   callable    $callback
     *      このコールバック関数が実行される間だけグループ化が有効となる。
     *      コールバックは第一引数にこのオブジェクトが渡される。
     */
    public function addGroup($common, callable $callback){

    }

    /**
     * ルートを定義する
     *
     * @param   string|string[] $method
     *      一致するメソッド、もしくはそのリスト。
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function addRoute($method, string $url, array $params = []){

    }

    /**
     * addRoute("GET",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function get(string $url, array $params = []){

    }

    /**
     * addRoute("POST",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function post(string $url, array $params = []){

    }

    /**
     * addRoute("PUT",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function put(string $url, array $params = []){

    }

    /**
     * addRoute("PATCH",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function patch(string $url, array $params = []){

    }

    /**
     * addRoute("DELETE",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function delete(string $url, array $params = []){

    }

    /**
     * addRoute("HEAD",)のショートカット
     *
     * @param   string  $url
     *      一致するURLのルール。
     * @param   mixed[] $params
     *      ルールーに一致した場合に返されるパラメーターリスト。
     *      グループパラメーターより優先され、URLルールで定義されたパラメーター
     *      に上書きされうる。
     */
    public function head(string $url, array $params = []){

    }

    /**
     * 正規表現などを使用しないルールのリストを返す
     *
     * @return  mixed
     */
    public function getStatic(){

    }

    /**
     * ルーティングルールの木構造データを返す
     *
     * @return  mixed
     */
    public function getTree(){

    }
}