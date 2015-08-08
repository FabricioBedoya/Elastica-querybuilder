<?php

namespace Fafas\ElasticaQuery\Filter;

class FilterGeoDistance extends AbstractFilter {
    
    const GEO_DISTANCE = 'geo_distance';
    const FIELD = 'field';
    const DISTANCE = 'distance';
    const POINT = 'point';
    const LAT = 'lat';
    const LON = 'lon';
    
    protected $options = array();
    
    public static $strategyKeys = array(
      self::GEO_DISTANCE,
    );
    
    /**
     * 
     * @param array $array
     * @return boolean
     */
    public function updateFromArray(array $array) {
        parent::updateFromArray($array);
        foreach(array(static::FIELD, static::DISTANCE, static::LAT, static::LON) as $key) {
            if (isset($array[$key])) {
                $this->options[$key] = $array[$key];
            }
        }
        if (empty($this->options[static::FIELD]) && !empty($this->options[static::DISTANCE])) {
            foreach(array_keys($array) as $key) {
                if ($key !== static::DISTANCE) {
                    if (!isset($this->options[static::FIELD])) {
                        $this->options[static::FIELD] = $key;
                    }
                    switch(true) {
                        case is_string($array[$key]):
                            $this->options[static::POINT] = $array[$key];
                            break;
                        case is_array($array[$key]):
                            foreach($array[$key] as $id => $value) {
                                switch(true) {
                                    case ($id == static::LAT):
                                        $this->options[static::LAT] = $value;
                                        break;
                                    case ($id == static::LON):
                                        $this->options[static::LON] = $value;
                                        break;
                                    default:
                                        if (empty($this->options[static::LAT])) {
                                            $this->options[static::LAT] = $value;
                                        }
                                        else {
                                            $this->options[static::LON] = $value;
                                        }
                                        break;
                                }
                            }
                            break;
                    }
                }
            }
        } 
        if (FilterNested::isNested($this->options[static::FIELD]) && !$this->skipNested) {
            $this->generateNested($this, FilterNested::getParent($this->options[static::FIELD]));
        }
    }
    
    /**
     * 
     * @param array $query
     * @return array
     */
    public function getFilterAsArray() {
        if ($this->getFilterNested() !== null) {
            return $this->getFilterNested()
                    ->getFilterAsArray();
        } else {
            $query = array();
            $query[static::GEO_DISTANCE] = array(
              static::DISTANCE => $this->options[static::DISTANCE],
              );
            if (isset($this->options[static::POINT])) {
                $query[static::GEO_DISTANCE][$this->options[static::FIELD]] = $this->options[static::POINT];
            }
            else {
                $query[static::GEO_DISTANCE][$this->options[static::FIELD]] = array(
                    static::LAT => $this->options[static::LAT],
                    static::LON => $this->options[static::LON],
                );
            }
            return $query;
        }
    }
    
}
