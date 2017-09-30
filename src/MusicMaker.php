<?php

require_once 'lib/midi.class.php';
require_once 'settings.php';

class MusicMaker {

    public $music_player;
    public $music_parser;
    public $music_generator;

    public function __construct() {
        $this->music_player = new Midi();
        $this->music_generator = new MusicGenerator();
    }

    public function read($file) {
        $this->music_player->importMid('songs/'.$file);
        $str = $this->music_player->getTxtMod();
        $midi_array = explode("<br>", $str);
        $this->music_parser = new MusicParser($midi_array);
        return $this->music_parser->parse();
    }

    public function readGlobal($file) {
        $this->music_player->importMid($file);
        $str = $this->music_player->getTxtMod();
        $midi_array = explode("<br>", $str);
        $this->music_parser = new MusicParser($midi_array);
        return $this->music_parser->parse();
    }

    public function write($song, $debug=false) {
        
        $this->music_player->open(480); //timebase=480, quarter note=120
	    $this->music_player->setBpm(100);
        
        foreach ($song->structure as $track) {
            $first = true;
            $track_number = $this->music_player->newTrack() - 1;
            $timer = 0;
            $measure_number = 0;
            $min = 75;
            $max = 95;
            $step = 5;
            foreach ($track as $part_index) {
                if ($part_index < 0) {
                    $part = Part::emptyPart();
                    for ($i=0; $i<abs($part_index); $i++) {
                        $part->measures[] = new Measure();
                    }
                } else {
                    $part = $song->parts[$part_index];
                }
                
                if ($first) { //so if you suddenly change parts, it won't change the instrument unless you create a new track 55. That's probably how it should work.
                    $channel = $track_number+1;
                    $instrument = ($part->instrument == null || $part->instrument == 0) ? 1 : $part->instrument;
                    /*if ($part->is_drum) {
                        $channel = 10;
                        $this->music_player->addMsg($track_number, "0 PrCh ch=10 p=$instrument"); //ch=1
                    } else {*/
                        $this->music_player->addMsg($track_number, "0 PrCh ch=$channel p=$instrument"); //ch=1
                    //}
                    $first = false;
                }
                
                $time_increment = 1920/$part->time_increment;
                foreach ($part->measures as $measure) {
                    if ($measure->notes != null) {
                        foreach ($measure->notes as $note) {
                            //$velocity = 100;
                            $velocity = rand($min, $max);
                            $min += $step;
                            $max += $step;
                            if ($max > 95 || $max < 65) {
                                $step *= -1;
                            } 
                            $timer = (1920 * $measure_number) + ($note->time*$time_increment);
                            foreach ($note->note as $same_note) {
                                $pitch = abs($same_note);
                                if ($same_note == -1) {
                                    $this->music_player->addMsg($track_number, "$timer On ch=$channel n=$pitch v=0");
                                } else {
                                    $this->music_player->addMsg($track_number, "$timer On ch=$channel n=$pitch v=$velocity");
                                }  
                            }
                            $length = $note->length*$time_increment;
                            $end_note = $length+$timer;
                            foreach ($note->note as $same_note) {
                                $pitch = abs($same_note);
                                if ($same_note == -1) {
                                    $this->music_player->addMsg($track_number, "$timer On ch=$channel n=$pitch v=0");
                                } else {
                                    $this->music_player->addMsg($track_number, "$timer On ch=$channel n=$pitch v=$velocity");
                                }    
                            }
                            $timer = $end_note;
                        }
                    }
                    $measure_number++;
                }
            }
            
            $this->music_player->addMsg($track_number, "$timer Meta TrkEnd");
        }
        
        $this->music_player->deleteTrack($this->music_player->getTrackCount()-1);

        $file = $song->title.'.mid';
        $loop = 1;
        $plug = 'wm';
        
        if ($debug)
            echo $this->music_player->getTxtMod();
        
        $this->music_player->saveMidFile('songs/'.$file);
    }

    public function writeGlobal($song, $debug=false) {
        
        $this->music_player->open(480); //timebase=480, quarter note=120
	    $this->music_player->setBpm(100);
        
        foreach ($song->structure as $track) {
            $first = true;
            $track_number = $this->music_player->newTrack() - 1;
            $timer = 0;
            $measure_number = 0;
            $min = 75;
            $max = 95;
            $step = 5;
            foreach ($track as $part_index) {
                if ($part_index < 0) {
                    $part = Part::emptyPart();
                    for ($i=0; $i<abs($part_index); $i++) {
                        $part->measures[] = new Measure();
                    }
                } else {
                    $part = $song->parts[$part_index];
                }
                
                if ($first) { //so if you suddenly change parts, it won't change the instrument unless you create a new track 55. That's probably how it should work.
                    $channel = $track_number+1;
                    $instrument = ($part->instrument == null || $part->instrument == 0) ? 1 : $part->instrument;
                    /*if ($part->is_drum) {
                        $channel = 10;
                        $this->music_player->addMsg($track_number, "0 PrCh ch=10 p=$instrument"); //ch=1
                    } else {*/
                        $this->music_player->addMsg($track_number, "0 PrCh ch=$channel p=$instrument"); //ch=1
                    //}
                    $first = false;
                }
                
                $time_increment = 1920/$part->time_increment;
                foreach ($part->measures as $measure) {
                    if ($measure->notes != null) {
                        foreach ($measure->notes as $note) {
                            //$velocity = 100;
                            $velocity = rand($min, $max);
                            $min += $step;
                            $max += $step;
                            if ($max > 95 || $max < 65) {
                                $step *= -1;
                            } 
                            $timer = (1920 * $measure_number) + ($note->time*$time_increment);
                            foreach ($note->note as $same_note) {
                                $pitch = abs($same_note);
                                if ($same_note == -1) {
                                    $this->music_player->addMsg($track_number, "$timer On ch=$channel n=$pitch v=0");
                                } else {
                                    $this->music_player->addMsg($track_number, "$timer On ch=$channel n=$pitch v=$velocity");
                                }  
                            }
                            $length = $note->length*$time_increment;
                            $end_note = $length+$timer;
                            foreach ($note->note as $same_note) {
                                $pitch = abs($same_note);
                                if ($same_note == -1) {
                                    $this->music_player->addMsg($track_number, "$timer On ch=$channel n=$pitch v=0");
                                } else {
                                    $this->music_player->addMsg($track_number, "$timer On ch=$channel n=$pitch v=$velocity");
                                }    
                            }
                            $timer = $end_note;
                        }
                    }
                    $measure_number++;
                }
            }
            
            $this->music_player->addMsg($track_number, "$timer Meta TrkEnd");
        }
        
        $this->music_player->deleteTrack($this->music_player->getTrackCount()-1);

        $file = $song->title.'.mid';
        $loop = 1;
        $plug = 'wm';
        
        if ($debug)
            echo $this->music_player->getTxtMod();
        
        $this->music_player->saveMidFile('../songs/'.$file);
    }

    public function import($files) {
        $imported_songs = array();
        foreach ($files as $file) {
            $imported_songs[] = $this->read($file);
        }

        return $imported_songs;
    }

    /*
     * The main point of the entire project. Runs a genetic algrotihm 
     */
    /*public function make($files, $analyzer_ids, $params, $debug=false) {

        $songs = $this->import($files);

        $analyzers = array();
        foreach ($analyzer_ids as $id) {
            $analyzers[] = new $id;
        }

        foreach ($params as $key => $value) {
            switch ($key) {
                case 'threshold':
                    $threshold = $value['threshold'];
                break;
                case 'pop_size':
                    $pop_size = $value['pop_size'];
                break;
                default:
                    //TODO
                break;
            }
        }

        //text view: layout_width: match_parent
        //layout_height: wrap_content
        //text view is in relative layout

        //look at patterns of measure layouts to determine overall structure

        $this->music_generator->setAnalyzers($analyzers);
        $this->music_generator->generatePopulation($pop_size);
        
        return $this->music_generator->run($threshold);
    }*/

    /*
     * The major entry point for creating music!
     */
    public function make($style, $params) {
        $instance = new $style($params);
        return $instance->make();
    }

    public function makeRandom($song, $increment, $similarity) {

        $patterns = $this->music_generator->getPatternsByIncrement($song, $increment, $similarity);
        $music_changer = new MusicChanger();

        $piece_per_measure = floor(16/$increment);

        $song_structure = array();
        $parts = array();

        foreach ($patterns as $object) {
            $pattern = $object->patterns;
            $structure = $object->structure;

            /*echo "<pre>";
            echo print_r($structure);
            echo "</pre>";*/

            $proposed_structure = array();
            $str = null;
            for ($i=0; $i<max(array_keys($structure))+1+(16/$increment); $i++) {
                if ($i % $piece_per_measure == 0) {
                    if ($str != null)
                        $proposed_structure[] = substr($str, 0, -1);
                    $str = null;
                }
                if (array_key_exists($i, $structure)) {
                    $str .= (string) $structure[$i].'|';
                } else {
                    $str .= 'N|';
                }
            } //may need to add padding at end if it ends mid-measure - keep in mind

            //now go through and assign each value a new number if it's the first time it's been seen
            $new_structure = array();
            foreach ($proposed_structure as $key => $value) {
                $new_structure[$value][] = $key;
            }

            $final_structure = array();
            $count = 0;
            foreach ($new_structure as $key => $value) {
                foreach ($value as $v) {
                    $final_structure[$v] = $count;
                }
                $count++;
            }

            ksort($final_structure);

            foreach ($final_structure as $key => $value) {
                $final_structure[$key] = $value+count($parts);
            }

            echo "<pre>";
            echo print_r($proposed_structure); //just put this in an array and encode it!
            echo "</pre>";

            $pieces = array();
            foreach ($pattern as $key => $value) {
                $piece = array();
                //$value->num_notes--;
                //if ($value->num_notes < 0) $value->num_notes = 1;
                for ($i=0; $i<$value->num_notes; $i++) {
                    $piece = $music_changer->addNoteFromSackInPiece($piece, $value->note_sack, $increment);
                }
                $pieces[] = $piece;
            }

            foreach ($new_structure as $key => $value) {
                $measures = array();
                $chords = array();
                $notes = array();
                $measure_pieces = explode("|", $key);
                $count = 0;
                foreach ($measure_pieces as $kp => $vp) {
                    $first = true;
                    if ($vp == 'N') {
                        //$notes[] = new Note(array(-1), "NORMAL", $count*$increment, $increment);
                    } else {
                        //make a copy of the piece
                        $copy = array();
                        foreach ($pieces[$vp] as $k => $v) {
                            $copy[$k] = clone $v;
                        }
                        foreach ($copy as $k => $v) {
                            /*if ($first) {
                                $first = false;
                                if ($copy[$k]->time != 0) {
                                    $notes[] = new Note(array(-1), "NORMAL", $count*$increment, $copy[$k]->time);
                                }
                            }*/
                            $copy[$k]->time += $count*$increment;
                            $notes[] = $copy[$k];
                        }
                    }
                    $count++;
                }
                ksort($notes);
                $chords[] = new Chord(0, "MAJOR", 16);
                $measures[] = new Measure($notes, $chords);

                $part = Part::setMeasures($measures);
                $part->instrument = 1;
                
                $parts[] = $part;
            }

            $song_structure[] = $final_structure;
        }

        $part = new Part();
        $measures = array();
        $offset = 0;
        for ($i=0; $i<count($song_structure[0]); $i++) {
            $notes = array();
            $chords = array();
            $chords[] = new Chord(0, "MAJOR", 16);
            for ($j=0; $j<4; $j++) {
                $notes[] = new Note([60], "NORMAL", 4*$j, 4);
            }
                    
            $measures[] = new Measure($notes, $chords);
            
        }

        $part = Part::setMeasures($measures);
        $part->instrument = 117; //10 //99
        $parts[] = $part;
        $metranome_id = count($parts)-1;

        //$song_structure[][0] = $metranome_id;

        $temp = array();
        for ($i=1; $i<count($song_structure); $i++) {
            $temp [] = $song_structure[$i];
            unset($song_structure[$i]);
        }

        $song_structure[][0] = $metranome_id;

        foreach ($temp as $t) {
            $song_structure[] = $t;
        }

        $song = new Song("New Random Song_".$song->title."_".$increment."_".$similarity."_".$this->generateRandomString(5), "Drew Jex", $song_structure);
        $song->parts = $parts;

        return $song;
    }

    public function makeSmart($song, $increment, $similarity) {

        $patterns = $this->music_generator->getPatternsByIncrement($song, $increment, $similarity);
        $music_changer = new MusicChanger();

        $piece_per_measure = floor(16/$increment);

        $song_structure = array();
        $parts = array();

        foreach ($patterns as $object) {
            $pattern = $object->patterns;
            $structure = $object->structure;

            /*echo "<pre>";
            echo print_r($structure);
            echo "</pre>";*/

            $proposed_structure = array();
            $str = null;
            for ($i=0; $i<max(array_keys($structure))+1+(16/$increment); $i++) {
                if ($i % $piece_per_measure == 0) {
                    if ($str != null)
                        $proposed_structure[] = substr($str, 0, -1);
                    $str = null;
                }
                if (array_key_exists($i, $structure)) {
                    $str .= (string) $structure[$i].'|';
                } else {
                    $str .= 'N|';
                }
            } //may need to add padding at end if it ends mid-measure - keep in mind

            //now go through and assign each value a new number if it's the first time it's been seen
            $new_structure = array();
            foreach ($proposed_structure as $key => $value) {
                $new_structure[$value][] = $key;
            }

            $final_structure = array();
            $count = 0;
            foreach ($new_structure as $key => $value) {
                foreach ($value as $v) {
                    $final_structure[$v] = $count;
                }
                $count++;
            }

            ksort($final_structure);

            foreach ($final_structure as $key => $value) {
                $final_structure[$key] = $value+count($parts);
            }

            echo "<pre>";
            echo print_r($proposed_structure);
            echo "</pre>";

            $pieces = array();
            foreach ($pattern as $key => $value) {
                $max_score = -1;
                $max_piece = null;
                for ($j=0; $j<1000; $j++) {
                    $piece = array();
                    for ($i=0; $i<$value->num_notes; $i++) {
                        $piece = $music_changer->addNoteFromSackInPiece($piece, $value->note_sack, $increment);
                    }
                    $part = new Part();
                    $measures = array();
                    $chords = array();
                    $chords[] = new Chord(0, "MAJOR", $increment);
                    $measures[] = new Measure($piece, $chords);

                    $part = Part::setMeasures($measures);
                    $part->instrument = 1;
                    $score = MusicAnalyzer::getScore($part);
                    if ($score > $max_score) {
                        $max_score = $score;
                        $max_piece = $piece;
                    }
                }
                $pieces[] = $max_piece;
            }

            foreach ($new_structure as $key => $value) {
                $measures = array();
                $chords = array();
                $notes = array();
                $measure_pieces = explode("|", $key);
                $count = 0;
                foreach ($measure_pieces as $kp => $vp) {
                    $first = true;
                    if ($vp == 'N') {
                        //$notes[] = new Note(array(-1), "NORMAL", $count*$increment, $increment);
                    } else {
                        //make a copy of the piece
                        $copy = array();
                        foreach ($pieces[$vp] as $k => $v) {
                            $copy[$k] = clone $v;
                        }
                        foreach ($copy as $k => $v) {
                            /*if ($first) {
                                $first = false;
                                if ($copy[$k]->time != 0) {
                                    $notes[] = new Note(array(-1), "NORMAL", $count*$increment, $copy[$k]->time);
                                }
                            }*/
                            $copy[$k]->time += $count*$increment;
                            $notes[] = $copy[$k];
                        }
                    }
                    $count++;
                }
                ksort($notes);
                $chords[] = new Chord(0, "MAJOR", 16);
                $measures[] = new Measure($notes, $chords);

                $part = Part::setMeasures($measures);
                $part->instrument = 1;
                
                $parts[] = $part;
            }

            $song_structure[] = $final_structure;
        }

        $part = new Part();
        $measures = array();
        $offset = 0;
        for ($i=0; $i<count($song_structure[0]); $i++) {
            $notes = array();
            $chords = array();
            $chords[] = new Chord(0, "MAJOR", 16);
            for ($j=0; $j<4; $j++) {
                $notes[] = new Note([60], "NORMAL", 4*$j, 4);
            }
                    
            $measures[] = new Measure($notes, $chords);
            
        }

        $part = Part::setMeasures($measures);
        $part->instrument = 117; //10 //99
        $parts[] = $part;
        $metranome_id = count($parts)-1;

        //$song_structure[][0] = $metranome_id;

        $temp = array();
        for ($i=1; $i<count($song_structure); $i++) {
            $temp [] = $song_structure[$i];
            unset($song_structure[$i]);
        }

        $song_structure[][0] = $metranome_id;

        foreach ($temp as $t) {
            $song_structure[] = $t;
        }

        $song = new Song("New Smart Song_".$song->title."_".$increment."_".$similarity."_".$this->generateRandomString(5), "Drew Jex", $song_structure);
        $song->parts = $parts;

        return $song;
    }

    public function makeSmarter($song, $increment, $similarity) {

        $patterns = $this->music_generator->getPatternsByIncrement($song, $increment, $similarity);
        $music_changer = new MusicChanger();

        $piece_per_measure = floor(16/$increment);

        $song_structure = array();
        $parts = array();

        $first_loop = true;
        $aligned_notes = array();

        foreach ($patterns as $object) {
            $pattern = $object->patterns;
            $structure = $object->structure;

            /*echo "<pre>";
            echo print_r($structure);
            echo "</pre>";*/

            $proposed_structure = array();
            $str = null;
            for ($i=0; $i<max(array_keys($structure))+1+(16/$increment); $i++) {
                if ($i % $piece_per_measure == 0) {
                    if ($str != null)
                        $proposed_structure[] = substr($str, 0, -1);
                    $str = null;
                }
                if (array_key_exists($i, $structure)) {
                    $str .= (string) $structure[$i].'|';
                } else {
                    $str .= 'N|';
                }
            } //may need to add padding at end if it ends mid-measure - keep in mind

            //now go through and assign each value a new number if it's the first time it's been seen
            $new_structure = array();
            foreach ($proposed_structure as $key => $value) {
                $new_structure[$value][] = $key;
            }

            $final_structure = array();
            $count = 0;
            foreach ($new_structure as $key => $value) {
                foreach ($value as $v) {
                    $final_structure[$v] = $count;
                }
                $count++;
            }

            ksort($final_structure);

            foreach ($final_structure as $key => $value) {
                $final_structure[$key] = $value+count($parts);
            }

            echo "<pre>";
            echo print_r($proposed_structure);
            echo "</pre>";

            $pieces = array();
            foreach ($pattern as $key => $value) {
                $max_score = -1;
                $max_piece = null;
                for ($j=0; $j<100; $j++) {
                    $piece = array();
                    if ($first_loop) {
                        for ($i=0; $i<$value->num_notes; $i++) {
                            $piece = $music_changer->addNoteFromSackInPiece($piece, $value->note_sack, $increment);
                        }
                    } else {
                        for ($i=0; $i<$value->num_notes; $i++) {
                            $aligned_piece = $aligned_notes[$key];
                            $rand_note_index = rand(0, count($aligned_piece)-1);
                            $time = $aligned_piece[$rand_note_index]->time;
                            $piece = $music_changer->addNoteFromSackInPieceAt($piece, $value->note_sack, $time, $increment);
                        }
                    }
                    $part = new Part();
                    $measures = array();
                    $chords = array();
                    $chords[] = new Chord(0, "MAJOR", $increment);
                    $measures[] = new Measure($piece, $chords);

                    $part = Part::setMeasures($measures);
                    $part->instrument = 1;
                    $score = MusicAnalyzer::getScore($part);
                    if ($score > $max_score) {
                        $max_score = $score;
                        $max_piece = $piece;
                    }
                }
                if ($first_loop) {
                    $aligned_notes[$key] = $max_piece;
                }
                $pieces[] = $max_piece;
            }

            $first_loop = false;

            foreach ($new_structure as $key => $value) {
                $measures = array();
                $chords = array();
                $notes = array();
                $measure_pieces = explode("|", $key);
                $count = 0;
                foreach ($measure_pieces as $kp => $vp) {
                    $first = true;
                    if ($vp == 'N') {
                        //$notes[] = new Note(array(-1), "NORMAL", $count*$increment, $increment);
                    } else {
                        //make a copy of the piece
                        $copy = array();
                        foreach ($pieces[$vp] as $k => $v) {
                            $copy[$k] = clone $v;
                        }
                        foreach ($copy as $k => $v) {
                            /*if ($first) {
                                $first = false;
                                if ($copy[$k]->time != 0) {
                                    $notes[] = new Note(array(-1), "NORMAL", $count*$increment, $copy[$k]->time);
                                }
                            }*/
                            $copy[$k]->time += $count*$increment;
                            $notes[] = $copy[$k];
                        }
                    }
                    $count++;
                }
                ksort($notes);
                $chords[] = new Chord(0, "MAJOR", 16);
                $measures[] = new Measure($notes, $chords);

                $part = Part::setMeasures($measures);
                $part->instrument = 1;
                
                $parts[] = $part;
            }

            $song_structure[] = $final_structure;
        }

        $part = new Part();
        $measures = array();
        $offset = 0;
        for ($i=0; $i<count($song_structure[0]); $i++) {
            $notes = array();
            $chords = array();
            $chords[] = new Chord(0, "MAJOR", 16);
            for ($j=0; $j<4; $j++) {
                $notes[] = new Note([60], "NORMAL", 4*$j, 4);
            }
                    
            $measures[] = new Measure($notes, $chords);
            
        }

        $part = Part::setMeasures($measures);
        $part->instrument = 117; //10 //99
        $parts[] = $part;
        $metranome_id = count($parts)-1;

        //$song_structure[][0] = $metranome_id;

        $temp = array();
        for ($i=1; $i<count($song_structure); $i++) {
            $temp [] = $song_structure[$i];
            unset($song_structure[$i]);
        }

        $song_structure[][0] = $metranome_id;

        foreach ($temp as $t) {
            $song_structure[] = $t;
        }

        $song = new Song("New Smarter Song_".$song->title."_".$increment."_".$similarity."_".$this->generateRandomString(5), "Drew Jex", $song_structure);
        $song->parts = $parts;

        return $song;
    }

    public function makeEvenSmarter($song) {
        $tree_maker = new TreeMaker(array('base_song' => $song));
        return $tree_maker->make();
    }

    public function play($file) {
         $this->music_player->playFile('/maker2/songs/'.$file);
    }

    public function toString($song) {
        echo "<pre>";
        print_r($song);
        echo "</pre>";
    }

}

?>