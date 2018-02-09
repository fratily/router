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
     * 文字列一致だけで一致確認ができるルートリスト
     * 
     * @var mixed[][]
     */
    private $static = [];
    
    /**
     * セグメントごとの一致確認用のデータリスト
     * 
     * @var mixed[][]
     */
    private $node   = [];
    
    /**
     * 一致確認用ルールリスト
     * 
     * @var mixed[][]
     */
    private $rule   = [];
    
    /**
     * ルート定義に対するルート名の対応リスト
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
}