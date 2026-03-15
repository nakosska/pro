<?php
session_start();
require_once 'data.php';

if ($_SESSION['user_role'] != 'Менеджер') {
    header('Location: projects.php');
    exit;
}

$page_title = 'Редактирование проекта';
include 'header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$storage = loadData();
$projects = &$storage['projects'];


$project = null;
$project_index = -1;
foreach ($projects as $index => $p) {
    if ($p['id'] == $id) {
        $project = $p;
        $project_index = $index;
        break;
    }
}

if (!$project) {
    header('Location: projects.php');
    exit;
}

$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $client = $_POST['client'] ?? '';
    $budget = (float)($_POST['budget'] ?? 0);
    $deadline = $_POST['deadline'] ?? '';
    $status = $_POST['status'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (empty($name) || empty($address) || empty($client)) {
        $error = 'Заполните все обязательные поля';
    } else {
      
        $projects[$project_index] = [
            'id' => $id,
            'name' => $name,
            'address' => $address,
            'client' => $client,
            'status' => $status,
            'progress' => $project['progress'],
            'budget' => $budget,
            'spent' => $project['spent'],
            'deadline' => $deadline,
            'start_date' => $project['start_date'],
            'tasks_total' => $project['tasks_total'],
            'tasks_completed' => $project['tasks_completed'],
            'workers' => $project['workers'],
            'manager' => $project['manager'],
            'manager_id' => $project['manager_id'],
            'description' => $description
        ];
        
        
        saveData($storage);
        
        $success = 'Проект успешно обновлен!';
        header('refresh:2;url=projects.php');
    }
}
?>

<div class="form-container">
    <h2>Редактирование проекта: <?php echo htmlspecialchars($project['name']); ?></h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="project-form">
        <div class="form-group">
            <label>Название проекта *</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($project['name']); ?>" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Адрес *</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($project['address']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Заказчик *</label>
                <input type="text" name="client" value="<?php echo htmlspecialchars($project['client']); ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Бюджет (₽)</label>
                <input type="number" name="budget" value="<?php echo $project['budget']; ?>">
            </div>
            
            <div class="form-group">
                <label>Срок сдачи</label>
                <input type="date" name="deadline" value="<?php echo $project['deadline']; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Статус</label>
            <select name="status">
                <option value="active" <?php echo $project['status'] == 'active' ? 'selected' : ''; ?>>Активен</option>
                <option value="pause" <?php echo $project['status'] == 'pause' ? 'selected' : ''; ?>>На паузе</option>
                <option value="planning" <?php echo $project['status'] == 'planning' ? 'selected' : ''; ?>>Планирование</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Описание</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Сохранить
            </button>
            <a href="projects.php" class="btn-secondary">
                <i class="fas fa-times"></i> Отмена
            </a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>