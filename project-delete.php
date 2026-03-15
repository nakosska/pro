<?php
session_start();
require_once 'data.php';

if ($_SESSION['user_role'] != 'Менеджер') {
    header('Location: projects.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$storage = loadData();


foreach ($storage['projects'] as $key => $project) {
    if ($project['id'] == $id) {
        unset($storage['projects'][$key]);
        $storage['projects'] = array_values($storage['projects']);
        break;
    }
}


saveData($storage);

header('Location: projects.php?deleted=1');
exit;
?>