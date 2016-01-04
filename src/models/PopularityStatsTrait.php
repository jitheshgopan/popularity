<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 03/01/16
 * Time: 1:49 PM
 */

namespace Jitheshgopan\Popularity;


use Jitheshgopan\Popularity\Facades\Popularity;

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

    public function scopePopular($query, $period = "day", $orderType = 'DESC')
    {
        switch($period) {
            case "week" :
                $statsType = 'seven_days_stats';
                break;
            case "month":
                $statsType = 'thirty_days_stats';
                break;
            case "lifetime":
                $statsType = 'all_time_stats';
                break;
            default :
                $statsType = 'one_day_stats';
        }
        $statsTable = (new Stats())->getTable();
        $thisTable = $this->table;
        $trackableType = get_class($this);
        $query->leftJoin($statsTable, function($leftJoin) use($statsTable, $thisTable, $trackableType) {
            $leftJoin->on($statsTable . '.trackable_id', '=', $thisTable . '.id');
        });
        $query->where($statsTable . '.trackable_type', '=', $trackableType);
        $query->where( $statsType, '!=', 0 );
        $query->select($thisTable . '.*');
        $query->orderBy( $statsType, $orderType );
        return $query;
    }
}