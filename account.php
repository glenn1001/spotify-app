<?php $title = 'Profile'; ?>
<?php require 'header.php'; ?>
<?php if ($user->user == null) { ?>
    <?php $user->showProfileForm(); ?>
<?php } else { ?>
    <div id="my_profile">
        <h2>Your profile</h2>
        <p>
            <strong>E-Mail:</strong> <?php echo $user->user->email; ?>
        </p>
        <p>
            <strong>Last.fm username:</strong> <?php echo $user->user->lastfm; ?>
        </p>
        <p>
            <strong>Firstname:</strong> <?php echo $user->user->firstname; ?>
        </p>
        <p>
            <strong>Lastname:</strong> <?php echo $user->user->lastname; ?>
        </p>
        <button class="showupdate button">Change information</button>
        <div id="update_user">
            <form method="post">
                <p>
                    <label for="lastfm">Last.fm username:</label><input type="text" placeholder="Last.fm username" id="lastfm" value="<?php echo $user->user->lastfm; ?>" />
                </p>
                <p>
                    <label for="fname">Firstname:</label><input type="text" placeholder="Firstname" id="fname" value="<?php echo $user->user->firstname; ?>" />
                </p>
                <p>
                    <label for="lname">Lastname:</label><input type="text" placeholder="Lastname" id="lname" value="<?php echo $user->user->lastname; ?>" />
                </p>
                <button class="update button">Update</button>
            </form>
        </div>
    </div>
<?php } ?>
<?php require 'footer.php'; ?>