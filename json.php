<?php

require 'db.class.php';
$db = new Db('localhost', 'spotify_01', '78XxIgKh', 'spotify_01');

if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'no_mode';
}

if ($mode == 'get_users') {
    $users = $db->select('users');
    echo json_encode(array('data' => $users, 'error' => false));
} else if ($mode == 'insert_user') {
    if (isset($_GET['data'])) {
        $user = $_GET['data'];
        $user = json_decode($user);
        $db->insert('users', $user);
        echo json_encode(array('error' => false, 'response' => 'User was inserted correctly'));
    } else {
        echo json_encode(array('error' => true, 'errorMsg' => "This mode requires 'data'"));
    }
} else if ($mode == 'no_mode') {
    echo json_encode(array('error' => true, 'errorMsg' => 'No mode was given'));
}
?>
