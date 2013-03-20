<?php

require 'db.class.php';
$db = new Db('localhost', 'spotify_01', '78XxIgKh', 'spotify_01');

if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'no_mode';
}

if ($mode == 'get_users') {
    $users = $db->firstRow('users');
    echo json_encode($users);
} else if ($mode == 'insert_user') {
    if (isset($_GET['data'])) {
        $user = $_GET['data'];
        $user = json_decode($user);
        $result = $db->insert('users', $user);
        echo json_encode($result);
    } else {
        echo json_encode(array('error' => true, 'errorMsg' => "This mode requires 'data'"));
    }
} else if ($mode == 'no_mode') {
    echo json_encode(array('error' => true, 'errorMsg' => 'No mode was given'));
}
?>
