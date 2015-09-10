<?php
namespace VDB\Spider;

/**
 * @author Tobinski
 * @copyright 2015 tobinski
 */
interface GraphInterface
{
    /**
     * @param $name
     * @return mixed
     */
    public function addNode($name);


    /**
     * @param $start
     * @param $end
     * @param bool|true $directed
     * @param int $weight
     * @return mixed
     */
    public function addEdge($start, $end, $directed=TRUE, $weight=1);
}
