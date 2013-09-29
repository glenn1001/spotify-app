<?php $title = 'Register'; ?>
<?php require 'header.php'; ?>
<?php if ($user->user == null) { ?>
    <form id="register" method="post">
        <div>
            <label for="lastfm">Last.fm username</label><input type="text" id="lastfm" />
        </div>
        <div>
            <label for="fName">Firstname</label><input type="text" id="fName"<?php if (isset($_SESSION['fb_firstname'])) { echo ' value="' . $_SESSION['fb_firstname'] . '"'; } ?> />
        </div>
        <div>
            <label for="lName">Lastname</label><input type="text" id="lName"<?php if (isset($_SESSION['fb_lastname'])) { echo ' value="' . $_SESSION['fb_lastname'] . '"'; } ?> /><br />
        </div>
        <div>
            <label for="email">Email</label><input type="text" id="email"<?php if (isset($_SESSION['fb_email'])) { echo ' value="' . $_SESSION['fb_email'] . '"'; } ?> /><br />
        </div>
        <div>
            <label for="password">Password</label><input type="password" id="password" /><br />
        </div>
        <div>
            <label for="password2">Repeat password</label><input type="password" id="password2" /><br />
        </div>
        <input type="submit" class="button" value="Register" />
    </form>
<?php } else { ?>
    <script>
        window.location='account.php';
    </script>
<?php } ?>
<?php require 'footer.php'; ?>
