<?php
session_start();
require_once 'data.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Профиль';
include 'header.php';


$current_user = null;
foreach ($employees as $emp) {
    if ($emp['id'] == $_SESSION['user_id']) {
        $current_user = $emp;
        break;
    }
}


if (!$current_user) {
    $current_user = [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role'],
        'email' => $_SESSION['user_email'] ?? $_SESSION['user_name'] . '@test.ru',
        'phone' => '+7 (999) 123-45-67',
        'tasks' => 0,
        'project' => '—'
    ];
}


$user_tasks = array_filter($tasks, function($t) use ($current_user) {
    return $t['assignee_id'] == $current_user['id'];
});
$user_tasks_count = count($user_tasks);
$user_completed_tasks = count(array_filter($user_tasks, function($t) {
    return $t['status'] == 'done';
}));


$user_projects = array_unique(array_column($user_tasks, 'project'));
$user_projects_count = count($user_projects);
?>

<div class="profile-container">
 
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="profile-name"><?php echo htmlspecialchars($current_user['name']); ?></div>
        <div class="profile-role"><?php echo htmlspecialchars($current_user['role']); ?></div>
        <span class="profile-badge">Активен</span>
    </div>

  
    <div class="profile-grid">
        <div class="profile-card">
            <h3>Личная информация</h3>
            <div class="info-row">
                <span class="info-label">Полное имя:</span>
                <span class="info-value"><?php echo htmlspecialchars($current_user['name']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($current_user['email']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Телефон:</span>
                <span class="info-value"><?php echo htmlspecialchars($current_user['phone']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Должность:</span>
                <span class="info-value"><?php echo htmlspecialchars($current_user['role']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">ID в системе:</span>
                <span class="info-value">#<?php echo $current_user['id']; ?></span>
            </div>
        </div>

        <div class="profile-card">
            <h3>Статистика</h3>
            <div class="stat-row">
                <span class="stat-label">Активные задачи:</span>
                <span class="stat-value"><?php echo $user_tasks_count - $user_completed_tasks; ?></span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Выполнено задач:</span>
                <span class="stat-value"><?php echo $user_completed_tasks; ?></span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Всего задач:</span>
                <span class="stat-value"><?php echo $user_tasks_count; ?></span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Проектов:</span>
                <span class="stat-value"><?php echo $user_projects_count; ?></span>
            </div>
            <?php if ($current_user['role'] == 'Прораб'): ?>
            <div class="stat-row">
                <span class="stat-label">Эффективность:</span>
                <span class="stat-value">
                    <?php 
                    $efficiency = $user_tasks_count > 0 ? round(($user_completed_tasks / $user_tasks_count) * 100) : 0;
                    echo $efficiency . '%';
                    ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>

   
    <?php if ($current_user['role'] == 'Прораб' && !empty($user_tasks)): ?>
    <div class="profile-card" style="margin-top: 20px;">
        <h3>Мои задачи</h3>
        <div class="tasks-list compact">
            <?php foreach (array_slice($user_tasks, 0, 5) as $task): ?>
            <div class="task-item">
                <div class="task-info">
                    <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                    <div class="task-meta">
                        <span class="task-project"><?php echo htmlspecialchars($task['project']); ?></span>
                        <span class="task-deadline">до <?php echo date('d.m.Y', strtotime($task['deadline'])); ?></span>
                    </div>
                </div>
                <span class="status-badge status-<?php echo $task['status']; ?>">
                    <?php 
                    echo $task['status'] == 'todo' ? 'К выполнению' : 
                         ($task['status'] == 'inprogress' ? 'В работе' : 
                         ($task['status'] == 'review' ? 'На проверке' : 'Выполнено')); 
                    ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

   
    <?php if ($current_user['role'] == 'Менеджер'): ?>
    <div class="profile-card" style="margin-top: 20px;">
        <h3>Сводка по проектам</h3>
        <div class="stats-mini">
            <div class="stat-mini-item">
                <span class="stat-mini-label">Всего проектов:</span>
                <span class="stat-mini-value"><?php echo count($projects); ?></span>
            </div>
            <div class="stat-mini-item">
                <span class="stat-mini-label">Активных проектов:</span>
                <span class="stat-mini-value"><?php echo count(array_filter($projects, function($p) { return $p['status'] == 'active'; })); ?></span>
            </div>
            <div class="stat-mini-item">
                <span class="stat-mini-label">Всего задач:</span>
                <span class="stat-mini-value"><?php echo count($tasks); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

  
    <?php if ($current_user['role'] == 'Директор'): ?>
    <div class="profile-card" style="margin-top: 20px;">
        <h3>Ключевые показатели</h3>
        <div class="stats-mini">
            <div class="stat-mini-item">
                <span class="stat-mini-label">Общий бюджет:</span>
                <span class="stat-mini-value"><?php echo number_format(array_sum(array_column($projects, 'budget'))/1000000, 1); ?> млн ₽</span>
            </div>
            <div class="stat-mini-item">
                <span class="stat-mini-label">Сотрудников:</span>
                <span class="stat-mini-value"><?php echo count($employees); ?></span>
            </div>
            <div class="stat-mini-item">
                <span class="stat-mini-label">Выполнено задач:</span>
                <span class="stat-mini-value"><?php echo count(array_filter($tasks, function($t) { return $t['status'] == 'done'; })); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if ($current_user['role'] == 'Бухгалтер'): ?>
    <div class="profile-card" style="margin-top: 20px;">
        <h3>Финансовая сводка</h3>
        <div class="stats-mini">
            <div class="stat-mini-item">
                <span class="stat-mini-label">Всего актов:</span>
                <span class="stat-mini-value"><?php echo count($acts); ?></span>
            </div>
            <div class="stat-mini-item">
                <span class="stat-mini-label">На сумму:</span>
                <span class="stat-mini-value"><?php echo number_format(array_sum(array_column($acts, 'sum'))/1000000, 1); ?> млн ₽</span>
            </div>
            <div class="stat-mini-item">
                <span class="stat-mini-label">Оплачено:</span>
                <span class="stat-mini-value"><?php echo number_format(array_sum(array_column(array_filter($acts, function($a) { return $a['status'] == 'paid'; }), 'sum'))/1000000, 1); ?> млн ₽</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <button class="edit-btn" onclick="alert('Функция редактирования будет доступна позже')">
        <i class="fas fa-edit"></i> Редактировать профиль
    </button>
</div>


<?php include 'footer.php'; ?>