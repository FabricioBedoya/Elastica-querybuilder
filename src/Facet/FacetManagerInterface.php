<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fafas\ElasticaQuery\Facet;

/**
 *
 * @author fabriciobedoya
 */
interface FacetManagerInterface {
    
    public function processFacets(array $array);
}
