<?php

namespace Fafas\QueryBuilder\Builder;

interface QueryInterface {
    
    public function getField();
    
    public function setField($field);
    
    public function getKeyword();
    
    public function setKeyword($keyword);
    
}
