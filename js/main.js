var user = null;
var shareButtons = [];
var url;
var data;
var refreshButton = null;

(function($){
    $.fn.extend({
        facebookLogin: function() {
            this.on('click', function(event) {
                if (event.preventDefault) {
                    event.preventDefault();
                } else {
                    event.returnValue = false;
                }
        
                FB.login(function(response) {
                    if (response.authResponse) {
                        FB.api('/me', function(response) {
                            data = {
                                'email': response.email,
                                'firstname': response.first_name,
                                'lastname': response.last_name,
                                'facebook_id': response.username
                            };
                            data = JSON.stringify(data);
                            
                            $.getJSON('json.php?mode=facebook_login', {
                                data: data
                            }, function(response) {
                                if (!response.error) {
                                    if (response.response == 'Not found') {
                                        msg = "You don't have an account yet. What do you want to do?";
                                        div = $('<div>' + msg + '</div>');
                                        div.dialog({
                                            title: 'Successfull Facebook login',
                                            buttons: [ {
                                                text: 'Register a new account',
                                                click: function () {
                                                    div.dialog('close');
                                                    window.location = 'register.php';
                                                }
                                            }, {
                                                text: 'Login into an existing account',
                                                click: function () {
                                                    div.dialog('close');
                                                    window.location = 'login.php';
                                                }
                                            }, {
                                                text: "Don't create an account",
                                                click: function () {
                                                    div.dialog('close');
                                                    $.getJSON('json.php?mode=facebook_register');
                                                    window.location='/';
                                                }
                                            }],
                                            width: 400,
                                            height: 310
                                        });
                                    } else {
                                        msg = response.response;
                                        div = $('<div>' + msg + '</div>');
                                        div.dialog({
                                            title: 'Successfull Facebook login',
                                            buttons: [ {
                                                text: 'Close',
                                                click: function () {
                                                    div.dialog('close');
                                                    location.reload();
                                                }
                                            }],
                                            width: 400,
                                            height: 250
                                        });
                                    }
                                }
                            });
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
            });
        },
        login: function () {
            var msg;
            var div;
            
            this.on('click', function(event) {
                if (event.preventDefault) {
                    event.preventDefault();
                } else {
                    event.returnValue = false;
                }
                
                var email = $(this).parent().find('input.email').val();
                var password = $(this).parent().find('input.password').val();
                
                if (email != '' || password != '') {
                    data = {
                        'email': email,
                        'password': md5(password)
                    };
                    data = JSON.stringify(data);
                    
                    $.getJSON('json.php?mode=login', {
                        data: data
                    }, function(response) {
                        if (response.error) {
                            msg = response.response;
                            div = $('<div>' + msg + '</div>');
                            div.dialog({
                                title: 'Login failed',
                                buttons: [ {
                                    text: 'Close',
                                    click: function () {
                                        div.dialog('close');
                                    }
                                }],
                                width: 400,
                                height: 200
                            });
                        } else {
                            msg = response.response;
                            div = $('<div>' + msg + '</div>');
                            div.dialog({
                                title: 'Login',
                                buttons: [ {
                                    text: 'Close',
                                    click: function () {
                                        div.dialog('close');
                                        location.reload();
                                    }
                                }],
                                width: 400,
                                height: 200
                            });
                        }
                    });
                } else {
                    msg = "You need to fill in a email and a password!";
                    div = $('<div>' + msg + '</div>');
                    div.dialog({
                        title: 'Login failed',
                        buttons: [ {
                            text: 'Close',
                            click: function () {
                                div.dialog('close');
                            }
                        }],
                        width: 400,
                        height: 200
                    });
                }
            });
        },
        logout: function() {
            this.on('click', function(event) {
                if (event.preventDefault) {
                    event.preventDefault();
                } else {
                    event.returnValue = false;
                }
                
                $.getJSON('json.php?mode=logout', null, function(response) {
                    if (response.error) {
                        msg = response.response;
                        div = $('<div>' + msg + '</div>');
                        div.dialog({
                            title: 'Logout failed',
                            buttons: [ {
                                text: 'Close',
                                click: function () {
                                    div.dialog('close');
                                }
                            }],
                            width: 400,
                            height: 200
                        });
                    } else {
                        msg = response.response;
                        div = $('<div>' + msg + '</div>');
                        div.dialog({
                            title: 'Logout',
                            buttons: [ {
                                text: 'Close',
                                click: function () {
                                    div.dialog('close');
                                    location.reload();
                                }
                            }],
                            width: 400,
                            height: 200
                        });
                    }
                });
            });
        },
        register: function() {
            this.on('submit', function(event) {
                if (event.preventDefault) {
                    event.preventDefault();
                } else {
                    event.returnValue = false;
                }
                if ($('#lastfm').val() == '' || $('#fName').val() == '' || $('#lName').val() == '' || $('#email').val() == '' || $('#password').val() == '' || $('#password2').val() == '') {
                    msg = "All fields are required! Please fill in all fields.";
                    div = $('<div>' + msg + '</div>');
                    div.dialog({
                        title: 'Register failed',
                        buttons: [ {
                            text: 'Close',
                            click: function () {
                                div.dialog('close');
                            }
                        }],
                        width: 400,
                        height: 200
                    });
                } else if ($('#password').val() != $('#password2').val()) {
                    $('#password').val('');
                    $('#password2').val('');
                    msg = "Both passwords aren't the same! Please correct them.";
                    div = $('<div>' + msg + '</div>');
                    div.dialog({
                        title: 'Register failed',
                        buttons: [ {
                            text: 'Close',
                            click: function () {
                                div.dialog('close');
                            }
                        }],
                        width: 400,
                        height: 200
                    });
                } else if (!isValidEmailAddress($('#email').val())) {
                    msg = "This isn't a valid email address! Please correct your email address.";
                    div = $('<div>' + msg + '</div>');
                    div.dialog({
                        title: 'Register failed',
                        buttons: [ {
                            text: 'Close',
                            click: function () {
                                div.dialog('close');
                            }
                        }],
                        width: 400,
                        height: 200
                    });
                } else {
                    data = {
                        'lastfm': $('#lastfm').val(),
                        'firstname': $('#fName').val(),
                        'lastname': $('#lName').val(),
                        'email': $('#email').val(),
                        'password': md5($('#password').val())
                    };
                    data = JSON.stringify(data);
                    
                    $.getJSON('json.php?mode=register', {
                        data: data
                    }, function(response) {
                        if (response.error) {
                            if (response.response.indexOf("Duplicate entry") != -1) {
                                response.response = 'This email address already existst!<br />Use another email address or login with this email address.';
                                var buttons = [ {
                                    text: 'Login with this email address',
                                    click: function () {
                                        div.dialog('close');
                                        window.location = 'account.php';
                                    }
                                }, {
                                    text: 'Use another email address',
                                    click: function () {
                                        div.dialog('close');
                                    }
                                }];
                            } else {
                                var buttons = [ {
                                    text: 'Close',
                                    click: function () {
                                        div.dialog('close');
                                    }
                                }];
                            }
                            
                            msg = response.response;
                            div = $('<div>' + msg + '</div>');
                            div.dialog({
                                title: 'Register failed',
                                buttons: buttons,
                                width: 400,
                                height: 300
                            });
                        } else {
                            msg = response.response;
                            div = $('<div>' + msg + '</div>');
                            div.dialog({
                                title: 'Registered',
                                buttons: [ {
                                    text: 'Close',
                                    click: function () {
                                        div.dialog('close');
                                        location.reload();
                                    }
                                }],
                                width: 400,
                                height: 200
                            });
                        }
                    });
                }
            });
        },
        get_achievements: function() {
            if (this.length != 0) {
                if (getUrlParam('user_id') == null) {
                    var div = this;
                    $.getJSON('json.php?mode=get_achievements', null, function(response) {
                        if (!response.error) {
                            for (var i = 0; i < response.data.length; i++) {
                                var achievement = response.data[i];
                                div.append('<div class="achievement"><div class="title"><h2>' + achievement.title + '</h2><p class="points">' + achievement.points + ' points</p></div><div class="description">' + achievement.description + '</div></div>');
                            }
                            div.append('<div class="clear"></div>')
                        }
                    });
                } else {
                    var div = this;
                    url = 'json.php?mode=get_achievements_by_user';
                    data = {
                        id: getUrlParam('user_id')
                    };
                    data = JSON.stringify(data);
                        
                    $.getJSON(url, {
                        data: data
                    }, function(response) {
                        if (!response.error) {
                            url = 'json.php?mode=get_achievement_score_by_user';
                            data = {
                                id: getUrlParam('user_id')
                            };
                            data = JSON.stringify(data);
                        
                            $.getJSON(url, {
                                data: data
                            }, function(response) {
                                if (!response.error) {
                                    div.prepend('<div class="score">Total achievement points: ' + response.data[0].total + '</div>');
                                }
                            });
                        
                            for (var i = 0; i < response.data.length; i++) {
                                var achievement = response.data[i];
                                if (achievement.date) {
                                    var achievementDiv = $('<div class="achievement"></div>');
                                    div.append(achievementDiv);
                                
                                    var completedDiv = $('<div class="completed"></div>');
                                    achievementDiv.append(completedDiv);
                                
                                    achievementDiv.append('<div class="title"><h2>' + achievement.title + '</h2><p class="points">' + achievement.points + ' points</p></div>');
                                    achievementDiv.append('<div class="description">' + achievement.description + '</div>');
                                } else {
                                    div.append('<div class="achievement"><div class="title"><h2>' + achievement.title + '</h2><p class="points">' + achievement.points + ' points</p></div><div class="description">' + achievement.description + '</div></div>');
                                }
                            }
                            div.append('<div class="clear"></div>')
                        }
                    });
                }
            }
        },
        my_achievements: function() {
            if (this.length != 0) {
                var div = this;
                if (getUrlParam('user_id') != null) {
                    url = 'json.php?mode=get_achievements_by_user';
                    data = {
                        id: getUrlParam('user_id')
                    };
                    data = JSON.stringify(data);
                } else {
                    url = 'json.php?mode=get_user_achievements';
                    data = null;
                }
                $.getJSON(url, {
                    data: data
                }, function(response) {
                    if (!response.error) {
                        if (getUrlParam('user_id') != null) {
                            url = 'json.php?mode=get_achievement_score_by_user';
                            data = {
                                id: getUrlParam('user_id')
                            };
                            data = JSON.stringify(data);
                        } else {
                            url = 'json.php?mode=get_user_achievement_score';
                            data = null;
                        }
                        
                        $.getJSON(url, {
                            data: data
                        }, function(response) {
                            if (!response.error) {
                                var headerDiv = $('<div class="score">Total achievement points: ' + response.data[0].total + '</div>')
                                div.prepend(headerDiv);
                                
                                refreshButton = $('<a class="check">[Check for new achievements]</a>');
                                refreshButton.on('click', function() {
                                    $.getJSON('json.php?mode=get_user_tracks', null, function(response) {
                                        location.reload();
                                    })
                                });
                                
                                if (refreshButton != null) {
                                    headerDiv.append(refreshButton);
                                }
                            }
                        });
                        
                        for (var i = 0; i < response.data.length; i++) {
                            var achievement = response.data[i];
                            if (achievement.date) {
                                var achievementDiv = $('<div class="achievement"></div>');
                                div.append(achievementDiv);
                                
                                var completedDiv = $('<div class="completed"></div>');
                                achievementDiv.append(completedDiv);
                                
                                if (getUrlParam('user_id') == null) {
                                    var img = new Image();
                                    img.className = 'facebook_share';
                                    img.src = 'img/facebook-share.png';
                                    img.prop = {
                                        'id': achievement.id,
                                        'title': achievement.title,
                                        'points': achievement.points,
                                        'description': achievement.description
                                    };
                                    img.onload = function() {
                                        $(this).on('click', function() {
                                            FB.ui({
                                                method: 'feed',
                                                link: 'http://www.listen-2-achieve.com/achievement.php',
                                                picture: 'http://www.listen-2-achieve.com/img/logo.png',
                                                name: user.firstname + ' completed: ' + this.prop.title,
                                                description: user.firstname + " completed: '" + this.prop.title + "' for " + this.prop.points + " achievement points at www.listen-2-achieve.com."
                                            }, function(response) {
                                                if (response) {
                                                    if (response.post_id) {
                                                        data = {
                                                            id: response.post_id
                                                        };
                                                        data = JSON.stringify(data);

                                                        $.getJSON('json.php?mode=facebook_share', {
                                                            data: data
                                                        });
                                                    }
                                                }
                                            });
                                        });
                                    };
                                
                                    completedDiv.append(img);
                                }
                                
                                var titleDiv = $('<div class="title"></div>');
                                achievementDiv.append(titleDiv);
                                
                                titleDiv.append('<h2>' + achievement.title + '</h2><p class="points">' + achievement.points + ' points</p>');
                                
                                achievementDiv.append('<div class="description">' + achievement.description + '</div>');
                            } else {
                                div.append('<div class="achievement"><div class="title"><h2>' + achievement.title + '</h2><p class="points">' + achievement.points + ' points</p></div><div class="description">' + achievement.description + '</div></div>');
                            }
                        }
                        div.append('<div class="clear"></div>')
                    }
                });
            }
        },
        ranking: function() {
            if (this.length != 0) {
                var div = this;
                $.getJSON('json.php?mode=ranking', null, function(response) {
                    if (response.error) {
                        console.log(response);
                    } else {
                        var table = '<table class="ranking"><thead><tr><th>Rank</th><th>Name</th><th>Achievement score</th><th>Actions</th></tr></thead><tbody>';
                        for (var i = 0; i < response.data.length; i++) {
                            var rankUser = response.data[i];
                            var rank = i + 1;
                            
                            if (user != null) {
                                if (rankUser.user_id == user.id) {
                                    table += '<tr class="me">';
                                } else {
                                    table += '<tr>';
                                }
                            } else {
                                table += '<tr>';
                            }
                            table += '<td>' + rank + '</td><td>' + rankUser.firstname + ' ' + rankUser.lastname + '</td><td>' + rankUser.total + '</td><td><a class="button" href="achievement.php?user_id=' + rankUser.user_id + '">View achievements</a></td></tr>';
                        }
                        table += '</tbody></table>';
                        div.append(table);
                    }
                });
            }
        },
        update_user: function(){
            this.on('click', function(event) {
                if (event.preventDefault) {
                    event.preventDefault();
                } else {
                    event.returnValue = false;
                }
                if ($('#fname').val() == '' || $('#lname').val() == '') {
                    msg = "All fields are required! Please fill in all fields.";
                    div = $('<div>' + msg + '</div>');
                    div.dialog({
                        title: 'One or more fields are empty.',
                        buttons: [ {
                            text: 'Close',
                            click: function () {
                                div.dialog('close');
                            }
                        }],
                        width: 400,
                        height: 200
                    });
                }else{
                    data = {
                        'lastfm': $('#lastfm').val(),
                        'firstname': $('#fname').val(),
                        'lastname': $('#lname').val()
                    };
                    data = JSON.stringify(data);
                
                    $.getJSON('json.php?mode=update_profile', {
                        data: data
                    }, function(response) {
                        if (response.error) {
                            msg = response.response;
                            div = $('<div>' + msg + '</div>');
                            div.dialog({
                                title: 'Update failed',
                                buttons: [ {
                                    text: 'Close',
                                    click: function () {
                                        div.dialog('close');
                                        location.reload();
                                    }
                                }],
                                width: 400,
                                height: 200
                            });
                        } else {
                            msg = response.response;
                            div = $('<div>' + msg + '</div>');
                            div.dialog({
                                title: 'Updated your profile!',
                                buttons: [ {
                                    text: 'Close',
                                    click: function () {
                                        div.dialog('close');
                                        location.reload();
                                    }
                                }],
                                width: 400,
                                height: 200
                            });
                        }
                    });
                }
            });
        }
    });
    
})(jQuery);

var isValidEmailAddress = function(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};

var getUrlParam = function(name) {
    return (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1];
};

$(window).load(function() {
    $("#profile-header").on('click', function(){
        $('#profile-dropdown').toggle("slow");
    });
    
    $(".showupdate").on('click', function(){
        $('#update_user').toggle("slow");
    });
    
    $('.facebooklogin').facebookLogin();
    $('.login').login();
    $('.logout').logout();
    $('#register').register();
    $('#achievements').get_achievements();
    $('#my_achievements').my_achievements();
    $('#ranking').ranking();
    $('.update').update_user();
});