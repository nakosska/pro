<?php
session_start();
require_once 'data.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];


$storage = loadData();
$task_found = false;


foreach ($storage['tasks'] as &$task) {
    if ($task['id'] == $task_id) {
        $task_found = true;
        
       
        if ($user_role == 'Прораб' && $task['assignee_id'] != $user_id) {
            $_SESSION['error'] = 'Это не ваша задача';
            header('Location: tasks.php');
            exit;
        }
        
    
        if ($task['status'] == 'done') {
            $task['status'] = 'todo';
        } else {
            $task['status'] = 'done';
        }
        break;
    }
}


if ($task_found) {
    saveData($storage);
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'tasks.php';
header('Location: ' . $redirect);
exit;
?>