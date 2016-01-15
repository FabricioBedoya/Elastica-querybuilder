<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Fafas\ElasticaQuery\Helper;

/**
 * Description of ElasticaGeoTools
 *
 * @author fabriciobedoya
 */
class ElasticaGeoTools 
{
    
    public static $clusters = array(
      "1" => 1,
      "2" => 1,
      "3" => 1,
      "4" => 0.85,
      "5" => 0.82,
      "6" => 0.80,
      "7" => 0.78,
      "8" => 0.73,
      "9" => 0.68,
      "10" => 0.67,
      "11" => 0.63,
      "12" => 0.58,
      "13" => 0.55,
      "14" => 0.5,
      "15" => 0.45,
      "16" => 0.37,
      "17" => 0.3,
      "18" => 0.2,
      "19" => 0,
      "20" => 0,
      "21" => 0,
      "22" => 0,
    );
    /**
     * Calculate distance entre 2 coordinates in meters
     * @param type $lat1
     * @param type $lon1
     * @param type $lat2
     * @param type $lon2
     * @return type
     */
    public static function calculateDistanceLatLon($lat1, $lon1, $lat2, $lon2) {
        $delta_lat = $lat2 - $lat1;
        $delta_lon = $lon2 - $lon1;
        $earth_radius = 6372.795477598;
        $alpha    = $delta_lat/2;
        $beta     = $delta_lon/2;
        $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
        $c        = asin(min(1, sqrt($a)));
        $distance = round(2 * $earth_radius * $c, 4);

        return $distance;
    }
    
    /**
     * 
     * @param type $response
     * @param type $distance
     */
    public static function clearFacetsByDistance($response, $lat1, $lon1, $distanceMax, $maximum = 10000) {
        $clusters = array();
        if (isset($response['facets']['places']['clusters'])) {
            foreach($response['facets']['places']['clusters'] as $cluster ) {
                $distance = static::calculateDistanceLatLon($lat1, $lon1, $cluster['center']['lat'], $cluster['center']['lon']);
                if ($distance < $distanceMax) {
                    $clusters[] = $cluster;
                }
                if (count($clusters) > $maximum) {
                    break;
                }
            }
        }
        $response['facets']['places']['clusters'] = $clusters;
        return $response;
    }

    /**
     * @param $response
     * @param $geoPoints
     * @param $distanceMax
     * @param int $maximum
     * @return mixed
     */
    public static function clearFacetsByPoints($response, $geoPoints, $distanceMax, $maximum = 10000) {
        $clusters = array();
        if (isset($response['facets']['places']['clusters'])) {
            foreach($response['facets']['places']['clusters'] as $cluster ) {
                $nearToAnyPoint = false;
                foreach($geoPoints as $geoPoint) {
                    list($lon,$lat) = explode(',', trim($geoPoint));
                    $distance = static::calculateDistanceLatLon($lat, $lon, $cluster['center']['lat'], $cluster['center']['lon']);
                    if ($distance <= $distanceMax) {
                        $nearToAnyPoint = true;
                        break;
                    }
                }
                if ($nearToAnyPoint) {
                    $clusters[] = $cluster;
                }
                if (count($clusters) > $maximum) {
                    break;
                }
            }
        }
        $response['facets']['places']['clusters'] = $clusters;
        return $response;
    }
    
    /**
     * 
     * @param array $response
     * @param type $lat1
     * @param type $lon1
     * @param type $distanceMax
     * @param type $maximum
     * @return type
     */
    public static function clearHitsByDistance($response, $lat1, $lon1, $distanceMax, $maximum = 100000) {
        if (isset($response['hits']['hits'])) {
            foreach($response['hits']['hits'] as $key => $index ) {
                $geoLocations = array();
                foreach($index['_source']['GEO_LOCATIONS'] as $geoLocation) {
                    list($lat,$lon) = explode(',',  $geoLocation);
                    $distance = static::calculateDistanceLatLon($lat1, $lon1, $lat, $lon);
                    if ($distance < $distanceMax) {
                        $geoLocations[] = $geoLocation;
                    }
                }
                $response['hits']['hits'][$key]['_source']['GEO_LOCATIONS'] = $geoLocation;
            }
        }
        return $response;
    }
    
    /**
     * 
     * @param type $latitude
     * @param type $longitude
     * @param type $bearing
     * @param type $distance
     * @param type $distance_unit
     * @param type $return_as_array
     * @return array
     */
    public static function calculateNearByBounds($latitude, $longitude, $bearing = 100, $distance = 10, $distance_unit = 'km') {
        $radius = 6378.1;
        if ($distance_unit == 'm') {
            // Distance is in miles.
            $radius = 3963.1676;
        }
        //  New latitude in degrees.
        $new_latitude = rad2deg(asin(sin(deg2rad($latitude)) * cos($distance / $radius) + cos(deg2rad($latitude)) * sin($distance / $radius) * cos(deg2rad($bearing))));
        //  New longitude in degrees.
        $new_longitude = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad($bearing)) * sin($distance / $radius) * cos(deg2rad($latitude)), cos($distance / $radius) - sin(deg2rad($latitude)) * sin(deg2rad($new_latitude))));
        $coord = array(
            'lat' => $new_latitude,
            'lon' => $new_longitude,
          );
        return $coord;
    }
    
    /**
     * 
     * @param type $bounds
     * @param type $map_width
     * @return type
     */
    public static function calculateZoom($min_lon, $max_lon, $map_width) {
        $GLOBE_WIDTH = 256; // a constant in Google's map projection
        $angle = $min_lon - $max_lon;
        if ($angle < 0) {
            $angle += 360;
        }
        return floor(log($map_width * 360 / $angle / $GLOBE_WIDTH) / log(2));
    }
    
    /**
     * 
     * @param type $zoom
     * @return type
     */
    public static function getFactor($zoom = 1) {
        return static::$clusters[$zoom];
    }
}
