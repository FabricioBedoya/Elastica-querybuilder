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
    public function getQuery(array $query = array(), $field = null, $keyword = null) {
        if ($keyword !== null) {
            $this->setKeyword($keyword);
        }
        if ($field !== null) {
            $this->setField($field);
        }
        $value = $this->getKeyword();
        if (empty($this->query)) {
            switch (true) {
                case $this->getKeyword() === null :
                default :
                    $this->query = array('match_all' => array());
                    break;
                case $this->getKeyword() !== null && ($this->getField() === null || $this->getField() == self::QUERY_FIELD_ALL):
                    $this->query = array('bool' =>
                      array(
                        'must' => array(
                          'match_phrase' => array(
                            '_all' => array(
                              'query' => $value,
                              // 'fuzziness' => 'AUTO',
                              'slop' => '4',
                            )
                          )
                        ),
//                        'should' => array(
//                          0 => array('term' => array(
//                              'ETBL_RESERVABLE' => array(
//                                'value' => 1,
//                                'boost' => 3,
//                              )
//                            )
//                          ),
//                          1 => array('nested' => array(
//                                'path' => 'MULTIMEDIAS',
//                                'query'=> array(
//                                  'terms'=>array(
//                                    'MUL_GENRE_ID'=> array(
//                                      0 => 7148165,
//                                      1 => 179105281,
//                                    )
//                                  )
//                                ),
//                                'boost' => 2.5,
//                            )
//                          ),
//                          1 => array('nested' => array(
//                                'path' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS',
//                                'query'=> array(
//                                  'term'=>array(
//                                    'CARACT_ATTRB_ID'=> array(
//                                      'value' => 210241848,
//                                    )
//                                  )
//                                ),
//                                'boost' => 2,
//                            )
//                          ),
//                          2 => array('nested' => array(
//                                'path' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS',
//                                'query'=> array(
//                                  'term'=>array(
//                                    'CARACT_ATTRB_ID'=> array(
//                                      'value' => 373620213,
//                                    )
//                                  )
//                                ),
//                                'boost' => 1.5,
//                            )
//                          ),
//                          3 => array('nested' => array(
//                                'path' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS',
//                                'query'=> array(
//                                  'term'=>array(
//                                    'CARACT_ATTRB_ID'=> array(
//                                      'value' => 373985076,
//                                    )
//                                  )
//                                ),
//                            )
//                          )
//                        ),
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
