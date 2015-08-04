<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder2\Query;

class QueryMatch extends AbstractQuery {
    
    const MATCH = 'match';
    const TYPE = 'type';
    const FIELD = 'field';
    const QUERY = 'query';
    const OPERATOR = 'operator';
    const ZERO_TERMS_QUERY = 'zero_terms_query';
    const ANALIZER = 'analyzer';
    
    protected static $strategyKeys = array(
      self::MATCH,
    );
    
    protected $options = array();
        
    /**
     * 
     * @return array
     */
    public function getFilterAsArray() {
        $this->validateQueryMatch();
        $match = array(
          static::MATCH => array(
            $this->options[static::FIELD] = array(
              static::QUERY => $this->options[static::QUERY],
            )
          ),
        );
        foreach(array(static::TYPE, static::OPERATOR, static::ANALIZER, static::ZERO_TERMS_QUERY) as $option) {
            if (isset($this->options[$option])) {
                $match[static::MATCH][$this->options[static::FIELD]][$option] = $this->options[$option];
            }
        }
        return $match;
    }

    /**
     * 
     * @param array $array
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        foreach(array(static::FIELD, static::TYPE, static::OPERATOR, static::QUERY, static::ANALIZER, static::ZERO_TERMS_QUERY) as $option) {
            if (isset($array[$option])) {
                $this->options[$option] = $array[$option];
            }
        }
    }
    
    /**
     * 
     * @return string
     */
    private function getMatchType() {
        $type = static::MATCH;
        switch(true) {
            case isset($this->options[static::MATCH_PHRASE]):
                $type = static::MATCH_PHRASE;
                break;
            case isset($this->options[static::MATCH_PHRASE_PREFIX]):
                $type = static::MATCH_PHRASE_PREFIX;
                break;
            default:
                break;
        }
        return $type;
    }
    
    /**
     * 
     * @throws Exception
     */
    private function validateQueryMatch() {
        foreach(array(static::FIELD, static::QUERY) as $option) {
            if (!array_key_exists($option, $this->options)) {
                throw new Exception('An error has occurred in QueryMatch: some options are missing');
            }
        }
    }

}
