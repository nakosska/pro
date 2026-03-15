<?php
session_start();
require_once 'data.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Дашборд';
include 'header.php';

$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$storage = loadData();
$projects = $storage['projects'] ?? [];
$tasks = $storage['tasks'] ?? [];
$employees = $storage['employees'] ?? [];
$acts = $storage['acts'] ?? [];
?>


<div class="stats-grid">
    <?php if ($user_role == 'Прораб'): ?>
        <?php 
        $my_tasks = array_filter($tasks, function($t) use ($user_id) {
            return ($t['assignee_id'] ?? 0) == $user_id;
        });
        $my_todo = count(array_filter($my_tasks, function($t) { return ($t['status'] ?? '') == 'todo'; }));
        $my_progress = count(array_filter($my_tasks, function($t) { return ($t['status'] ?? '') == 'inprogress'; }));
        $my_done = count(array_filter($my_tasks, function($t) { return ($t['status'] ?? '') == 'done'; }));
        ?>
        <div class="stat-card">
            <i class="fas fa-tasks stat-icon blue"></i>
            <div>
                <div class="stat-value"><?php echo $my_todo; ?></div>
                <div class="stat-label">К выполнению</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-spinner stat-icon orange"></i>
            <div>
                <div class="stat-value"><?php echo $my_progress; ?></div>
                <div class="stat-label">В работе</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle stat-icon green"></i>
            <div>
                <div class="stat-value"><?php echo $my_done; ?></div>
                <div class="stat-label">Выполнено</div>
            </div>
        </div>
        
    <?php elseif ($user_role == 'Менеджер'): ?>
        <div class="stat-card">
            <i class="fas fa-folder stat-icon blue"></i>
            <div>
                <div class="stat-value"><?php echo count($projects); ?></div>
                <div class="stat-label">Проектов</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-tasks stat-icon green"></i>
            <div>
                <div class="stat-value"><?php echo count($tasks); ?></div>
                <div class="stat-label">Задач</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-users stat-icon orange"></i>
            <div>
                <div class="stat-value"><?php echo count($employees); ?></div>
                <div class="stat-label">Сотрудников</div>
            </div>
        </div>
        
    <?php elseif ($user_role == 'Директор'): ?>
        <?php 
        $total_budget = array_sum(array_column($projects, 'budget'));
        $active_projects = count(array_filter($projects, function($p) { return ($p['status'] ?? '') == 'active'; }));
        ?>
        <div class="stat-card">
            <i class="fas fa-chart-line stat-icon blue"></i>
            <div>
                <div class="stat-value"><?php echo $active_projects; ?></div>
                <div class="stat-label">Активных проектов</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-ruble-sign stat-icon green"></i>
            <div>
                <div class="stat-value"><?php echo number_format($total_budget/1000000, 1); ?> млн</div>
                <div class="stat-label">Общий бюджет</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-users stat-icon orange"></i>
            <div>
                <div class="stat-value"><?php echo count($employees); ?></div>
                <div class="stat-label">Сотрудников</div>
            </div>
        </div>
        
    <?php elseif ($user_role == 'Бухгалтер'): ?>
        <?php 
        $total_acts = array_sum(array_column($acts, 'sum'));
        $paid_acts = array_sum(array_column(array_filter($acts, function($a) { return ($a['status'] ?? '') == 'paid'; }), 'sum'));
        ?>
        <div class="stat-card">
            <i class="fas fa-file-invoice stat-icon blue"></i>
            <div>
                <div class="stat-value"><?php echo count($acts); ?></div>
                <div class="stat-label">Актов</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle stat-icon green"></i>
            <div>
                <div class="stat-value"><?php echo number_format($paid_acts/1000000, 1); ?> млн</div>
                <div class="stat-label">Оплачено</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock stat-icon orange"></i>
            <div>
                <div class="stat-value"><?php echo number_format(($total_acts - $paid_acts)/1000000, 1); ?> млн</div>
                <div class="stat-label">Ожидает</div>
            </div>
        </div>
    <?php endif; ?>
</div>


<h2 style="margin: 30px 0 15px;">Текущие проекты</h2>
<div class="projects-grid">
    <?php 
    $display_projects = array_slice($projects, 0, 3);
    foreach ($display_projects as $project): 
    ?>
    <div class="project-card">
        <div class="project-header">
            <h3><?php echo htmlspecialchars($project['name'] ?? 'Без названия'); ?></h3>
            <span class="project-status status-<?php echo $project['status'] ?? 'planning'; ?>">
                <?php 
                $status = $project['status'] ?? 'planning';
                echo $status == 'active' ? 'Активен' : ($status == 'pause' ? 'На паузе' : 'Планирование'); 
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
        <div class="project-footer">
            <span><i class="fas fa-calendar"></i> до <?php echo date('d.m.Y', strtotime($project['deadline'] ?? 'now')); ?></span>
            <span><i class="fas fa-ruble-sign"></i> <?php echo number_format(($project['budget'] ?? 0)/1000000, 1); ?> млн</span>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (count($projects) > 3): ?>
    <div class="view-all">
        <a href="projects.php" class="btn-secondary">Все проекты →</a>
    </div>
    <?php endif; ?>
</div>





<?php if ($user_role == 'Менеджер'): ?>
<h2 style="margin: 30px 0 15px;">Последние созданные задачи</h2>
<div class="tasks-list">
    <?php 
    $recent_tasks = array_slice(array_reverse($tasks), 0, 5);
    foreach ($recent_tasks as $task): 
    ?>
    <div class="task-item">
        <div class="task-info">
            <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
            <div class="task-meta">
                <span class="task-project"><?php echo htmlspecialchars($task['project']); ?></span>
                <span class="task-user"><i class="fas fa-user"></i> <?php echo htmlspecialchars($task['assignee']); ?></span>
                <span class="task-deadline"><i class="fas fa-calendar"></i> до <?php echo date('d.m.Y', strtotime($task['deadline'])); ?></span>
            </div>
        </div>
        <span class="status-badge status-<?php echo $task['status']; ?>">
            <?php 
            echo $task['status'] == 'todo' ? 'К выполнению' : 
                 ($task['status'] == 'inprogress' ? 'В работе' : 
                 ($task['status'] == 'review' ? 'На проверке' : 'Выполнено')); 
            ?>
        </span>
        <a href="task-edit.php?id=<?php echo $task['id']; ?>" class="btn-icon" style="margin-left: 10px;">
            <i class="fas fa-edit"></i>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>







<?php if ($user_role == 'Прораб' || $user_role == 'Менеджер'): ?>
<h2 style="margin: 30px 0 15px;">
    <?php echo $user_role == 'Прораб' ? 'Мои задачи' : 'Активные задачи'; ?>
</h2>
<div class="tasks-list">
    <?php 
    if ($user_role == 'Прораб') {
        $display_tasks = array_filter($tasks, function($t) use ($user_id) {
            return ($t['assignee_id'] ?? 0) == $user_id && ($t['status'] ?? '') != 'done';
        });
    } else {
        $display_tasks = array_filter($tasks, function($t) {
            return ($t['status'] ?? '') != 'done';
        });
    }
    $display_tasks = array_slice($display_tasks, 0, 5);
    ?>
    
    <?php if (empty($display_tasks)): ?>
        <p class="no-data">Нет активных задач</p>
    <?php else: ?>
        <?php foreach ($display_tasks as $task): ?>
        <div class="task-item">
            <?php if ($user_role == 'Прораб'): ?>
                <a href="task-complete.php?id=<?php echo $task['id'] ?? 0; ?>" class="task-checkbox-link">
                    <div class="task-checkbox"></div>
                </a>
            <?php else: ?>
                <div class="task-checkbox"></div>
            <?php endif; ?>
            <div class="task-info">
                <div class="task-title"><?php echo htmlspecialchars($task['title'] ?? 'Без названия'); ?></div>
                <div class="task-meta">
                    <span class="task-project"><?php echo htmlspecialchars($task['project'] ?? 'Без проекта'); ?></span>
                    <?php if ($user_role == 'Менеджер'): ?>
                    <span class="task-user"><i class="fas fa-user"></i> <?php echo htmlspecialchars($task['assignee'] ?? 'Не назначен'); ?></span>
                    <?php endif; ?>
                    <span class="task-deadline"><i class="fas fa-calendar"></i> <?php echo date('d.m.Y', strtotime($task['deadline'] ?? 'now')); ?></span>
                </div>
            </div>
            <span class="status-badge status-<?php echo $task['status'] ?? 'todo'; ?>">
                <?php 
                $status = $task['status'] ?? 'todo';
                echo $status == 'todo' ? 'К выполнению' : 
                     ($status == 'inprogress' ? 'В работе' : 
                     ($status == 'review' ? 'На проверке' : 'Выполнено')); 
                ?>
            </span>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endif; ?>


<?php if ($user_role == 'Бухгалтер' && !empty($acts)): ?>
<h2 style="margin: 30px 0 15px;">Последние акты</h2>
<div class="finance-grid">
    <?php foreach (array_slice($acts, 0, 3) as $act): ?>
    <div class="act-card">
        <div class="act-header">
            <span class="act-number"><?php echo htmlspecialchars($act['number'] ?? 'АКТ-000'); ?></span>
            <span class="act-date"><?php echo htmlspecialchars($act['date'] ?? '00.00.0000'); ?></span>
        </div>
        <div class="act-project"><?php echo htmlspecialchars($act['project'] ?? 'Проект'); ?></div>
        <div class="act-sum"><?php echo number_format($act['sum'] ?? 0); ?> ₽</div>
        <div class="act-footer">
            <span class="act-status <?php echo $act['status'] ?? 'waiting'; ?>">
                <?php echo ($act['status'] ?? '') == 'paid' ? 'Оплачено' : 'Ожидание'; ?>
            </span>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>