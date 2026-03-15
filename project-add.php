<?php
session_start();
require_once 'data.php';

if ($_SESSION['user_role'] != 'Менеджер') {
    header('Location: projects.php');
    exit;
}

$page_title = 'Новый проект';
include 'header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $client = $_POST['client'] ?? '';
    $budget = (float)($_POST['budget'] ?? 0);
    $deadline = $_POST['deadline'] ?? '';
    $description = $_POST['description'] ?? '';
    
    if (empty($name) || empty($address) || empty($client)) {
        $error = 'Заполните все обязательные поля';
    } else {
      
        $storage = loadData();
        
        
        $new_project = [
            'id' => getNextId('project'),
            'name' => $name,
            'address' => $address,
            'client' => $client,
            'status' => 'planning',
            'progress' => 0,
            'budget' => $budget,
            'spent' => 0,
            'deadline' => $deadline,
            'start_date' => date('Y-m-d'),
            'tasks_total' => 0,
            'tasks_completed' => 0,
            'workers' => 0,
            'manager' => $_SESSION['user_name'],
            'manager_id' => $_SESSION['user_id'],
            'description' => $description
        ];
        
      
        $storage['projects'][] = $new_project;
        saveData($storage);
        
        $success = 'Проект успешно создан!';
        header('refresh:2;url=projects.php');
    }
}
?>

<div class="form-container">
    <h2>Создание нового проекта</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="project-form">
        <div class="form-group">
            <label>Название проекта *</label>
            <input type="text" name="name" required placeholder="Например: ЖК 'Восход'">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Адрес *</label>
                <input type="text" name="address" required placeholder="ул. Ленина, 15">
            </div>
            
            <div class="form-group">
                <label>Заказчик *</label>
                <input type="text" name="client" required placeholder="ООО СтройИнвест">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Бюджет (₽)</label>
                <input type="number" name="budget" placeholder="1500000" value="0">
            </div>
            
            <div class="form-group">
                <label>Срок сдачи</label>
                <input type="date" name="deadline">
            </div>
        </div>
        
        <div class="form-group">
            <label>Описание</label>
            <textarea name="description" rows="4" placeholder="Описание проекта..."></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Создать проект
            </button>
            <a href="projects.php" class="btn-secondary">
                <i class="fas fa-times"></i> Отмена
            </a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>