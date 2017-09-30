<?php

/* 
 * Analyzes intervals between notes
 */

class AnalyzeIntervals implements Analyzer {

    public $analyzed_part;

    public function setPart($part) {
        $this->analyzed_part = $part;
    }

    public function getScore($part) {
        //for
    }

}

?>