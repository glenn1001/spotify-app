<?php $title = 'Achievements'; ?>
<?php require 'header.php'; ?>
<?php if ($user->user == null) { ?>
    <div id="achievements"></div>
<?php } else { ?>
    <div id="my_achievements"></div>
<?php } ?>

<?php require 'footer.php'; ?>