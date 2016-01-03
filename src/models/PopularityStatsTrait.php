<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 03/01/16
 * Time: 1:49 PM
 */

namespace Jitheshgopan\Popularity;


trait PopularityStatsTrait {

    public function popularityStats()
    {
        return $this->morphOne('Jitheshgopan\Popularity\Stats', 'trackable');
    }

    public function hit()
    {
        //check if a polymorphic relation can be set
        if($this->exists){
            $stats = $this->popularityStats()->first();
            if( empty( $stats ) ){
                //associates a new Stats instance for this instance
                $stats = new Stats();
                $this->popularityStats()->save($stats);
            }
            return $stats->updateStats();
        }
        return false;
    }
}