<?php
session_start();
require_once 'data.php';

if ($_SESSION['user_role'] != 'Менеджер') {
    header('Location: tasks.php');
    exit;
}

$page_title = 'Новая задача';
include 'header.php';

$storage = loadData();
$projects = $storage['projects'] ?? [];
$employees = array_filter($storage['employees'] ?? [], function($e) {
    return $e['role'] == 'Прораб';
});

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $project_id = (int)($_POST['project_id'] ?? 0);
    $assignee_id = (int)($_POST['assignee_id'] ?? 0);
    $deadline = $_POST['deadline'] ?? '';
    $priority = $_POST['priority'] ?? 'medium';
    $description = $_POST['description'] ?? '';
    
 
    $project_name = '';
    foreach ($projects as $p) {
        if ($p['id'] == $project_id) {
            $project_name = $p['name'];
            break;
        }
    }
    

    $assignee_name = '';
    foreach ($storage['employees'] as $e) {
        if ($e['id'] == $assignee_id) {
            $assignee_name = $e['name'];
            break;
        }
    }
    
    if (empty($title) || empty($project_id) || empty($assignee_id) || empty($deadline)) {
        $error = 'Заполните все обязательные поля';
    } else {
        $new_task = [
            'id' => getNextId('task'),
            'title' => $title,
            'description' => $description,
            'project' => $project_name,
            'project_id' => $project_id,
            'assignee' => $assignee_name,
            'assignee_id' => $assignee_id,
            'deadline' => $deadline,
            'status' => 'todo',
            'priority' => $priority,
            'created_by' => $_SESSION['user_name'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $storage['tasks'][] = $new_task;
        
        foreach ($storage['projects'] as &$p) {
            if ($p['id'] == $project_id) {
                $p['tasks_total'] = ($p['tasks_total'] ?? 0) + 1;
                break;
            }
        }
        
        saveData($storage);
        $success = 'Задача успешно создана!';
        header('refresh:2;url=tasks.php');
    }
}
?>

<div class="form-container">
    <h2>Создание новой задачи</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="task-form">
        <div class="form-group">
            <label>Название задачи *</label>
            <input type="text" name="title" required placeholder="Например: Сделать замеры">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Проект *</label>
                <select name="project_id" required>
                    <option value="">Выберите проект</option>
                    <?php foreach ($projects as $project): ?>
                        <?php if ($project['status'] == 'active'): ?>
                        <option value="<?php echo $project['id']; ?>">
                            <?php echo htmlspecialchars($project['name']); ?>
                        </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Исполнитель *</label>
                <select name="assignee_id" required>
                    <option value="">Выберите прораба</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo $emp['id']; ?>">
                            <?php echo htmlspecialchars($emp['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Срок выполнения *</label>
                <input type="date" name="deadline" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label>Приоритет</label>
                <select name="priority">
                    <option value="low">Низкий</option>
                    <option value="medium" selected>Средний</option>
                    <option value="high">Высокий</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Описание задачи</label>
            <textarea name="description" rows="4" placeholder="Подробности задачи..."></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Создать задачу
            </button>
            <a href="tasks.php" class="btn-secondary">
                <i class="fas fa-times"></i> Отмена
            </a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
