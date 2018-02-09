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

    const RAW   = 1;
    const REG   = 2;
    const SREG  = 3;
    
    const REG_SEG   = "/\A([A-Z_][0-9A-Z_]*)?(:|\|)(.+?)\z/x";
    
    /**
     * @var string
     */
    private $groupPrefix    = "";
    
    /**
     * @var mixed[]
     */
    private $groupData      = [];
    
    /**
     * 静的ルート定義に対するデータリスト
     * 
     * [
     *      GET     => [
     *          "/foo/bar/baz"  => [data],
     *          ...
     *      ],
     *      POST    => [
     *          ...
     *      ],
     *      ...
     * ]
     * 
     * @var mixed[][][]
     */
    private $static     = [];
    
    /**
     * 動的ルート定義に対するデータリスト
     * 
     * [
     *      GET     => [
     *          "ac14f,c61ea"   => [data],
     *          ...
     *      ],
     *      POST    => [
     *          ...
     *      ],
     *      ...
     * ]
     * 
     * @var mixed[][][]
     */
    private $dynamic    = [];
    
    /**
     * セグメントごとのデータ
     * 
     * [
     *      nodeID   => [
     *          rule    => ruleID,
     *          name    => parameterName,
     *          child   => [
     *              parentNodeId    => [
     *                  childNodeId,
     *                  ...
     *              ],
     *              parentNodeId    => [
     *                  ...
     *              ],
     *              ...
     *          ]
     *      ],
     *      ...
     * ]
     * 
     * @var mixed[][]
     */
    private $node   = [];
    
    /**
     * 一致確認用ルールリスト
     * 
     * [
     *      ruleID   => [
     *          type    => self::RAW || self:REG || self::SREG,
     *          match   => 一致確認用の文字列(正規表現やSREGネーム等)
     *      ],
     *      ...
     * ]
     * 
     * @var mixed[]
     */
    private $rule   = [];
    
    /**
     * ルート定義に対するルート名の対応リスト
     * 
     * [
     *      static  => [
     *          
     *      ]
     * ]
     * 
     * @var string[]
     */
    private $name   = [];
    
    /**
     * ルート名に対するルート定義の対応リスト
     * 
     * @var string[]
     */
    private $route  = [];
    
    /**
     * 新しいノードを追加する
     * 
     * @param   int $ruleId
     *      ルールのID
     * @param   string  $name
     *      このセグメントのパラメータ名
     * @param   string  $methods
     *      このノードで受容するHTTPメソッドリスト
     * @param   string  $parent
     *      親ノードID
     * @param    $data
     *      このノードで終了する場合のルートデータ
     */
    protected function addNode(
        int $ruleId,
        string $name,
        array $methods,
        string $parent = null,
        array $data = null
    ){
    }
    
    protected function addRule(){}
}