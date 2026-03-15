<?php
session_start();
require_once 'data.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Проекты';
include 'header.php';

$user_role = $_SESSION['user_role'];


$storage = loadData();
$projects = $storage['projects'] ?? [];

$deleted = isset($_GET['deleted']);
?>

<div class="page-header">
    <h2>Все проекты</h2>
    <?php if ($user_role == 'Менеджер'): ?>
    <a href="project-add.php" class="btn-primary">
        <i class="fas fa-plus"></i> Новый проект
    </a>
    <?php endif; ?>
</div>

<?php if ($deleted): ?>
    <div class="alert alert-success">Проект удален</div>
<?php endif; ?>


<div class="search-section">
    <input type="text" id="projectSearch" placeholder="Поиск по названию, адресу или заказчику..." class="search-input">
</div>

<div class="projects-grid" id="projectsContainer">
    <?php if (empty($projects)): ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <p>Нет проектов</p>
        </div>
    <?php else: ?>
        <?php foreach ($projects as $project): ?>
        <div class="project-card" data-name="<?php echo strtolower($project['name'] ?? ''); ?>" 
             data-address="<?php echo strtolower($project['address'] ?? ''); ?>"
             data-client="<?php echo strtolower($project['client'] ?? ''); ?>">
            <div class="project-header">
                <h3><?php echo htmlspecialchars($project['name'] ?? 'Без названия'); ?></h3>
                <span class="project-status status-<?php echo $project['status'] ?? 'planning'; ?>">
                    <?php 
                    $status = $project['status'] ?? 'planning';
                    echo $status == 'active' ? 'Активен' : 
                         ($status == 'pause' ? 'На паузе' : 'Планирование'); 
                    ?>
                </span>
            </div>
            <p class="project-address">
                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($project['address'] ?? 'Адрес не указан'); ?>
            </p>
            <p class="project-client">
                <i class="fas fa-user"></i> <?php echo htmlspecialchars($project['client'] ?? 'Клиент не указан'); ?>
            </p>
            <div class="project-progress">
                <div class="progress-info">
                    <span>Прогресс</span>
                    <span><?php echo $project['progress'] ?? 0; ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $project['progress'] ?? 0; ?>%"></div>
                </div>
            </div>
            <div class="project-meta">
                <span><i class="fas fa-tasks"></i> <?php echo ($project['tasks_completed'] ?? 0); ?>/<?php echo ($project['tasks_total'] ?? 0); ?> задач</span>
                <span><i class="fas fa-users"></i> <?php echo $project['workers'] ?? 0; ?> чел</span>
                <span><i class="fas fa-ruble-sign"></i> <?php echo number_format(($project['budget'] ?? 0)/1000000, 1); ?> млн</span>
            </div>
            <div class="project-footer">
                <span><i class="fas fa-calendar"></i> до <?php echo date('d.m.Y', strtotime($project['deadline'] ?? 'now')); ?></span>
                <span><i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($project['manager'] ?? 'Не назначен'); ?></span>
            </div>
            
            <?php if ($user_role == 'Менеджер'): ?>
            <div class="project-actions" style="margin-top: 15px; display: flex; gap: 10px; justify-content: flex-end;">
                <a href="project-edit.php?id=<?php echo $project['id']; ?>" class="btn-icon" title="Редактировать">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="project-delete.php?id=<?php echo $project['id']; ?>" class="btn-icon" title="Удалить" 
                   onclick="return confirm('Вы уверены, что хотите удалить проект?')">
                    <i class="fas fa-trash"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>

document.getElementById('projectSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.project-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const name = card.dataset.name || '';
        const address = card.dataset.address || '';
        const client = card.dataset.client || '';
        
        if (name.includes(searchTerm) || address.includes(searchTerm) || client.includes(searchTerm)) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
  
    const container = document.getElementById('projectsContainer');
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