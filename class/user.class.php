<?php

class User {

    public $user = null;

    public function __construct() {
        if (isset($_SESSION['email']) && isset($_SESSION['password'])) {
            if ($_SESSION['email'] != '' && $_SESSION['password'] != '') {
                $this->checkLogin($_SESSION['email'], $_SESSION['password']);
            } else {
                unset($_SESSION['email']);
                unset($_SESSION['password']);
            }
        } else {
            unset($_SESSION['email']);
            unset($_SESSION['password']);
        }
    }

    public function checkLogin($email, $password) {
        global $db;

        $user = $db->firstRow('user', null, "WHERE `email`='" . $db->escape($email) . "' AND `password`='" . $db->escape($password) . "'");
        if ($user['error']) {
            return array('error' => true, 'response' => 'Email/password combination was incorrect!');
        } else {
            $this->user = $user['data'];
            $_SESSION['email'] = $this->user->email;
            $_SESSION['password'] = $this->user->password;

            if (isset($_SESSION['facebook_id'])) {
                if ($this->user->facebook_id == '') {
                    $this->user->facebook_id = $_SESSION['facebook_id'];

                    $data = array(
                        'facebook_id' => $this->user->facebook_id
                    );
                    $db->update('user', $data, "WHERE `id`='" . $this->user->id . "'");
                    
                    $this->completeFacebookAchievement($this->user->id);
                }
                unset($_SESSION['fb_email']);
                unset($_SESSION['fb_firstname']);
                unset($_SESSION['fb_lastname']);
                unset($_SESSION['facebook_id']);
            }

            return array('error' => false, 'response' => 'Welcome back ' . $user['data']->firstname . '!');
        }
    }

    public function logout() {
        unset($_SESSION['email']);
        unset($_SESSION['password']);
    }

    public function register($data) {
        global $db;

        if (isset($_SESSION['facebook_id'])) {
            $data->facebook_id = $_SESSION['facebook_id'];
        }

        $result = $db->insert('user', $data);

        if ($result['error']) {
            return $result;
        } else {
            $user_id = $db->firstCell('user', 'id', "WHERE `facebook_id`='" . $_SESSION['facebook_id'] . "'");
            $this->completeFacebookAchievement($user_id['data']);
            
            unset($_SESSION['fb_email']);
            unset($_SESSION['fb_firstname']);
            unset($_SESSION['fb_lastname']);
            unset($_SESSION['facebook_id']);
        }

        $this->checkLogin($data->email, $data->password);
        return array('error' => false, 'response' => 'Thank you for registering, ' . $data->firstname . '.');
    }

    public function showProfileForm($no_facebook = false) {
        ?>
        <div id="profile-form">
            <?php if ($this->user == null) { ?>
                <?php if (!$no_facebook) { ?>
                    <button class="facebooklogin">Login with Facebook</button>
                    <em>or</em>
                    <!--                    <button class="twitterlogin">Login with Twitter</button>
                                        <em>or</em>-->
                <?php } ?>
                <form method="post">
                    <input type="text" placeholder="Listen-2-achieve email" name="email" class="email" />
                    <input type="password" placeholder="Password" name="password" class="password" />
                    <button class="login button">Login</button>
                    <a href="register.php" class="button">Register</a>
                    <a href="recover.php">Password forgotten?</a>
                </form>
            <?php } else { ?>
                <a href="account.php">My profile</a>
                <a href="achievement.php">My achievements</a>
                <a href="update_pass.php">Change password</a>
                <hr />
                <button class="logout button">Logout</button>
            <?php } ?>
        </div>
        <?php
    }

    public function generatePassword($length = 8) {

        // start with a blank password
        $password = "";

        // define possible characters - any character in this string can be
        // picked for use in the password, so if you want to put vowels back in
        // or add special characters such as exclamation marks, this is where
        // you should do it
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

        // we refer to the length of $possible a few times, so let's grab it now
        $maxlength = strlen($possible);

        // check for length overflow and truncate if necessary
        if ($length > $maxlength) {
            $length = $maxlength;
        }

        // set up a counter for how many characters are in the password so far
        $i = 0;

        // add random characters to $password until $length is reached
        while ($i < $length) {

            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, $maxlength - 1), 1);

            // have we already used this character in $password?
            if (!strstr($password, $char)) {
                // no, so it's OK to add it onto the end of whatever we've already got...
                $password .= $char;
                // ... and increase the counter by one
                $i++;
            }
        }

        // done!
        return $password;
    }
    
    private function completeFacebookAchievement($user_id) {
        global $achievement;
        
        $achievement->setUserId($user_id);
        $achievement->completeAchievement(25);
    }

}
?>