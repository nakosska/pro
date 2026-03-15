<?php
session_start();
require_once 'data.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['user_role'] != 'Менеджер' && $_SESSION['user_role'] != 'Директор') {
    header('Location: dashboard.php');
    exit;
}

$page_title = 'Команда';
include 'header.php';


$storage = loadData();
$employees = $storage['employees'] ?? [];
$tasks = $storage['tasks'] ?? [];


foreach ($employees as &$emp) {
    $emp['tasks_count'] = 0;
    foreach ($tasks as $task) {
        if (isset($task['assignee_id']) && $task['assignee_id'] == $emp['id']) {
            $emp['tasks_count']++;
        }
    }
}
?>

<div class="page-header">
    <h2>Команда</h2>
    <?php if ($_SESSION['user_role'] == 'Менеджер'): ?>
    <button class="btn-primary" onclick="alert('Функция добавления сотрудника будет позже')">
        <i class="fas fa-user-plus"></i> Добавить сотрудника
    </button>
    <?php endif; ?>
</div>


<div class="search-section">
    <input type="text" id="employeeSearch" placeholder="Поиск по имени или должности..." class="search-input">
</div>

<div class="team-grid" id="employeesContainer">
    <?php if (empty($employees)): ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <p>Нет сотрудников для отображения</p>
        </div>
    <?php else: ?>
        <?php foreach ($employees as $emp): ?>
        <div class="team-card" data-name="<?php echo strtolower($emp['name'] ?? ''); ?>" 
             data-role="<?php echo strtolower($emp['role'] ?? ''); ?>"
             data-email="<?php echo strtolower($emp['email'] ?? ''); ?>">
            <div class="team-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <h3><?php echo htmlspecialchars($emp['name'] ?? 'Без имени'); ?></h3>
            <p class="team-role"><?php echo htmlspecialchars($emp['role'] ?? 'Сотрудник'); ?></p>
            <div class="team-info">
                <p><i class="fas fa-tasks"></i> <?php echo $emp['tasks_count'] ?? 0; ?> задач</p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($emp['phone'] ?? '+7 (999) 000-00-00'); ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($emp['email'] ?? 'email@test.ru'); ?></p>
            </div>
            <?php if ($_SESSION['user_role'] == 'Менеджер' && $emp['role'] == 'Прораб'): ?>
    <a href="task-add.php?assignee_id=<?php echo $emp['id']; ?>" class="btn-primary btn-small" style="margin-top: 10px; display: inline-block;">
        <i class="fas fa-plus"></i> Назначить задачу
    </a>
<?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>

document.getElementById('employeeSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.team-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const name = card.dataset.name || '';
        const role = card.dataset.role || '';
        const email = card.dataset.email || '';
        
        if (name.includes(searchTerm) || role.includes(searchTerm) || email.includes(searchTerm)) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
   
    const container = document.getElementById('employeesContainer');
    let noResults = document.getElementById('noResults');
    
    if (visibleCount == 0) {
        if (!noResults) {
            noResults = document.createElement('div');
            noResults.id = 'noResults';
            noResults.className = 'no-results';
            noResults.innerHTML = '<p>Ничего не найдено</p>';
            container.appendChild(noResults);
        }
    } else {
        if (noResults) {
            noResults.remove();
        }
    }
});
</script>

<?php include 'footer.php'; ?>