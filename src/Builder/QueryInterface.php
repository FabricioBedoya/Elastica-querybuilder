<?php

namespace Fafas\ElasticaQuery\Builder;

interface QueryInterface {
    
    public function getField();
    
    public function setField($field);
    
    public function getKeyword();
    
    public function setKeyword($keyword);
    
}
