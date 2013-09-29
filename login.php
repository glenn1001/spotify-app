<?php require 'header.php'; ?>
<?php if ($user->user == null) { ?>
    <?php $user->showProfileForm(true); ?>
<?php } else { ?>
    <script>
        window.location='account.php';
    </script>
<?php } ?>
<?php require 'footer.php'; ?>
