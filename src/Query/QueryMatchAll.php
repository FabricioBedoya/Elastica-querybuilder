<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Query;

class QueryMatchAll extends AbstractQuery {
    
    const MATCH_ALL = 'match_all';
    
    protected static $strategyKeys = array(
      self::MATCH_ALL
    );
    
    protected $options = array();
    
    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        $match_all = array(
          static::MATCH_ALL => (object) array()
            );
        if (isset($this->options[FilterConstants::BOOST])) {
            $match_all = array(static::MATCH_ALL => array(
              FilterConstants::BOOST => $this->options[FilterConstants::BOOST],
            ));
        }
        return $match_all;
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        if (isset($array[FilterConstants::BOOST])) {
            $this->options[FilterConstants::BOOST] = $array[FilterConstants::BOOST];
        }
    }


}
