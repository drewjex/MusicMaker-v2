<?php

    require_once '../settings.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Music Maker Client - by Drew Jex</title>

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="custom-style.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">

    <!-- Theme CSS -->
    <link href="css/grayscale.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

    <!-- Navigation -->
    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    Menu <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand page-scroll" href="#page-top">
                    <i class="fa fa-play-circle"></i> <span class="light">Music</span> Maker <small>Beta</small>
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#test">Test</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#about">About</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#feedback">Feedback</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Intro Header -->
    <header class="intro">
        <div class="intro-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <h1 class="brand-heading">MusicMaker</h1>
                        <p class="intro-text">Try out what's been done on the Music Maker here.
                            <br>[Created by Drew Jex]</p>
                        <a href="#test" class="btn btn-circle page-scroll">
                            <i class="fa fa-angle-double-down animated"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Test Section -->
    <section id="test" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>Test Music Maker</h2>
                <p>Choose a song from the list provided, adjust your settings, and then compare the result!</p>
                <p>(1) Choose a song to imitate</p>
                <select id='select_song' class='btn-lg'>
                    <option value='twinkle.mid'>Twinkle, Twinkle Little Star</option>
                    <option value='A_Sky_Full_Of_Stars.mid'>A Sky Full of Stars</option>
                    <option value='Highway 101_2.mid'>Hwy 101</option>
                    <option value='Clocks.mid'>Clocks</option>
                    <option value='variations_on_waltz.mid'>Variations on a Waltz by Beethoven</option>
                    <option value='Fur_Elise.mid'>Fur Elise</option>
                    <option value='Gotta_Catch_Em_All.mid'>Gotta Catch Em' All</option>
                    <option value='just_the_way_you_are.mid'>Just the Way you are Bruno Mars</option>
                    <option value='What_a_Wonderful_World.mid'>What a Wonderful World</option>
                    <option value='clair_de_lune.mid'>Clair de Lune</option>
                    <option value='beethoven_opus22_4_format0.mid'>Beethoven Opus Rondo Allegro</option>
                    <option value='pathetique_3_format0.mid'>Beethoven Pathetique</option>
                    <option value='panic_at_disco.mid'>Panic at the Disco</option>
                    <option value='tobu_dreams.mid'>Dreams - Tobu</option>
                    <option value='Maple_Leaf_Rag_Scott_Joplin.mid'>Maple Leaf Rag</option>
                    <option value='The_Entertainer.mid'>The Entertainer</option>
                    <option value='HAPPY.mid'>Happy</option>
                    <option value='sound_of_music.mid'>Sound of Music</option>
                    <option value='star_wars.mid'>Star Wars</option>
                </select>
                <button onclick='playSong()' class="btn btn-default btn-lg">Play</button>
                <button onclick='stopSong()' class="btn btn-warning btn-lg">Stop</button>

                <div style='margin-top:50px;'></div>

                <p>(2) Adjust your settings</p>
                <input type='number' id='structure' class='btn-lg' placeholder='Structural Level (1-16)' min='1'/>
                <input type='number' id='similarity' class='btn-lg' placeholder='Similarity Value' min='1' />
                <br><br><br>

                <p>(3) Choose the creation style</p>
                <select id='select_style' class='btn-lg'>
                    <option value='random'>makeRandom()</option>
                    <option value='smart'>makeSmart()</option>
                    <option value='smarter'>makeSmarter()</option>
                </select>
                <br><br><br>

                <p>(4) Generate! <img id='loading' style='display:none' src='../client/img/loading.gif' width='75px' height='75px' /></p>
                <button onclick='generateSong()' class="btn btn-default btn-lg">Generate!</button>
                <button onclick='downloadSong()' class="btn btn-default btn-lg">Download</button>
                <button onclick='stopSong()' class="btn btn-warning btn-lg">Stop</button>

                <br/><br/>
                <p><small><a href='' id='view_struc' style='display:none;' onclick='viewSongStructure(event)'>View Song Structure</a></small></p>
            
                <div id='view_structure' style='display:none'></div>

            </div>
        </div>
    </section>

    <iframe id="iframe" style="display:none;"></iframe>

    <!-- About Section -->
    <section id="about" class="content-section text-center">
        <div class="download-section">
            <div class="container">
                <div class="col-lg-8 col-lg-offset-2">
                    <h2>About Music Maker</h2>
                    <p>MusicMaker was started in the Fall of 2015. I first wanted to build it so I could have some simple program generate new song ideas for me.</p>
                    <a href="https://wiki.cs.byu.edu/mind/musicmaker" class="btn btn-default btn-lg">Visit Wiki Page</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="feedback" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>Under Construction</h2>
                <!--<p>Feel free to email us to provide some feedback on our templates, give us suggestions for new templates and themes, or to just say hello!</p>
                <p><a href="mailto:feedback@startbootstrap.com">feedback@startbootstrap.com</a>
                </p>
                <ul class="list-inline banner-social-buttons">
                    <li>
                        <a href="https://twitter.com/SBootstrap" class="btn btn-default btn-lg"><i class="fa fa-twitter fa-fw"></i> <span class="network-name">Twitter</span></a>
                    </li>
                    <li>
                        <a href="https://github.com/IronSummitMedia/startbootstrap" class="btn btn-default btn-lg"><i class="fa fa-github fa-fw"></i> <span class="network-name">Github</span></a>
                    </li>
                    <li>
                        <a href="https://plus.google.com/+Startbootstrap/posts" class="btn btn-default btn-lg"><i class="fa fa-google-plus fa-fw"></i> <span class="network-name">Google+</span></a>
                    </li>
                </ul>-->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p>Copyright &copy; Drew Jex Music Maker 2017</p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="vendor/jquery/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>

    <!-- Google Maps API Key - Use your own API key to enable the map feature. More information on the Google Maps API can be found at https://developers.google.com/maps/ -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRngKslUGJTlibkQ3FkfTxj3Xss1UlZDA&sensor=false"></script>

    <!-- Theme JavaScript -->
    <script src="js/grayscale.min.js"></script>

    <!-- Listen JavaScript -->
    <script src="js/listen.js"></script>

    <script src='http://www.midijs.net/lib/midi.js'></script>

</body>

</html>
