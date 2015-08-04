<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Query;

class QueryMultiMatch extends AbstractQuery {
    
    const MULTI_MATCH = 'multi_match';
    
    const TYPE = 'type';
    const FIELDS = 'fields';
    const QUERY = 'query';
    const OPERATOR = 'operator';
    const ANALIZER = 'analyzer';
    
    protected static $strategyKeys = array(
      self::MULTI_MATCH
    );
    
    protected $options = array();
    
    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        $this->validateQueryMatch();
        $match = array(
          static::MULTI_MATCH => array(
            static::QUERY => $this->options[static::QUERY],
            static::FIELDS => $this->options[static::FIELDS],
          ),
        );
        foreach(array(static::TYPE, static::OPERATOR, static::ANALIZER) as $option) {
            if (isset($this->options[$option])) {
                $match[static::MULTI_MATCH][$option] = $this->options[$option];
            }
        }
        return $match;
    }
    
    /**
     * 
     * @return array
     */
    public function getFilter() {
        return $this->getFilterAsArray();
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        foreach(array(static::FIELDS, static::TYPE, static::OPERATOR, static::QUERY, static::ANALIZER) as $option) {
            if (isset($array[$option])) {
                $this->options[$option] = $array[$option];
            }
        }
        return $this;
    }
    
    /**
     * 
     * @throws Exception
     */
    private function validateQueryMatch() {
        foreach(array(static::FIELDS, static::QUERY) as $option) {
            if (static::FIELDS === $option && !is_array($this->options[static::FIELDS])) {
                throw new Exception('An error has occurred in QueryMatch: fields must to be an array');
            }
            if (!array_key_exists($option, $this->options)) {
                throw new Exception('An error has occurred in QueryMatch: some options are missing');
            }
        }
    }


}
