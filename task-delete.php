<?php
session_start();
require_once 'data.php';

if ($_SESSION['user_role'] != 'Менеджер') {
    header('Location: tasks.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$storage = loadData();

foreach ($storage['tasks'] as $key => $task) {
    if ($task['id'] == $id) {
        // Уменьшаем счетчик в проекте
        foreach ($storage['projects'] as &$p) {
            if ($p['id'] == $task['project_id']) {
                $p['tasks_total'] = max(0, ($p['tasks_total'] ?? 1) - 1);
                if ($task['status'] == 'done') {
                    $p['tasks_completed'] = max(0, ($p['tasks_completed'] ?? 1) - 1);
                }
                break;
            }
        }
        unset($storage['tasks'][$key]);
        $storage['tasks'] = array_values($storage['tasks']);
        break;
    }
}

saveData($storage);
header('Location: tasks.php?deleted=1');
exit;
?>