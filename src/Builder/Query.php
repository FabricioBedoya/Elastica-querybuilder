<?php

namespace O2\QueryBuilder\Builder;

use O2\QueryBuilder\Builder\QueryInterface;

class Query {
    
    const QUERY_FIELD_ALL = '_all';
    
    protected $field;
    
    protected $keyword;
    
    protected $query = array();
    
    /**
     * 
     * @param string $field
     * @param string $keyword
     */
    public function __construct($field = null, $keyword = null) {
        if ($field !== null) {
            $this->field = $field;
        }
        if ($keyword !== null) {
            $this->keyword = $keyword;
        }
    }
    
    function getField() {
        return $this->field;
    }

    function getKeyword() {
        return $this->keyword;
    }

        
    public function updateFromArray(array $parameters) {
        if (array_key_exists('field', $parameters)) {
            $this->field = $parameters['field'];
        }
        if (array_key_exists('value', $parameters)) {
            $this->keyword = $parameters['value'];
        }
        return $this;
    }
    
    /**
     * 
     * @param array $query
     * @param string $keyword
     * @param string $field
     * @return array
     */
    public  function getQuery(array $query = array(), $field = null, $keyword = null) {
        if ($keyword !== null) {
            $this->setKeyword($keyword);
        }
        if ($field !== null) {
            $this->setField($field);
        }
        $value = $this->getKeyword();
        if (empty($this->query)) {
            switch(true){
                case $this->getKeyword() === null :
                default :
                    $this->query = array('match_all' => array());
                    break;
                case $this->getKeyword() !== null && ($this->getField() === null || $this->getField() == self::QUERY_FIELD_ALL):
                    $this->query = array('match' => array(
                                            '_all' => array(
                                                'query' => $value,
                                                'operator' => 'and'
                                            )
                                        )
                                    ); 
                    break;
                case $this->getKeyword() !== null && ($this->getField() !== null && $this->getField() != self::QUERY_FIELD_ALL):
                    $this->query = array('term' => array(
                       $this->getField() => array('value' => $value),
                    ));
                    break;
            }
        }
        return $this->query;
    }


}
