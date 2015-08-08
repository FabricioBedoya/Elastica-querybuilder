<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Query;

class QueryMatchAll extends AbstractQuery {
    
    const MATCH_ALL = 'match_all';
    const BOOST = 'boost';
    
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
        if (isset($this->options[static::BOOST])) {
            $match_all = array(static::MATCH_ALL => array(
              static::BOOST => $this->options[static::BOOST],
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
        if (isset($array[static::BOOST])) {
            $this->options[static::BOOST] = $array[static::BOOST];
        }
    }


}
