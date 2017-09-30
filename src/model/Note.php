<?php

class Note {
    
    public $time;
    public $note = array();
    public $effect;
    public $length;
    
    public function __construct($note, $effect, $time, $length) {
        $this->note = $note;
        $this->effect = $effect;
        $this->time = $time;
        $this->length = $length;
    }

    public function __clone() {
         foreach($this as $key => $val) {
            if (is_object($val) || (is_array($val))) {
                $this->{$key} = unserialize(serialize($val));
            }
        }
    }
}