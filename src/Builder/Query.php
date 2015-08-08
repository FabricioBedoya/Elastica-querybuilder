<?php

namespace Fafas\ElasticaQuery\Builder;

use Fafas\ElasticaQuery\Builder\QueryInterface;

class Query {

    const QUERY_FIELD_ALL = '_all';
    const QUERY_LANG = 'lang';
    const QUERY_OPTION_FICHE = 'rich_content';
    
    const QUERY_FIELD_FR = 'all_french';
    const QUERY_FIELD_EN = 'all_english';
    const LANG_FR = 'fr';
    const LANG_EN = 'en';    
    const QUERY_SLOP = 5;

    protected $field;
    protected $keyword;
    protected $query = array();
    protected $options = array(
      self::QUERY_LANG => self::LANG_FR,
      self::QUERY_OPTION_FICHE => true,
    );

    /**
     * 
     * @param string $field
     * @param string $keyword
     */
    public function __construct($field = null, $keyword = null, array $options = array()) {
        if ($field !== null) {
            $this->field = $field;
        }
        if ($keyword !== null) {
            $this->keyword = $keyword;
        }
        $this->options = array_merge($this->options, $options);        
    }

    /**
     * 
     * @param type $key
     * @param type $value
     */
    public function setOption($key, $value = null) {
        $this->options[$key] = $value;
    }

    /**
     * 
     * @param type $key
     * @return type
     */
    public function getOption($key) {
        return $this->options[$key];
    }

    /**
     * 
     * @return type
     */
    function getField() {
        return $this->field;
    }

    /**
     * 
     * @return type
     */
    function getKeyword() {
        return $this->keyword;
    }

    /**
     * 
     * @param array $parameters
     * @return \Fafas\ElasticaQuery\Builder\Query
     */
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
                    if ($this->getOption(self::QUERY_OPTION_FICHE) === true) {
                        $this->query = array(
                          'bool' => array(
                            'must'=> array(
                              'match_all' => (object) array(),
                            ),
                            'should' => self::boostWidgetContent(),
                          )
                          );
                    }
                    else {
                        $this->query = array('match_all' => (object) array());
                    }
                    break;
                case $this->getKeyword() !== null && ($this->getField() === null || $this->getField() == self::QUERY_FIELD_ALL):
                    if ($this->getOption(self::QUERY_OPTION_FICHE) === true) {
                        $this->query = static::improveedQueryRichContent($value, $this->getOption(self::QUERY_LANG));
                    } else {
                        $this->query = static::queryAll($value);
                    }
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

    private static function queryAll($value) {
        return array('bool' =>
          array(
            'must' => array(
              'match_phrase' => array(
                '_all' => array(
                  'query' => $value,
                  'slop' => self::QUERY_SLOP,
                )
              )
            ),
          )
        );
    }

    /**
     * 
     * @param string $value
     * @param string $lang
     * @return array
     */
    private static function improveedQueryRichContent($value, $lang = self::LANG_FR) {
        $field = $lang == self::LANG_FR ? self::QUERY_FIELD_FR : self::QUERY_FIELD_EN;
        return array('bool' =>
          array(
            'must' => array(
              'multi_match' => array(
                'query' => $value,
                'fields' => array('ETBL_NOM_'.strtoupper($lang).'^10',$field),
                'type' => 'phrase',
                'slop' => self::QUERY_SLOP,
              )
            ),
            'should' => self::boostRichContent(),
          )
        );
    }
    
    /**
     * 
     * @return array
     */
    private static function boostRichContent() {
        return array(
          0 => array('term' => array(
              'ETBL_RESERVABLE' => array(
                'value' => 1,
                'boost' => 3,
              )
            )
          ),
          1 => array('nested' => array(
              'path' => 'MULTIMEDIAS',
              'query' => array(
                'terms' => array(
                  'MUL_GENRE_ID' => array(
                    0 => 7148165,
                    1 => 179105281,
                  )
                )
              ),
              'boost' => 5,
            )
          ),
          2 => array('nested' => array(
              'path' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS',
              'query' => array(
                'term' => array(
                  'CARACT_ATTRB_ID' => array(
                    'value' => 210241848,
                  )
                )
              ),
              'boost' => 2,
            )
          ),
          3 => array('nested' => array(
              'path' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS',
              'query' => array(
                'term' => array(
                  'CARACT_ATTRB_ID' => array(
                    'value' => 373620213,
                  )
                )
              ),
              'boost' => 1.5,
            )
          ),
          4 => array('nested' => array(
              'path' => 'CARACTERISTIQUES.CARACT_ATTRIBUTS',
              'query' => array(
                'term' => array(
                  'CARACT_ATTRB_ID' => array(
                    'value' => 373985076,
                  )
                )
              ),
            )
          )
        );
    }
    
    /**
     * 
     * @return array
     */
    private static function boostWidgetContent() {
        return array(
          0 => array('nested' => array(
              'path' => 'MULTIMEDIAS',
              'query' => array(
                'terms' => array(
                  'MUL_GENRE_ID' => array(
                    0 => 7148165,
                    1 => 179105281,
                  )
                )
              ),
              'boost' => 5,
            )
          ),
        );
    }

}
