<?php

require_once (dirname(__FILE__).'/../settings.php');

class MusicParser {
  
  public $midi;
  public $line_number = -1;
  public $track_number = 0;
  
  public function __construct($midi) {
      $this->midi = $midi;
  }  
  
  public function parse() { //assume that all numbers are in ascending order from track begin to track end.
      
      $structure = array();
      $parts = array();
      $first = true;
      
      do {
          
        do {
            $pointer = $this->next();
            $tokens = explode(" ", $pointer);
        } while ($tokens[0] != "MTrk");
        
        //create new track and a new part - how long will we make these parts? As long as the track? For now, yes, but that should be able to be defined.
        $structure[][0] = count($parts);
        
        if ($first) { //or just put this all out front and look for the first "On" - probably better.
            do {
                $pointer = $this->next();
                $tokens = explode(" ", $pointer);
            } while ($tokens[1] != "PrCh");
            $first = false;
        }
        
        $part = new Part(filter_var($tokens[3], FILTER_SANITIZE_NUMBER_INT)); //the instrument for this channel.
        
        $pointer = $this->next();
        $tokens = explode(" ", $pointer);
      
        while ($tokens[0] != "TrkEnd") {
            
            //where the magic happens
            //note on when there is a note and the v=some value > 0
            //note off when there is that same note (before another one with v > 0) and v=0 or Off
            
            $time_increment = 1920/$part->time_increment;
            
            $time = round($tokens[0]/$time_increment) % $part->time_increment;
            $raw_time = $tokens[0];
            $type = $tokens[1];
            $channel = filter_var($tokens[2], FILTER_SANITIZE_NUMBER_INT);
            $note_value = filter_var($tokens[3], FILTER_SANITIZE_NUMBER_INT);
            $velocity = filter_var($tokens[4], FILTER_SANITIZE_NUMBER_INT);
            $measure_number = floor($tokens[0]/1920);
            
            if ($velocity > 0 && ($type == "On" || $type == "Off")) { //maybe make 16 not the interval, but 1920, just like the midi file, so i can get all the stocato and everything in.
            
                $length = 0;
                $current_line_number = $this->line_number;
                do {
                    $pointer = $this->next();
                    $tokens = explode(" ", $pointer);
                } while (filter_var($tokens[3], FILTER_SANITIZE_NUMBER_INT) != $note_value && filter_var($tokens[4], FILTER_SANITIZE_NUMBER_INT) != 0);
                
                $length = abs(round(($raw_time-$tokens[0])/$time_increment) % $part->time_increment);
                if ($length == 0)
                    $length = 1; 
                $this->line_number = $current_line_number;    
                
                //round all times to the closest time_interval.
                if ($part->measures[$measure_number] == null) {
                    $part->measures[$measure_number] = new Measure();
                }
                
                $exists = false;
                foreach($part->measures[$measure_number]->notes as $note) {
                    if ($note->time == $time) {
                        $note->note[] = $note_value;
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $part->measures[$measure_number]->notes[] = new Note([$note_value], "NORMAL", $time, $length);
                }
            }      
            $pointer = $this->next();
            $tokens = explode(" ", $pointer);
            
        } 
        
        for ($i=0; $i<$measure_number; $i++) { //it looks like top part is skipping left hand practice part on Celebration...
            if ($part->measures[$i] == null) {
                $part->measures[$i] = new Measure();
            }
        }
        
        ksort($part->measures);
        
        $parts[] = $part;
        
      } while ($this->midi[$this->line_number+1] != null);
       
      $song = new Song("Unknown", "Drew Jex", $structure);
      $song->parts = $parts;
      return $song;
  }

  public function toString($song) {
      echo "<pre>";
      print_r($song);
      echo "</pre>";
  }
  
  public function next() {
      $this->line_number++;
      return $this->midi[$this->line_number];
  }
  
  public function floorToFraction($number, $denominator = 1) {
      $x = $number * $denominator;
      $x = floor($x);
      $x = $x / $denominator;
      return $x;
  }
}

?>