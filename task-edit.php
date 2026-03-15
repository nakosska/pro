<?php
session_start();
require_once 'data.php';

if ($_SESSION['user_role'] != 'Менеджер') {
    header('Location: tasks.php');
    exit;
}

$page_title = 'Редактирование задачи';
include 'header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$storage = loadData();
$tasks = &$storage['tasks'];
$projects = $storage['projects'] ?? [];
$employees = array_filter($storage['employees'] ?? [], function($e) {
    return $e['role'] == 'Прораб';
});

$task = null;
$task_index = -1;
foreach ($tasks as $index => $t) {
    if ($t['id'] == $id) {
        $task = $t;
        $task_index = $index;
        break;
    }
}

if (!$task) {
    header('Location: tasks.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $project_id = (int)($_POST['project_id'] ?? 0);
    $assignee_id = (int)($_POST['assignee_id'] ?? 0);
    $deadline = $_POST['deadline'] ?? '';
    $priority = $_POST['priority'] ?? 'medium';
    $status = $_POST['status'] ?? 'todo';
    $description = $_POST['description'] ?? '';
    
    // Находим название проекта
    $project_name = '';
    foreach ($projects as $p) {
        if ($p['id'] == $project_id) {
            $project_name = $p['name'];
            break;
        }
    }
    
    // Находим имя исполнителя
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
        $tasks[$task_index] = [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'project' => $project_name,
            'project_id' => $project_id,
            'assignee' => $assignee_name,
            'assignee_id' => $assignee_id,
            'deadline' => $deadline,
            'status' => $status,
            'priority' => $priority,
            'created_by' => $task['created_by'] ?? $_SESSION['user_name'],
            'created_at' => $task['created_at'] ?? date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        saveData($storage);
        $success = 'Задача успешно обновлена!';
        header('refresh:2;url=tasks.php');
    }
}
?>

<div class="form-container">
    <h2>Редактирование задачи</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="task-form">
        <div class="form-group">
            <label>Название задачи *</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($task['title']); ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Проект *</label>
                <select name="project_id" required>
                    <option value="">Выберите проект</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?php echo $project['id']; ?>" 
                            <?php echo $project['id'] == $task['project_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($project['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Исполнитель *</label>
                <select name="assignee_id" required>
                    <option value="">Выберите прораба</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo $emp['id']; ?>"
                            <?php echo $emp['id'] == $task['assignee_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($emp['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Срок выполнения *</label>
                <input type="date" name="deadline" required value="<?php echo $task['deadline']; ?>">
            </div>
            
            <div class="form-group">
                <label>Приоритет</label>
                <select name="priority">
                    <option value="low" <?php echo $task['priority'] == 'low' ? 'selected' : ''; ?>>Низкий</option>
                    <option value="medium" <?php echo $task['priority'] == 'medium' ? 'selected' : ''; ?>>Средний</option>
                    <option value="high" <?php echo $task['priority'] == 'high' ? 'selected' : ''; ?>>Высокий</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Статус</label>
                <select name="status">
                    <option value="todo" <?php echo $task['status'] == 'todo' ? 'selected' : ''; ?>>К выполнению</option>
                    <option value="inprogress" <?php echo $task['status'] == 'inprogress' ? 'selected' : ''; ?>>В работе</option>
                    <option value="review" <?php echo $task['status'] == 'review' ? 'selected' : ''; ?>>На проверке</option>
                    <option value="done" <?php echo $task['status'] == 'done' ? 'selected' : ''; ?>>Выполнено</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Описание задачи</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($task['description'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Сохранить
            </button>
            <a href="tasks.php" class="btn-secondary">
                <i class="fas fa-times"></i> Отмена
            </a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>