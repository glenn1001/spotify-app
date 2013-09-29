<?php require 'header.php'; ?>
<script>
    window.fbAsyncInit = function() {
        // init the FB JS SDK
        FB.init({
            appId      : '136645209851772', // App ID from the App Dashboard
            channelUrl : '//www.listen-2-achieve.com/plugin/facebook/channel.php', // Channel File for x-domain communication
            status     : true, // check the login status upon init?
            cookie     : true, // set sessions cookies to allow your server to access the session?
            xfbml      : true  // parse XFBML tags on this page?
        });
        
        FB.login(function(response) {
            if (response.authResponse) {
                FB.api('/me', function(response) {
                    console.log(response);
                });
            } else {
                msg = 'You cancelled login or did not fully authorize.<br /><strong>You will be redirected to the homepage now.</strong>';
                div = $('<div>' + msg + '</div>');
                div.dialog({
                    title: 'Facebook login failed',
                    buttons: [ {
                            text: 'Close',
                            click: function () {
                                div.dialog('close');
                                window.location = '/';
                            }
                        }],
                    width: 400,
                    height: 250
                });
            }
        }, {
            scope: 'email,user_likes,publish_actions,user_checkins,friends_checkins,user_status,friends_status'
        });
    };

    // Load the SDK's source Asynchronously
    // Note that the debug version is being actively developed and might 
    // contain some type checks that are overly strict. 
    // Please report such bugs using the bugs tool.
    (function(d, debug){
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement('script');
        js.id = id;
        js.async = true;
        js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
        ref.parentNode.insertBefore(js, ref);
    }(document, /*debug*/ false));
</script>
<?php require 'footer.php'; ?>