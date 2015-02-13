<?php

namespace O2\QueryBuilder\Filter;

use O2\QueryBuilder\Filter\FilterInterface;

class FilterNested implements FilterInterface {

    protected $field;
    protected $value;
    protected $parent;
    protected $condition;

    public function __construct($condition = 'should', $field = null, $value = null) {
        $this->condition = $condition;
        if ($field !== null) {
            $this->field = $field;
        }
        if ($value !== null) {
            $this->value = $value;
        }
    }

    public function updateFromArray(array $parameters) {
        $this->condition = key($parameters);
        $this->field = key($parameters[$this->condition]);
        $parts = explode('.', $this->field);
        array_pop($parts);
        $this->parent = implode('.', $parts);
        $this->value = $parameters[$this->condition][$this->field];
    }

    public function getFilter() {
        return $filter = array(
          'nested' => array(
            'path' => $this->parent,
            'filter' => array(
              'bool' => array(
                $this->condition => array(
                  0 => array(
                    'term' => array(
                      $this->field => $this->value,
                    ),
                  ),
                ),
              ),
            ),
          ),
        );
    }
    
    public function __clone() {
        $this->field = null;
        $this->value = null;
        $this->condition = null;
        $this->parent = null;
    }

}
