<?php

class Part {
   
    public $instrument;
    public $time_signature;
    public $time_increment;
    public $tempo;
    public $volume;
    
    public $measures = array();
    
    public function __construct($instrument=1, $time_signature="4/4", $time_increment=16, $tempo=120, $volume=100) {
        //$this->measures = $measures; //probably don't need this line, right?
        $this->instrument = $instrument;
        $this->time_signature = $time_signature;
        $this->time_increment = $time_increment;
        $this->tempo = $tempo;
        $this->volume = $volume;
    }
    
    public static function setMeasures($measures) {
        $instance = new self();
        $instance->measures = $measures;
        return $instance;
    }
    
    public static function emptyPart() {
        return new self();
    }
}

?>