<?php

namespace TQ\QueryBuilderBundle\Filter;

interface FilterInterface {
    
    public function updateFromArray(array $array);
    
    public function getFilter();
}
