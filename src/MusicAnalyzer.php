<?php

foreach (glob("src/model/*.php") as $filename) {
    include_once $filename;
}

class MusicAnalyzer {

    public $analyzers;
    public $database;

    public function __construct() {

    }

    /*
     * The big fitness function
     */
    public function getScore($part) {

        //get base pattern score that rewards repeating patterns.
        $score = 0;
		$increment_length = 1;

		while ($increment_length <= $part->time_increment) { //!=
			$part_sections_rhythm = array();
            $part_sections_notes = array();
			$measure_number = 0;
			$initial_note = null;
			foreach ($part->measures as $measure) {
				foreach ($measure->notes as $note) {
					$pointer = ($measure_number*$part->time_increment) + $note->time;
					$list_number = floor($pointer / $increment_length);
					$position_number = $pointer % $increment_length;
					
					if (count($part_sections_notes[$list_number]) == 0) {
						$part_sections_notes[$list_number][$position_number] = 0;
						$initial_note = $note->note[0];
					} else {
						$current_note_diff = abs($note->note[0] - $initial_note);
						$part_sections_notes[$list_number][$position_number] = $current_note_diff;
					}	
					
					$part_sections_rhythm[$list_number][$position_number] = 1;
				}
				$measure_number++;
			}
			while (!empty($part_sections_rhythm)) {
				$current_section_rhythm = array_shift($part_sections_rhythm); //$part_sections_rhythm[0];
				$current_section_notes = array_shift($part_sections_notes); //$part_sections_notes[0];
                foreach ($part_sections_rhythm as $key => $value) { // => $value
					if (($value === $current_section_rhythm) && in_array(1, $current_section_rhythm)) {
						$score += pow($increment_length, 2); //so it rewards bigger matches of patterns
						if ($part_sections_notes[$key] === $current_section_notes) {
							$score += pow($increment_length, 2);
						}
                        unset($part_sections_rhythm[$key]);
                        unset($part_sections_notes[$key]);
					} 
				}
			}
			
			$increment_length *= 2;
		}

        /*foreach ($this->analyzers as $analyzer) {
            $score += $analyzer->getScore($part);
        }*/

        return $score;

    }

	public static function getSimilarity($measure1, $measure2) { 
		
		$score = 0;
		$copy = clone $measure1; 
				
		foreach ($copy->notes as $key => $note1) {
			$count = 0;
			foreach ($measure2->notes as $note2) {
				if ($note1->time == $note2->time) {
					if ($note1->note !== $note2->note) {
						$note1->note = $note2->note;
						$score++;
					}
					if ($note1->length !== $note2->length) {
						$note1->length = $note2->length;
						$score++;
					}		
					break;
				} else if ($note2->time > $note1->time || $count == count($measure2->notes)-1) {
					unset($copy->notes[$key]);
					$score++;
				} 
				$count++;
			}
		}
			
		if (count($copy->notes) != count($measure2->notes)) {
			foreach ($measure2->notes as $note2) {
				$found = false;
				foreach ($copy->notes as $note1) {
					if ($note1->time == $note2->time) {
						$found = true;
						break;
					}
				}
				if (!$found) {
					$copy->notes[] = clone $note2;
					$score++;
				}
			}
		}
		
		return $score;
	}

	public static function getSimilarityRevised($piece1, $piece2) { 
		
		$score = 0;
		$copy = array();
		foreach ($piece1 as $key => $value) {
			$copy[$key] = clone $value;
		}
				
		foreach ($copy as $key => $note1) {
			$count = 0;
			foreach ($piece2 as $note2) {
				if ($note1->time == $note2->time) {
					if ($note1->note !== $note2->note) {
						$note1->note = $note2->note;
						$score++;
					}
					if ($note1->length !== $note2->length) {
						$note1->length = $note2->length;
						$score++;
					}		
					break;
				} else if ($note2->time > $note1->time || $count == count($piece2)-1) {
					unset($copy[$key]);
					$score++;
				} 
				$count++;
			}
		}
			
		if (count($copy) != count($piece2)) {
			foreach ($piece2 as $note2) {
				$found = false;
				foreach ($copy as $note1) {
					if ($note1->time == $note2->time) {
						$found = true;
						break;
					}
				}
				if (!$found) {
					$copy[] = clone $note2;
					$score++;
				}
			}
		}
		
		return $score;
	}

	public static function getDiff($piece1, $piece2) {
		$score = 0;
		$copy = array();
		foreach ($piece1 as $key => $value) {
			$copy[$key] = clone $value;
		}

		foreach ($copy as $key => $note1) {
			$count = 0;
			if ($note1->length == 0) {
				unset($copy[$key]);
				break;
			}
			foreach ($piece2 as $note2) {
				if ($note2->length == 0)
					continue;
				if ($note1->time == $note2->time) {
					if ($note1->note !== $note2->note) {
						$note1->note = $note2->note;
						$score++;
					}
					if ($note1->length !== $note2->length) {
						$note1->length = $note2->length; //not worried about length
					}
					break;
				} else if ($note2->time > $note1->time || $count == count($piece2)-1) {
					unset($copy[$key]);
					$score++;
					break;
				} 
				$count++;
			}
		}

		if (count($copy) != count($piece2)) {
			foreach ($piece2 as $note2) {
				$found = false;
				foreach ($copy as $note1) {
					if ($note1->time == $note2->time) {
						$found = true;
						break;
					}
				}
				if (!$found) {
					$copy[] = clone $note2;
					$score++;
				}
			}
		}
		
		return $score;
	}

	public static function getDiffRhythmic($piece1, $piece2) {
		$score = 0;
		$copy = array();
		foreach ($piece1 as $key => $value) {
			$copy[$key] = clone $value;
		}

		foreach ($copy as $key => $note1) {
			$count = 0;
			if ($note1->length == 0) {
				unset($copy[$key]);
				break;
			}
			foreach ($piece2 as $note2) {
				if ($note2->length == 0)
					continue;
				if ($note1->time == $note2->time) {
					break;
				} else if ($note2->time > $note1->time || $count == count($piece2)-1) {
					unset($copy[$key]);
					$score++;
					break;
				} 
				$count++;
			}
		}

		if (count($copy) != count($piece2)) {
			foreach ($piece2 as $note2) {
				$found = false;
				foreach ($copy as $note1) {
					if ($note1->time == $note2->time) {
						$found = true;
						break;
					}
				}
				if (!$found) {
					$copy[] = clone $note2;
					$score++;
				}
			}
		}
		
		return $score;
	}

	public static function getTotalSimilarNotes($pattern1, $pattern2) {
		return count(array_intersect($pattern1->note_sack, $pattern2->note_sack));
	}

	public static function getSongPatternsByIncrement($song, $pattern_length=16, $similarity=2) { //should includes notes, chords, repetition patterns, common rhythms and note-lengths, and song structure.
		
			$object = new stdClass();
			$patterns = array();
			$structure = array();

			$all_pieces = array();
			foreach ($song->parts as $part) {
				$pieces = array();
				$measure_number = 0;
				foreach ($part->measures as $measure) {
					foreach ($measure->notes as $note) {
						$pointer = ($measure_number*$part->time_increment) + $note->time;
						$list_number = floor($pointer / $pattern_length);
						$position_number = $pointer % $pattern_length;
						
						$pieces[$list_number][$position_number] = $note;	
					}
					$measure_number++;
				}
				$all_pieces[] = $pieces;
			}

			while (!empty($all_pieces)) {
					$next = self::array_shift_assoc_kv($all_pieces);
					echo "_____________";
					echo "<pre>";
					echo print_r($next);
					echo "</pre>";
					echo "______________";
					reset($next);
					$current_key = key($next);
					$current_piece = $next[$current_key];
					$pattern = new Pattern();
					$pattern->length = $pattern_length;
					//$pattern->chords = $current_piece->chords; //here, use getChordValues()
					$pattern->num_notes = count($current_piece);
					foreach ($current_piece as $note) {
						foreach ($note->note as $n) {
							$pattern->note_sack[] = $n;
						}
						//$pattern->note_sack[] = $note->note[0];
					}
				/*	$patterns[] = $pattern;
					$pattern_id = count($patterns)-1;
					$structure[$part_index][$current_key] = $pattern_id; 
					foreach ($pieces as $key => $value) { //needs to go through every piece of each track
						foreach ($all_pieces as $key_part_index => $pieces_to_check) {
							foreach ($pieces_to_check as $key_piece => $value_piece) {
								//for current piece, go through each piece of each part and mark those with similar score.
								if (self::getSimilarityRevised($current_piece, $value_piece) < $similarity) { //also need to look across octaves, right? - haven't done that yet
									$structure[$key_part_index][$key_piece] = $pattern_id;
									unset($pieces[$key]);
								}
							}
						}
					}*/
				}

			/*foreach ($all_pieces as $part_index => $pieces) {

				while (!empty($all_pieces[$part_index])) {
					$next = self::array_shift_assoc_kv($all_pieces[$part_index]);
					reset($next);
					$current_key = key($next);
					$current_piece = $next[$current_key];
					$pattern = new Pattern();
					$pattern->length = $pattern_length;
					//$pattern->chords = $current_piece->chords; //here, use getChordValues()
					$pattern->num_notes = count($current_piece);
					foreach ($current_piece as $note) {
						foreach ($note->note as $n) {
							$pattern->note_sack[] = $n;
						}
						//$pattern->note_sack[] = $note->note[0];
					}
					$patterns[] = $pattern;
					$pattern_id = count($patterns)-1;
					$structure[$part_index][$current_key] = $pattern_id; 
					foreach ($pieces as $key => $value) { //needs to go through every piece of each track
						foreach ($all_pieces as $key_part_index => $pieces_to_check) {
							foreach ($pieces_to_check as $key_piece => $value_piece) {
								//for current piece, go through each piece of each part and mark those with similar score.
								if (self::getSimilarityRevised($current_piece, $value_piece) < $similarity) { //also need to look across octaves, right? - haven't done that yet
									$structure[$key_part_index][$key_piece] = $pattern_id;
									unset($all_pieces[$part_index][$key]);
								}
							}
						}
					}
				}

			}*/
			
			$object->patterns = $patterns;
			$object->structure = $structure;
			
			ksort($object->structure);
		
			return $object;
	}

	public static function getSongRhythmicPatternsByIncrement($song, $pattern_length=16, $similarity=2) { //should includes notes, chords, repetition patterns, common rhythms and note-lengths, and song structure.
		
		$object = new stdClass();
		$patterns = array();
		$structure = array();
		//$measures = $part->measures; //instead of this, we need to break it into sections of patter_length beats

		$pieces = array();
		$measure_number = 0;
		foreach ($song->parts as $part) {
			foreach ($part->measures as $measure) {
				foreach ($measure->notes as $note) {
					$pointer = ($measure_number*$part->time_increment) + $note->time;
					$list_number = floor($pointer / $pattern_length);
					$position_number = $pointer % $pattern_length;
					
					$pieces[$list_number][$position_number] = $note;	
				}
				$measure_number++;
			}
		}

		while (!empty($pieces)) {
			$next = self::array_shift_assoc_kv($pieces);
			reset($next);
			$current_key = key($next);
			$current_piece = $next[$current_key];
			$pattern = new Pattern();
			$pattern->length = $pattern_length;
			//$pattern->chords = $current_piece->chords; //here, use getChordValues()
			$pattern->num_notes = count($current_piece);
			foreach ($current_piece as $note) {
				foreach ($note->note as $n) {
					$pattern->note_sack[] = $n;
				}
				//$pattern->note_sack[] = $note->note[0];
			}
			$patterns[] = $pattern;
			$pattern_id = count($patterns)-1;
			$structure[$current_key] = $pattern_id; 
			foreach ($pieces as $key => $value) {
				if (self::getDiffRhythmic($current_piece, $value) < $similarity) { //also need to look across octaves, right? - haven't done that yet
					$structure[$key] = $pattern_id;
					unset($pieces[$key]);
				}
			}
		}
		
		$object->patterns = $patterns;
		$object->structure = $structure;
		
		ksort($object->structure);
		
		return $object;
	}

	public static function getPatternsByIncrement($part, $pattern_length=16, $similarity=2) { //should includes notes, chords, repetition patterns, common rhythms and note-lengths, and song structure.
		
		$object = new stdClass();
		$patterns = array();
		$structure = array();
		//$measures = $part->measures; //instead of this, we need to break it into sections of patter_length beats

		$pieces = array();
		$measure_number = 0;
		foreach ($part->measures as $measure) {
			foreach ($measure->notes as $note) {
				$pointer = ($measure_number*$part->time_increment) + $note->time;
				$list_number = floor($pointer / $pattern_length);
				$position_number = $pointer % $pattern_length;
				
				$pieces[$list_number][$position_number] = $note;	
			}
			$measure_number++;
		}

		while (!empty($pieces)) {
			$next = self::array_shift_assoc_kv($pieces);
			reset($next);
			$current_key = key($next);
			$current_piece = $next[$current_key];
			$pattern = new Pattern();
			$pattern->length = $pattern_length;
			//$pattern->chords = $current_piece->chords; //here, use getChordValues()
			$pattern->num_notes = count($current_piece);
			foreach ($current_piece as $note) {
				foreach ($note->note as $n) {
					$pattern->note_sack[] = $n;
				}
				//$pattern->note_sack[] = $note->note[0];
			}
			$patterns[] = $pattern;
			$pattern_id = count($patterns)-1;
			$structure[$current_key] = $pattern_id; 
			foreach ($pieces as $key => $value) {
				if (self::getSimilarityRevised($current_piece, $value) < $similarity) { //also need to look across octaves, right? - haven't done that yet
					$structure[$key] = $pattern_id;
					unset($pieces[$key]);
				}
			}
		}
		
		$object->patterns = $patterns;
		$object->structure = $structure;
		
		ksort($object->structure);
		
		return $object;
	}

	public static function getRhythmicPatternsByIncrement($part, $pattern_length=16, $similarity=2) { //should includes notes, chords, repetition patterns, common rhythms and note-lengths, and song structure.
		
		$object = new stdClass();
		$patterns = array();
		$structure = array();
		//$measures = $part->measures; //instead of this, we need to break it into sections of patter_length beats

		$pieces = array();
		$measure_number = 0;
		foreach ($part->measures as $measure) {
			foreach ($measure->notes as $note) {
				$pointer = ($measure_number*$part->time_increment) + $note->time;
				$list_number = floor($pointer / $pattern_length);
				$position_number = $pointer % $pattern_length;
				
				$pieces[$list_number][$position_number] = $note;	
			}
			$measure_number++;
		}

		while (!empty($pieces)) {
			$next = self::array_shift_assoc_kv($pieces);
			reset($next);
			$current_key = key($next);
			$current_piece = $next[$current_key];
			$pattern = new Pattern();
			$pattern->length = $pattern_length;
			//$pattern->chords = $current_piece->chords; //here, use getChordValues()
			$pattern->num_notes = count($current_piece);
			foreach ($current_piece as $note) {
				foreach ($note->note as $n) {
					$pattern->note_sack[] = $n;
				}
				//$pattern->note_sack[] = $note->note[0];
			}
			$patterns[] = $pattern;
			$pattern_id = count($patterns)-1;
			$structure[$current_key] = $pattern_id; 
			foreach ($pieces as $key => $value) {
				if (self::getDiffRhythmic($current_piece, $value) < $similarity) { //also need to look across octaves, right? - haven't done that yet
					$structure[$key] = $pattern_id;
					unset($pieces[$key]);
				}
			}
		}
		
		$object->patterns = $patterns;
		$object->structure = $structure;
		
		ksort($object->structure);
		
		return $object;
	}

	public static function getPatterns($part, $pattern_length=16) { //should includes notes, chords, repetition patterns, common rhythms and note-lengths, and song structure.
		$object = new stdClass();
		$patterns = array();
		$structure = array();
		if ($part->time_increment == $pattern_length) {
			$measures = $part->measures;

			echo "<pre>";
			echo print_r($measures);
			echo "</pre>";

			/*echo var_dump($measures[0]);
			echo self::getSimilarity($measures[0], $measures[1])."<br>";
			echo var_dump($measures[0]);
			echo self::getSimilarity($measures[0], $measures[2])."<br>";
			echo self::getSimilarity($measures[2], $measures[2])."<br>";*/

			echo self::getSimilarity($measures[3], $measures[7])."<br>";

			while (!empty($measures)) {
				$next = self::array_shift_assoc_kv($measures);
				reset($next);
				$current_key = key($next);
				$current_measure = $next[$current_key];
				$pattern = new Pattern();
				$pattern->length = $pattern_length;
				$pattern->chords = $current_measure->chords; //here, use getChordValues()
				$pattern->num_notes = count($current_measure->notes);
				foreach ($current_measure->notes as $note) {
					$pattern->note_sack[] = $note->note[0];
				}
				$patterns[] = $pattern;
				$pattern_id = count($patterns)-1;
				$structure[$current_key] = $pattern_id; 
				foreach ($measures as $key => $value) {
					if (self::getSimilarity($current_measure, $value) < 4) { //also need to look across octaves, right? - haven't done that yet
						$structure[$key] = $pattern_id;
						unset($measures[$key]);
					}
				}
			}
		}
		
		$object->patterns = $patterns;
		$object->structure = $structure;
		
		ksort($object->structure);
		
		return $object;
	}

	public static function getChordProgPlusRoot($song, $increment=1) {
		$object = new stdClass();
		$chords = array();
		$roots = array();

		$section_number = 0;
		$measure_number = 0;
		$time = 0;
		$current_note = null;

		while ($section_number < count($song->structure[0])) {
			while ($measure_number < count($song->parts[$song->structure[0][$section_number]]->measures)) {
				while ($time < 16) {
					$notes = self::getAllNotesAtTime($song, $section_number, $measure_number, $time);	
					$chords[] = self::getChordValuesOctave($notes);
					$roots[] = self::getRootNote($notes);
					$time++;
				}
				$measure_number++;
				$time = 0;
			}
			$section_number++;
		}

		$object->roots = $roots;
		$object->chords = $chords;

		return $object;

	}

    public function setAnalyzers($analyzers) {
        $this->analyzers = $analyzers;
    }

	// returns value
	public static function array_shift_assoc( &$arr ){
		$val = reset( $arr );
		unset( $arr[ key( $arr ) ] );
		return $val; 
	}

	// returns [ key, value ]
	public static function array_shift_assoc_kv( &$arr ){
		$val = reset( $arr );
		$key = key( $arr );
		$ret = array( $key => $val );
		unset( $arr[ $key ] );
		return $ret; 
	}

}

?>