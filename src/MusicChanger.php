<?php

class MusicChanger {
    
    public $part;
    public $is_drum;
    
    public function cmp($a, $b) {
		if ($a->time == $b->time) {
			return 0;
		}
		return ($a->time < $b->time) ? -1 : 1;
	}
    
    public function noteExistsAt($measure, $time) {
        if (!empty($measure->notes)) {
            foreach ($measure->notes as $key => $value) {
                if ($value->time == $time)
                    return $key;
            }
        }
        return false;
    }

    public function noteExistsAtPiece($piece, $time) {
        if (!empty($piece)) {
            foreach ($piece as $key => $value) {
                if ($value->time == $time)
                    return $key;
            }
        }
        return false;
    }
    
    public function addNoteRandom($part) {
        $measure_index = rand(0, count($part->measures)-1);
        $time = rand(0, $part->time_increment-1);
        $chord = $this->getChordAtTime($part, $measure_index, $time);
        $new_note = $this->getRandomNoteInChord($chord);
        $result = $this->noteExistsAt($part->measures[$measure_index], $time);
        if (!$result) {
            $part->measures[$measure_index]->notes[] = new Note([$new_note], "NORMAL", $time, 0);
        } else {
            $part->measures[$measure_index]->notes[$result]->note[] = $new_note;
        }
        
        usort($part->measures[$measure_index]->notes, array("MusicChanger", "cmp"));
        
        foreach ($part->measures[$measure_index]->notes as $key => $value) {
            $next_note = (isset($part->measures[$measure_index]->notes[$key+1])) ? $part->measures[$measure_index]->notes[$key+1] : false;
            if (!$next_note) {
                $part->measures[$measure_index]->notes[$key]->length = $part->time_increment - $value->time;
            } else {
                $part->measures[$measure_index]->notes[$key]->length = $next_note->time - $value->time;  
            }
        }
        
        return $part;
    }
    
    public function addNoteRandomInMeasure($measure, $time_increment=16) {
        $time = rand(0, $time_increment-1);
        $chord = $this->getChordAtTimeInMeasure($measure, $time);
        $new_note = $this->getRandomNoteInChord($chord);
        $result = $this->noteExistsAt($measure, $time);
        if (!$result) {
            $measure->notes[] = new Note([$new_note], "NORMAL", $time, 0);
        } else {
            $measure->notes[$result]->note[] = $new_note;
        }
        
        usort($measure->notes, array("MusicChanger", "cmp"));
        
        foreach ($measure->notes as $key => $value) {
            $next_note = (isset($measure->notes[$key+1])) ? $measure->notes[$key+1] : false;
            if (!$next_note) {
                $measure->notes[$key]->length = $time_increment - $value->time;
            } else {
                $measure->notes[$key]->length = $next_note->time - $value->time;  
            }
        }
        
        return $measure;
    }
    
    public function addNoteFromSackInMeasure($measure, $sack, $time_increment=16) {
        $time = rand(0, $time_increment-1);
        $rand_index = rand(0, count($sack)-1);
        $new_note = $sack[$rand_index];
        $result = $this->noteExistsAt($measure, $time);
        if (!$result) {
            $measure->notes[] = new Note([$new_note], "NORMAL", $time, 0);
        } else {
            $measure->notes[$result]->note[] = $new_note;
        }
        
        usort($measure->notes, array("MusicChanger", "cmp"));
        
        foreach ($measure->notes as $key => $value) {
            $next_note = (isset($measure->notes[$key+1])) ? $measure->notes[$key+1] : false;
            if (!$next_note) {
                $measure->notes[$key]->length = $time_increment - $value->time;
            } else {
                $measure->notes[$key]->length = $next_note->time - $value->time;  
            }
        }
        
        return $measure;
    }

    public function addNoteFromSackInPiece($piece, $sack, $time_increment=16) {
        $time = rand(0, $time_increment-1);
        $rand_index = rand(0, count($sack)-1);
        $new_note = $sack[$rand_index];
        $result = $this->noteExistsAtPiece($piece, $time);
        if (!$result) {
            $piece[] = new Note([$new_note], "NORMAL", $time, 0);
        } else {
            $piece[$result]->note[] = $new_note;
        }
        
        usort($piece, array("MusicChanger", "cmp"));
        
        foreach ($piece as $key => $value) {
            $next_note = (isset($piece[$key+1])) ? $piece[$key+1] : false;
            if (!$next_note) {
                $piece[$key]->length = $time_increment - $value->time;
            } else {
                $piece[$key]->length = $next_note->time - $value->time;  
            }
        }
        
        return $piece;
    }

    public function addNoteFromSackInPieceAt($piece, $sack, $time, $time_increment=16) {
        $rand_index = rand(0, count($sack)-1);
        $new_note = $sack[$rand_index];
        $result = $this->noteExistsAtPiece($piece, $time);
        if (!$result) {
            $piece[] = new Note([$new_note], "NORMAL", $time, 0);
        } else {
            $piece[$result]->note[] = $new_note;
        }
        
        usort($piece, array("MusicChanger", "cmp"));
        
        foreach ($piece as $key => $value) {
            $next_note = (isset($piece[$key+1])) ? $piece[$key+1] : false;
            if (!$next_note) {
                $piece[$key]->length = $time_increment - $value->time;
            } else {
                $piece[$key]->length = $next_note->time - $value->time;  
            }
        }
        
        return $piece;
    }

    public function addClosestNoteFromSackInPieceAt($piece, $sack, $note, $time, $time_increment=16) {
        $min_diff = 100;
        $min_note = null;
        foreach ($sack as $s) {
            if (abs($s - $note) < $min_diff && abs($s - $note) != 0) {
                $min_diff = abs($s - $note);
                $min_note = $s;
            }
        }
        if ($min_note != null) {
            $new_note = $min_note;
        } else {
            $new_note = $note;
        }
        $result = $this->noteExistsAtPiece($piece, $time);
        if (!$result) {
            $piece[] = new Note([$new_note], "NORMAL", $time, 0);
        } else {
            $piece[$result]->note[] = $new_note;
        }
        
        usort($piece, array("MusicChanger", "cmp"));
        
        foreach ($piece as $key => $value) {
            $next_note = (isset($piece[$key+1])) ? $piece[$key+1] : false;
            if (!$next_note) {
                $piece[$key]->length = $time_increment - $value->time;
            } else {
                $piece[$key]->length = $next_note->time - $value->time;  
            }
        }
        
        return $piece;
    }

    public function addClosestAbsNoteFromSackInPieceAt($piece, $sack, $note, $time, $time_increment=16) {
        $min_diff = 100;
        $min_note = null;
        foreach ($sack as $s) {
            if (abs($s - $note) < $min_diff) {
                $min_diff = abs($s - $note);
                $min_note = $s;
            }
        }
        $new_note = $min_note;
        $result = $this->noteExistsAtPiece($piece, $time);
        if (!$result) {
            $piece[] = new Note([$new_note], "NORMAL", $time, 0);
        } else {
            $piece[$result]->note[] = $new_note;
        }
        
        usort($piece, array("MusicChanger", "cmp"));
        
        foreach ($piece as $key => $value) {
            $next_note = (isset($piece[$key+1])) ? $piece[$key+1] : false;
            if (!$next_note) {
                $piece[$key]->length = $time_increment - $value->time;
            } else {
                $piece[$key]->length = $next_note->time - $value->time;  
            }
        }
        
        return $piece;
    }

    public function addTouchingNoteFromSackInPieceAt($piece, $note, $time, $increment) {
        //$this->getRandomNoteInKey
        //$new_note = getTouchingNoteInScale($note);
        $result = $this->noteExistsAtPiece($piece, $time);
        if (!$result) {
            $piece[] = new Note([$new_note], "NORMAL", $time, 0);
        } else {
            $piece[$result]->note[] = $new_note;
        }
        
        usort($piece, array("MusicChanger", "cmp"));
        
        foreach ($piece as $key => $value) {
            $next_note = (isset($piece[$key+1])) ? $piece[$key+1] : false;
            if (!$next_note) {
                $piece[$key]->length = $time_increment - $value->time;
            } else {
                $piece[$key]->length = $next_note->time - $value->time;  
            }
        }
        
        return $piece;
    }
    
    //public function addNearNoteInMeasure()
    
    public function changeNoteRandom($part) {
        $measure_index = rand(0, count($part->measures)-1);
        $note_index = rand(0, count($part->measures[$measure_index]->notes)-1);
        $part->measures[$measure_index]->notes[$note_index]->note[0] = $this->getRandomNoteInChord($this->getChordAtTime($part, $measure_index, $part->measures[$measure_index]->notes[$note_index]->time));
        return $part;
    }
    
    public function changeNoteRandomInMeasure($measure) {
        $note_index = rand(0, count($measure->notes)-1);
        $measure->notes[$note_index]->note[0] = $this->getRandomNoteInChord($this->getChordAtTimeInMeasure($measure, $measure->notes[$note_index]->time));
        return $measure;
    }
    
    public function changeRhythmRandom($part) {
        $measure_index = rand(0, count($part->measures)-1);
        $possible_notes = array();
        for ($i=0; $i<count($part->measures[$measure_index]->notes); $i++) {
            $possible_notes[] = $i;
        }
        $note_index = rand(0, count($possible_notes)-1);
        while ($part->measures[$measure_index]->notes[$note_index]->note[0] == -1) {
            //unset()
        }
    }
    
    public function getRandomNoteInChord($chord, $octave=5) {
        $increments = array(0, 2, 4, 5, 7, 9, 11);
        $rand_index = rand(0, 6);
        return $chord->bass_note + $increments[$rand_index] + (12*$octave);
    }
    
    public function getRandomNoteInKey($key, $octave=5) {
        $increments = array(0, 2, 4, 5, 7, 9, 11);
        $rand_index = rand(0, 6);
        return $key + $increments[$rand_index] + (12*$octave);
    }

    public function getChordAtTime($part, $measure_index, $time) {
        $i = $j = 0;
        while ($part->measures[$measure_index]->chords[$i]->length+$j < $time) {
            $j += $part->measures[$measure_index]->chords[$i]->length;
            $i++;
        }
        
        return $part->measures[$measure_index]->chords[$i];
    }
    
    public function getChordAtTimeInMeasure($measure, $time) {
        $i = $j = 0;
        while ($measure->chords[$i]->length+$j < $time) {
            $j += $measure->chords[$i]->length;
            $i++;
        }
        
        return $measure->chords[$i];
    }
    
}

?>