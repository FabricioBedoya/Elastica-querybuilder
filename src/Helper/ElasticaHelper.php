<?php

namespace Fafas\ElasticaQuery\Helper;

/**
 * Description of ElasticaHelper
 *
 * @author fabriciobedoya
 */
class ElasticaHelper {
    
    public static function isAssociativeArray(array $array) {
        $flag = (bool) count(array_filter(array_keys($array), 'is_string'));
        return $flag;
    }
    
    /**
     * 
     * @param array $aggs
     * @param array $response
     * @return type
     */
    public static function getAvailablesValues(array $aggs, array $response) {
        $availables = array();
        foreach($aggs as $agg) {
            $availables[$agg['terms']['_id']] = static::getAggregationIdsAvailables($response, $agg['terms']['_id']);
        }
        return $availables;
    }
    
    
    /**
     * 
     * @param array $results
     * @param string $aggName
     * @return array
     */
    public static function getAggregationIdsAvailables(array $results, $aggName) {
        $availables = array();
        if (isset($results['aggregations']['global_aggregations'])) {
            if (isset($results['aggregations']['global_aggregations'][$aggName])) {
                $buckets = static::getBuckets($results['aggregations']['global_aggregations'][$aggName]);
                foreach($buckets as $key => $agg) {
                    $availables[$agg['key']] = $agg['key'];
                }
                $results['aggs'][$aggName] = $availables;
            }
        }
        return $availables;
    }
    
    /**
     * 
     * @param type $array
     */
    public static function getBuckets($array) {
        if (!is_array($array)) {
            $buckets = false;
        }
        else {
            foreach (array_keys($array) as $key) {
                if ($key === 'buckets') {
                    $buckets = $array[$key];
                }
                else {
                    $buckets = static::getBuckets($array[$key]);
                }
                if ($buckets !== false) {
                    break;
                }
            }
        }
        return $buckets;
    }
    
    /**
     * Format all date value
     * @param array $params
     * @return array
     */
    public static function formatRangeDate(array $params) {
        foreach($params as $key => $value) {
            if (is_array($value)) {
                $params[$key] = static::formatRangeDate($value);
            }
            if (preg_match('/([0-9]{2,4})\-([0-9]{2,4})\-([0-9]{2,4})/', $value)) {
                $date = new \DateTime($value);
                $params[$key] = $date->format('d-m-Y');
            } 
        }
        return $params;
    }
}
