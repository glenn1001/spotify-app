<?php
require_once 'session.php';
?><!doctype html>
<html>
<head>
    <title><?php if (isset($title)) {echo $title . ' :: ';}?>Listen-2-achieve</title>

    <meta charset="UTF-8" />
    <meta name="google" value="notranslate" />
    <meta http-equiv="Content-Language" content="en" />

    <script type="text/javascript" src="plugin/jquery/jquery.js"></script>
    <script type="text/javascript" src="plugin/jquery/jquery-ui.js"></script>
    <script type="text/javascript" src="js/facebook.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
    <?php if ($browser->getBrowser() == 'Internet Explorer') { ?>
        <?php if ($browser->getVersion() < 10) { ?>
            <script type="text/javascript" src="js/ie.js"></script>
        <?php } ?>
    <?php } ?>
        
    <link href="plugin/jquery/jquery-ui.css" rel="stylesheet" type="text/css" >
    <link href="css/stylesheet.css" rel="stylesheet" type="text/css" >
    
    <?php if ($user->user != null) { ?>
    <script>
        user = <?php echo json_encode($user->user); ?>;
    </script>
    <?php } ?>
</head>
<body>
    <div id="fb-root"></div>
    <div id="container">
        <div id="header">
            <a href="/">
                <img id="logo" src="img/logo.png" alt="Listen-2-achieve" title="Listen-2-achieve">
            </a>
            <div id="profile">
                <div id="profile-header">My Profile</div>
                <div id="profile-dropdown">
                    <div id="profile-form">
                        <?php $user->showProfileForm(); ?>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div id="nav">
            <ul>
                <li>
                    <a href="how-to.php">How to join</a>
                </li>
                <li>
                    <a href="account.php">Profile</a>
                </li>
                <li>
                    <a href="achievement.php">Achievements</a>
                </li>
                <li>
                    <a href="ranking.php">Ranking</a>
                </li>
            </ul>
        </div>
        <div id="page">