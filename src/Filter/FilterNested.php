<?php

namespace Fafas\QueryBuilder2\Filter;

use Fafas\QueryBuilder2\Filter\FilterInterface;
use Fafas\QueryBuilder2\Elastica\EntityInterface;

class FilterNested extends AbstractFilter {

    protected $field;
    protected $strategy;
    protected $value;
    protected $parent;
    protected $condition;
    
    /**
     *
     * @var ArrayObject
     */
    protected $filters = null;

    public function __construct($filters = array(), $condition = 'should', $field = null, $value = null) {
        $this->condition = $condition;
        if ($field !== null) {
            $this->field = $field;
        }
        if ($value !== null) {
            $this->value = $value;
        }
        foreach ($filters as $key => $filter) {
            if ($filter instanceof FilterInterface) {
                $this->addFilterStrategy($key, $filter);
            }
        }
    }
    
    /**
     * 
     * @param type $key
     * @param FilterInterface $filter
     */
    public function addFilterStrategy($key, FilterInterface $filter) {
        $this->filters[$key] = $filter;
    }
    
    /**
     * 
     * @param type $nameFilter
     * @return type
     * @throws \Exception
     */
    private function getFilterStrategy($nameFilter) {
        if (!array_key_exists($nameFilter, $this->filters)) {
            throw new \Exception(sprintf('Filter %s not found', $nameFilter));
        }
        $filter = clone $this->filters[$nameFilter];
        return $filter;
    }

    public function updateFromArray(array $parameters) {
        $this->condition = key($parameters);
        $this->strategy = key($parameters[$this->condition]['nested']);
        $this->field = key($parameters[$this->condition]['nested'][$this->strategy]);
        $parts = explode('.', $this->field);
        array_pop($parts);
        $this->parent = implode('.', $parts);
        $this->value = $parameters[$this->condition]['nested'][$this->strategy][$this->field];
    }

    public function getFilterAsArray() {
        $filterStragety = $this->getFilterStrategy($this->strategy);
        $filterStragety->updateFromArray(array($this->field => $this->value));
        $subFilter = $filterStragety->getFilter();
        return $filter = array(
          'nested' => array(
            'path' => $this->parent,
            'filter' => array(
              'bool' => array(
                $this->condition => array(
                  0 => $subFilter
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
