<?php
/**
 * FratilyPHP Router
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento-oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Router\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 * @Attributes({
 *   @Attribute("path", type = "string"),
 *   @Attribute("host", type = "string"),
 *   @Attribute("methods", type = "array<string>"),
 *   @Attribute("name", type = "string"),
 * })
 */
class Route{

    const ALLOW_PARAMETER   = [
        "path"      => true,
        "host"      => false,
        "methods"   => false,
        "name"      => false,
    ];

    /**
     * @var static|null
     */
    private $parent;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $host     = null;

    /**
     * @var string[]
     */
    protected $methods  = null;

    /**
     * @var string|null
     */
    protected $name     = null;

    /**
     * Constructor
     *
     * @param   mixed[] $data
     *  アノテーションで定義されたデータの連想配列
     */
    public function __construct(array $data){
        $class  = static::class;
        $keys   = self::ALLOW_PARAMETER;

        foreach($data as $key => $value){
            if(!array_key_exists($key, $keys)){

                throw new Exception\UnknownFieldException(
                    "Unknown field '{$key}' on annotation '{$class}'."
                );
            }

            unset($keys[$key]);

            $this->$key = $value;
        }

        $requireKeys    = array_keys(array_filter($keys, function($v){return $v;}));
        $requireKeysStr = implode(", ", $requireKeys);

        if(1 === count($requireKeys)){
            throw new Exception\RequireFieldMissingException(
                "Require field '{$requireKeysStr}' is missing on annotation '{$class}'"
            );
        }elseif(1 < count($requireKeys)){
            throw new Exception\RequireFieldMissingException(
                "Require fields '{$requireKeysStr}' is missing on annotation '{$class}'"
            );
        }
    }

    /**
     * 親アノテーションを取得する
     *
     * @return  Route|null
     */
    public function getParent(){
        return $this->parent;
    }

    /**
     * 親アノテーションを登録する
     *
     * @param   Route   $parent
     *  親アノテーションクラスのインスタンス
     *
     * @return  void
     */
    public function setParent(Route $parent){
        $this->parent   = $parent;
    }

    /**
     * 一致パスを取得する
     *
     * @return  string
     */
    public function getPath(){
        return
            (null === $this->getParent() ? "" : $this->getParent()->getPath())
            . $this->path
        ;
    }

    /**
     * 許容ホストワイルドカードを取得する
     *
     * @return  string
     */
    public function getHost(){
        return
            $this->host
            ?? (null === $this->getParent() ? null : $this->getParent()->getHost())
            ?? "*"
        ;
    }

    /**
     * 許容メソッドリストを取得する
     *
     * @return  string[]
     */
    public function getMethods(){
        return
            $this->methods
            ?? (null === $this->getParent() ? null : $this->getParent()->getMethods())
            ?? ["GET"]
        ;
    }

    /**
     * ルート名を取得する
     *
     * @return  string|null
     */
    public function getName(){
        return $this->name;
    }

    /**
     * ルートインスタンスを生成する
     *
     * @return  \Fratily\Router\Route
     */
    public function cerateRoute(){
        $route  = \Fratily\Router\Route::newInstance(
            $this->getPath(),
            $this->getHost(),
            $this->getMethods()
        );

        if(null !== $this->getName()){
            $route  = $route->withName($this->getName());
        }

        return $route;
    }
}