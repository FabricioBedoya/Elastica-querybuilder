<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder\Query;

use O2\QueryBuilder\Filter\FilterConstants;
use O2\QueryBuilder\Filter\FilterInterface;

class QueryMatchAll implements FilterInterface {
    
    const MATCH_ALL = 'match_all';
    
    protected static $defaultOptions = array(
    );
    
    protected $options = array();
    
    /**
     * 
     * @param array $options
     */
    public function __construct(array $options = array()) {
        if (!empty($options)) {
            $this->options = $options;
        }
    }
    
    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        $match_all = array(
          static::MATCH_ALL => (object) array()
            );
        if (isset($array[FilterConstants::BOOST])) {
            $match_all = array(static::MATCH_ALL => array(
              FilterConstants::BOOST => $array[FilterConstants::BOOST],
            ));
        }
        return $match_all;
    }
    
    public function getFilter() {
        return $this->getFilterAsArray();
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        if (isset($array[FilterConstants::BOOST])) {
            $this->options[FilterConstants::BOOST] = $array[FilterConstants::BOOST];
        }
    }


}
