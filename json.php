<?php

require_once 'session.php';

if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'no_mode';
}

switch ($mode) {
    case 'login2':
        $users = $db->firstRow('user', null, "WHERE `id`='36'");
        $data = $users['data'];

        echo json_encode($user->checkLogin($data->email, $data->password));
        break;
    case 'login':
        if (isset($_GET['data'])) {
            $data = json_decode($_GET['data']);
            echo json_encode($user->checkLogin($data->email, $data->password));
        } else {
            echo json_encode(array('error' => true, 'response' => "This mode requires 'data'!"));
        }
        break;
    case 'logout':
        $user->logout();
        echo json_encode(array('error' => false, 'response' => "Successfull logout"));
        break;
    case 'register':
        if (isset($_GET['data'])) {
            $data = json_decode($_GET['data']);
            echo json_encode($user->register($data));
        } else {
            echo json_encode(array('error' => true, 'response' => "This mode requires 'data'!"));
        }
        break;
    case 'facebook_login':
        if (isset($_GET['data'])) {
            $data = json_decode($_GET['data']);
            $_SESSION['fb_email'] = $data->email;
            $_SESSION['fb_firstname'] = $data->firstname;
            $_SESSION['fb_lastname'] = $data->lastname;
            $_SESSION['facebook_id'] = $data->facebook_id;

            $result = $db->firstRow('user', null, "WHERE `facebook_id`='" . $data->facebook_id . "'");
            if ($result['error']) {
                echo json_encode(array('error' => false, 'response' => 'Not found'));
            } else {
                echo json_encode($user->checkLogin($result['data']->email, $result['data']->password));
            }
        } else {
            echo json_encode(array('error' => true, 'response' => "This mode requires 'data'!"));
        }
        break;
    case 'facebook_register':
        if (isset($_SESSION['facebook_id'])) {
            $data = (object) array(
                        'email' => $_SESSION['facebook_id'] . '@facebook-anonymous.com',
                        'password' => md5($user->generatePassword()),
                        'firstname' => $_SESSION['fb_firstname'],
                        'lastname' => $_SESSION['fb_lastname'],
                        'facebook_id' => $_SESSION['facebook_id']
            );
            echo json_encode($user->register($data));
        } else {
            echo json_encode(array('error' => true, 'response' => "You aren't logged in with facebook!"));
        }
        break;
    case 'facebook_share':
        if (isset($_GET['data'])) {
            if ($user->user != null) {
                $achievement->setUserId($user->user->id);
                $achievement->completeAchievement(26);

                echo json_encode(array('error' => false, 'response' => "Facebook share achievement is completed!"));
            } else {
                echo json_encode(array('error' => true, 'response' => "You aren't logged in!"));
            }
        } else {
            echo json_encode(array('error' => true, 'response' => "This mode requires 'data'!"));
        }
        break;
    case 'check_login':
        if ($user->user != null) {
            echo json_encode(array('error' => false, 'data' => $user->user, 'response' => "Hello, " . $user->user->firstname . "!"));
        } else {
            echo json_encode(array('error' => true, 'response' => "You aren't logged in!"));
        }
        break;
    case 'get_users':
        $users = $db->select('user');
        echo json_encode($users);
        break;
    case 'insert_user':
        if (isset($_GET['data'])) {
            $user = $_GET['data'];
            $user = json_decode($user);
            $result = $db->insert('user', $user);
            echo json_encode($result);
        } else {
            echo json_encode(array('error' => true, 'response' => "This mode requires 'data'!"));
        }
        break;
    case 'get_achievements':
        $achievements = $db->select('achievement');
        echo json_encode($achievements);
        break;
    case 'get_user_achievements':
        $user_achievements = $db->query("SELECT `a`.*, `ua`.`date` FROM `achievement` AS `a` INNER JOIN `user_achievement` AS `ua` ON `a`.`id`=`ua`.`achievement_id` WHERE `ua`.`user_id`='" . $user->user->id . "' ORDER BY `ua`.`date` DESC");
        $where = 'WHERE';
        if (is_array($user_achievements['data'])) {
            foreach ($user_achievements['data'] as $achievement) {
                if ($where == 'WHERE') {
                    $where .= " `id`!='" . $achievement->id . "'";
                } else {
                    $where .= " AND `id`!='" . $achievement->id . "'";
                }
            }
        }
        if ($where != 'WHERE') {
            $achievements = $db->select('achievement', null, $where);
            $user_achievements['data'] = array_merge((array) $user_achievements['data'], (array) $achievements['data']);
        } else {
            $user_achievements = $db->select('achievement');
        }

        echo json_encode($user_achievements);
        break;
    case 'get_achievements_by_user':
        if (isset($_GET['data'])) {
            $user = $_GET['data'];
            $user = json_decode($user);
            $user_id = $user->id;

            $user_achievements = $db->query("SELECT `a`.*, `ua`.`date` FROM `achievement` AS `a` INNER JOIN `user_achievement` AS `ua` ON `a`.`id`=`ua`.`achievement_id` WHERE `ua`.`user_id`='" . $user_id . "' ORDER BY `ua`.`date` DESC");
            $where = 'WHERE';
            if (is_array($user_achievements['data'])) {
                foreach ($user_achievements['data'] as $achievement) {
                    if ($where == 'WHERE') {
                        $where .= " `id`!='" . $achievement->id . "'";
                    } else {
                        $where .= " AND `id`!='" . $achievement->id . "'";
                    }
                }
            }
            if ($where != 'WHERE') {
                $achievements = $db->select('achievement', null, $where);
                $user_achievements['data'] = array_merge((array) $user_achievements['data'], (array) $achievements['data']);
            } else {
                $user_achievements = $db->select('achievement');
            }

            echo json_encode($user_achievements);
        } else {
            echo json_encode(array('error' => true, 'response' => "This mode requires 'data'!"));
        }
        break;
    case 'get_user_achievement_score':
        $result = $db->query("SELECT SUM(points) AS `total` FROM `achievement` AS `a` INNER JOIN `user_achievement` AS `ua` ON `a`.`id`=`ua`.`achievement_id` WHERE `ua`.`user_id`='" . $user->user->id . "'");
        if ($result['data'][0]->total === null) {
            $result['data'][0]->total = 0;
        }
        echo json_encode($result);

        break;
    case 'get_achievement_score_by_user':
        if (isset($_GET['data'])) {
            $user = $_GET['data'];
            $user = json_decode($user);
            $user_id = $user->id;

            $result = $db->query("SELECT SUM(points) AS `total` FROM `achievement` AS `a` INNER JOIN `user_achievement` AS `ua` ON `a`.`id`=`ua`.`achievement_id` WHERE `ua`.`user_id`='" . $user_id . "'");
            if ($result['data'][0]->total === null) {
                $result['data'][0]->total = 0;
            }
            echo json_encode($result);
        } else {
            echo json_encode(array('error' => true, 'response' => "This mode requires 'data'!"));
        }
        break;
    case 'ranking':
        $result = $db->query("SELECT `ua`.`user_id`, SUM(`a`.`points`) AS `total`, `u`.`firstname`, `u`.`lastname` FROM `achievement` AS `a` INNER JOIN `user_achievement` AS `ua` ON `a`.`id`=`ua`.`achievement_id` LEFT JOIN `user` AS `u` ON `ua`.`user_id`=`u`.`id` GROUP BY `ua`.`user_id` ORDER BY `total` DESC, `ua`.`user_id` ASC");
        echo json_encode($result);
        break;
    case 'ranking_by_page':
        if (isset($_GET['data'])) {
            $data = $_GET['data'];
            $page = $db->escape($data['page']);
            $limit = ($page - 1) * 10;

            $result = $db->query("SELECT `ua`.`user_id`, SUM(`a`.`points`), `u`.`firstname`, `u`.`lastname` AS `total` FROM `achievement` AS `a` INNER JOIN `user_achievement` AS `ua` ON `a`.`id`=`ua`.`achievement_id` LEFT JOIN `user` AS `u` ON `ua`.`user_id`=`u`.`id` GROUP BY `ua`.`user_id` ORDER BY `total` DESC, `ua`.`user_id` ASC LIMIT " . $limit . ",10");
            echo json_encode($result);
        } else {
            echo json_encode(array('error' => true, 'response' => "This mode requires 'data'!"));
        }
        break;
    case 'update_profile':
        if (isset($_GET['data'])) {
            $data = json_decode($_GET['data']);

            if ($user->user != null) {
                $db->update('user', $data, "WHERE `id`='" . $user->user->id . "'");
                echo json_encode(array('error' => false, 'response' => "Profile has been updated successfully!"));
            } else {
                echo json_encode(array('error' => true, 'response' => "You are not logged in!"));
            }
        } else {
            echo json_encode(array('error' => true, 'response' => "This mode requires 'data'!"));
        }

        break;
    case 'get_tracks':
        $users = $db->select('user', null, "WHERE `lastfm` IS NOT NULL AND `lastfm`!=''");
        if (!$users['error']) {
            foreach ($users['data'] as $tmpUser) {
                $lastfm->getTracks($tmpUser->id, $tmpUser->lastfm);
                echo json_encode(array('error' => false, 'response' => "All user tracks and achievements have been updated!"));
            }
        } else {
            echo json_encode($users);
        }
        break;
    case 'get_user_tracks':
        if ($user->user != null) {
            echo json_encode($lastfm->getTracks($user->user->id, $user->user->lastfm));
        } else {
            echo json_encode(array('error' => true, 'response' => "You are not logged in!"));
        }
        break;
    case 'no_mode':
        echo json_encode(array('error' => true, 'response' => 'No mode was given!'));
        break;
    default:
        echo json_encode(array('error' => true, 'response' => 'This mode is unkown!'));
        break;
}
?>
