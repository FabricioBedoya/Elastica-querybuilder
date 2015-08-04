<?php
/**
 * Description of newPHPClass
 *
 * @author fabriciobedoya
 */
namespace Fafas\QueryBuilder\Aggregation;

use Fafas\QueryBuilder\Builder\ManagerAbstract;

class AggregationManager extends ManagerAbstract {
    
    public function __construct() {
        $this->patternClass = 'O2\\QueryBuilder\\Aggregation\\';
        $this->patternFile = 'Aggregation';
    }
    
    public function getFolder() {
        return dirname(__FILE__);
    }

}
