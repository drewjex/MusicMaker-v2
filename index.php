<script src='http://www.midijs.net/lib/midi.js'></script>

<?php

require_once 'settings.php';

$debug = false;
$music_maker = new MusicMaker();

/*
 * Choose any file and play it back
 */
/*
$music_maker->play('Highway 101.mid'); 
*/


/*
 * Gets a file, converts it to MM format, then writes it back to MIDI and plays it
 */
/*
$song = $music_maker->read('Highway 101.mid');
$song->title = 'Revised Highway 101';
$music_maker->write($song, $debug);
$music_maker->play('Revised Highway 101.mid');
*/

/*
 * Gets a file and returns the patterns (per measure) for that file.
 */
/*
$song = $music_maker->read('Highway 101_2.mid');
$patterns = $music_maker->music_generator->getPatterns($song);
foreach ($patterns as $p) {
    echo "<pre>";
    echo print_r($p);
    echo "</pre>";
}
*/

/*
 * Gets a file and returns the patterns (per defined time increment) for that file.
 */
/* 
$song = $music_maker->read('Highway 101_2.mid');
$patterns = $music_maker->music_generator->getPatternsByIncrement($song, 8);
foreach ($patterns as $p) {
    echo "<pre>";
    echo print_r($p);
    echo "</pre>";
}
*/

/*
 * Gets a file and plays a song similar to it with level of structure and similarity-value provided
 * makeRandom(SONG_DATA_MODEL, STRUCTURAL_LEVEL, SIMILARITY_VALUE)
 */
 /*
$song = $music_maker->read('Highway 101_2.mid');
$new_song = $music_maker->makeRandom($song, 4, 1);
//echo "<pre>";
//echo print_r($new_song); 
//echo "</pre>";
$music_maker->write($new_song, $debug);
$music_maker->play("Brand New Song.mid");
*/

//$music_maker->play('twinkle.mid');

//change = create note, delete note, change note characteristic (only looking at beginning of notes).

/*$piece1 = array();
$piece1[] = new Note(array(68), "NORMAL", 1, 1);
$piece1[] = new Note(array(62), "NORMAL", 2, 2);
$piece1[] = new Note(array(62), "NORMAL", 4, 4);

$piece2 = array();
$piece2[] = new Note(array(60), "NORMAL", 1, 1);
$piece2[] = new Note(array(62), "NORMAL", 2, 2);
$piece2[] = new Note(array(62), "NORMAL", 6, 2);

echo "SIMILARITY: ".MusicAnalyzer::getDiffRhythmic($piece1, $piece2)."<br>";*/

$array1 = array(1, 1, 1, 1, 3, 4);
$array2 = array(5, 6, 1, 1, 1, 1);

echo count(array_intersect($array1, $array2));

$song = $music_maker->read('Highway 101_2.mid'); //Highway 101_2
$new_song = $music_maker->make('RepeatRhythmsAllTracks', array('song' => $song));
$music_maker->write($new_song, $debug);
$music_maker->play("Brand New Song.mid");


/*
$songs = array();
$songs[] = $music_maker->read('Highway 101_2.mid');
$songs[] = $music_maker->read('A_Sky_Full_Of_Stars.mid');

$new_songs = array();

$new_song = $music_maker->make('CombineSongs', array('songs' => $songs));
$music_maker->write($new_song, $debug);
$music_maker->play("Brand New Song.mid");
*/

/*foreach ($songs as $s) {
    $song = $music_maker->read($s);
    $new_songs[] = $music_maker->make('RepeatRhythmsSmarter', array('song' => $song));
}*/

//now we have all these songs in an array. What should we do with them? 


//$song = $music_maker->play('smarter_rr_84rhythm_81notes_3.mid');



?>