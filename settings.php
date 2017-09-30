<?php

session_start();

require_once 'config.php';

foreach (glob(DIR_SRC."*.php") as $filename)
{
    require_once $filename;
}

foreach (glob(DIR_MODEL."*.php") as $filename)
{
    require_once $filename;
}

foreach (glob(DIR_MAKERS."*.php") as $filename)
{
    require_once $filename;
}

?>