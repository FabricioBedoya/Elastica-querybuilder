<?php
namespace Fafas\QueryBuilder\Builder;

use Fafas\QueryBuilder\Elastica\EntityInterface;
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
class ManagerAbstract implements ManagerInterface {
    
    public $patternClass = 'O2\\QueryBuilder\\Builder\\';
    public $patternFile = 'Builder';
    
    protected static $strategyKeys = array('test');
    
    public $strategy = array();
    
    protected static $instance = null;
    
    /**
     * 
     * @return \Fafas\QueryBuilder\Builder\ManagerInterface
     */
    public static function createInstance() {
        if (static::$instance === null) {
            static::$instance = new static();
            static::$instance->autoloadStrategies();
        }
        return static::$instance;
    }
    
    public function getStrategyKeys() {
        return static::$strategyKeys;
    }
    
    /**
     * 
     * @param type $name
     * @param \Fafas\QueryBuilder\Elastica\EntityInterface $queryStrategy
     */
    public function addQueryStrategy($name, EntityInterface $queryStrategy) {
        $this->strategy[$name] = $queryStrategy;
    }
    
    /**
     * 
     * @param string $name
     * @return \Fafas\QueryBuilder\Elastica\EntityInterface
     * @throws \Exception
     */
    public function getQueryStrategy($name) {
        if (!array_key_exists($name, $this->strategy)) {
            return false;
        }
        $strategy = clone $this->strategy[$name];
        return $strategy;
    }
    
    public function getFolder() {
        return dirname(__FILE__);
    }

    /**
     * Function to autoload all strategies in folder Filter/
     */
    public function autoloadStrategies() {
        $folder = $this->getFolder();
        $handle = opendir($folder);
        if ($handle !== false) {
            while (false !== ($entry = readdir($handle))) {
                if (!preg_match('/manager/i', $entry) && !preg_match('/interface/i', $entry)) {
                    if (preg_match('/([a-z|A-Z]+)\.php/', $entry, $matches)) {
                        $class = $this->patternClass . $matches[1];
                        $patternFile = '/'.$this->patternFile.'([a-z|A-Z]+)\.php/';
                        if (preg_match($patternFile, $entry)) {
                            $queryStrategy = new $class($this);
                            if (is_array(class_implements($queryStrategy)) 
                                && array_key_exists('Fafas\QueryBuilder\Elastica\EntityInterface', class_implements($queryStrategy))) {
                                try {
                                    foreach($queryStrategy->getStrategyKeys() as $nomStrategy) { 
                                        $this->addQueryStrategy($nomStrategy, $queryStrategy);
                                    }
                                } catch (Exception $e) {
                                    throw new \Exception(sprintf('An unexpected error has been found adding %s class of file %s', $class, $entry), 0, $e);
                                }
                            }
                            
                        }
                    }
                }
            }
            closedir($handle);
        }
    }
}
