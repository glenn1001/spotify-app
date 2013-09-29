<?php

session_start();

require_once 'class/db.class.php';
$db = new Db('localhost', 'spotify_01', '78XxIgKh', 'spotify_01');

require_once 'class/user.class.php';
$user = new User();

require_once 'class/browserCheck.class.php';
$browser = new Browser();

require_once 'class/lastfm.class.php';
$lastfm = new LastFM();

require_once 'class/achievement.class.php';
$achievement = new Achievement();

?>