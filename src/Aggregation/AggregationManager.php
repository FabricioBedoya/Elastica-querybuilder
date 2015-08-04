<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace O2\QueryBuilder2\Aggregation;

use O2\QueryBuilder2\Builder\ManagerAbstract;

class AggregationManager extends ManagerAbstract {
    
    public function __construct() {
        $this->patternClass = 'O2\\QueryBuilder\\Aggregation\\';
        $this->patternFile = 'Aggregation';
    }
    
    public function getFolder() {
        return dirname(__FILE__);
    }

}
