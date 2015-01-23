<?php

namespace O2\QueryBuilder\Filter;

interface FilterInterface {
    
    public function updateFromArray(array $array);
    
    public function getFilter();
}
