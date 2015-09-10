<?php

namespace VDB\Spider\Graph;
use VDB\Spider\Graph\GraphInterface;

class LinkGraph implements GraphInterface {
    private $adj_mat;
    private $edges;
    private $nodes;

    function __construct(){
        $this->adj_mat = array();
        $this->edges = 0;
        $this->nodes = 0;
    }

    /**
     * print matrix
     */
    public function debug ()
    {
        print_r($this->adj_mat);
    }
    /**
     * Return matrix
     */
    public function getGraphMatrix(){
        return $this->adj_mat;
    }

    /**
     * @param $name
     * @return bool
     */
    private static function validateName($name){
        if(!$name) return FALSE;
        if(is_object($name) || is_array($name)) return FALSE;
        if($name == '') return FALSE;
        if(strlen($name) < 1) return FALSE;
        return TRUE;
    }


    /**
     * @param $name
     * @return bool
     */
    public function addNode($name){
        if(!self::validateName($name)){
            return FALSE;
        }
        if(!isset($this->adj_mat[$name])){
            $this->adj_mat[$name] = array();
            $this->nodes++;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function nodeExists($name){
        return isset($this->adj_mat[$name]);
    }

    /**
     * @return int
     */
    public function nodeCount(){
        return $this->nodes;
    }

    /**
     * @param $name
     * @param bool|true $directed
     * @return bool
     */
    public function removeNode($name, $directed=TRUE){
        if(!self::validateName($name)){
            return FALSE;
        }
        if(!$this->nodeExists($name)){
            return FALSE;
        }
        foreach($this->adj_mat[$name] as $node => $list ){
            $this->removeEdge($name, $node, $directed);
        }

        //do the final cleanup pass through ESPECIALLY if its directed
        foreach($this->adj_mat as $n => $list){
            if( $n == $name ) continue;
            $this->removeEdge($n, $name, TRUE);
        }
        unset($this->adj_mat[$name]);//leave this for the end since we need it for removeEdge();
        $this->nodes--;
    }

    /**
     * @param $name
     * @return bool|int
     */
    public function degree($name){
        if(!self::validateName($name)){
            return FALSE;
        }
        if(!$this->nodeExists($name)){
            return FALSE;
        }
        return count($this->adj_mat[$name]);
    }

    /**
     * @param $name
     * @return bool|int
     */
    public function outDegree($name){
        return $this->degree($name);
    }

    /**
     * @param $name
     * @return bool|int
     */
    public function inDegree($name){
        if(!self::validateName($name)){
            return FALSE;
        }
        if(!$this->nodeExists($name)){
            return FALSE;
        }
        //expensive operation, go through all nodes but this one
        $inDegree = 0;
        foreach($this->adj_mat as $n => $list){
            if($n == $name) continue;
            if($this->edgeExists($n, $name)) $inDegree++;
        }
        return $inDegree;
    }

    /**
     * @param $start
     * @param $end
     * @return bool
     */
    public function edgeExists($start, $end){
        return $this->getEdge($start, $end) !== FALSE;
    }

    /**
     * @param bool|true $directed
     * @return float|int
     */
    public function edgeCount($directed=TRUE){
        if(!$directed) return $this->edges / 2;
        return $this->edges;
    }

    /**
     * @param $start
     * @param $end
     * @return bool
     */
    public function getEdge($start, $end){
        if(!self::validateName($start) || !self::validateName($end)){
            return FALSE;
        }
        if($this->nodeExists($start) && $this->nodeExists($end)){
            if( isset($this->adj_mat[$start][$end]) ) return $this->adj_mat[$start][$end];
        }
        return FALSE;
    }

    /**
     * @param $start
     * @param $end
     * @param bool|true $directed
     * @param int $weight
     * @return bool
     */
    public function addEdge($start, $end, $directed=TRUE, $weight=1){
        if(!self::validateName($start) || !self::validateName($end)){
            return FALSE;
        }
        if( $this->nodeExists($start) && $this->nodeExists($end) ){
            if(!isset($this->adj_mat[$start][$end])) $this->edges++;
            $this->adj_mat[$start][$end] = $weight;
            if(!$directed){
                if(!isset($this->adj_mat[$end][$start])) $this->edges++;
                $this->adj_mat[$end][$start] = $weight;
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * @param $start
     * @param $end
     * @param bool|true $directed
     * @return bool
     */
    public function removeEdge($start, $end, $directed=TRUE){
        if( !self::validateName($start) || !self::validateName($end) ){
            return FALSE;
        }
        if( $this->nodeExists($start) && $this->nodeExists($end) ){
            if(isset($this->adj_mat[$start][$end])){
                unset($this->adj_mat[$start][$end]);
                $this->edges--;
            }
            if(!$directed){
                if(isset($this->adj_mat[$end][$start])){
                    unset($this->adj_mat[$end][$start]);
                    $this->edges--;
                }
            }

            return TRUE;
        }
        return FALSE;
    }

    /**
     * get first added node with degree 0
     * @return mixed
     */
    public function  getRoot()
    {
        $array =  array_keys($this->adj_mat);
        return $array[0];
    }

    /**
     * @param $name
     * @return array
     */
    public function getChildren ($name)
    {
        if(!self::validateName($name) || !$this->nodeExists($name)) return false;
        return array_keys($this->adj_mat[$name]);
    }

    /**
     * @param $name
     * @return array
     */
    public function getParents ($name)
    {
        if(!self::validateName($name) || !$this->nodeExists($name)) return false;
        $parents = array();
        foreach ($this->adj_mat as $key => $row) {
            if(array_key_exists($name,$row)) $parents[] = $key;
        }
        return $parents;
    }
}

