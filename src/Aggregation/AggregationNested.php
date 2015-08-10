<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\ElasticaQuery\Aggregation;

class AggregationNested extends AbstractAggregation {
    
    const NESTED = 'nested';
    
    const FILTER = 'filter';
    const PATH = 'path';
    const AGGS = 'aggs';
    const SCORE_MODE = 'score_mode';
    
    public static $strategyKeys = array(
      self::NESTED,
    );
    
    protected $isGlobal = true,
        $name = null,
        $filter = null,
        $field = null,
        $size = 0,
        $order = null,
        $aggs = null,
        $path = null;
    
    /**
     * 
     * @param string $field
     * @return boolean
     */
    public static function isNested($field = '') {
        return (strpos($field, '.') ? true : false);
    }
    
    /**
     * 
     * @param string $field
     * @return string
     */
    public static function getParent($field) {
        $parts = explode('.', $field);
        array_pop($parts);
        return implode('.', $parts);
    }

    public function getFilterAsArray() {
        $nested = array(
            static::AGGS => array(),
            static::FILTER => array(),
        );
        $filter = null;
        if ($this->filter !== null) {
            $idFilterExcluded = array($this->getId());
            $filter = $this->getFilter()
                ->getMandatoryBoolButId($idFilterExcluded)
                ->getFilterAsArray();
        }
        if ($filter !== null) {
            $nested[static::FILTER] = $filter;
        }
        if (isset($this->options[self::PATH])) {
            $nested[static::AGGS][$this->getId()] = array(
                static::NESTED => array(
                  self::PATH => $this->options[self::PATH])
                );
        }
        if (isset($this->options[self::AGGS])) {
            $nested[static::AGGS][$this->getId()][static::AGGS] = array($this->getId() => $this->options[self::AGGS]);
        }
        return $nested;
    }
    
    /**
     * Update AggregationTerms from Array
     * @param array $array
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        foreach(array(self::PATH, self::AGGS) as $key)  {
            if (isset($array[$key])) {
                $this->options[$key] = $array[$key];
            }
        }
    }

}
