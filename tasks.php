<?php
session_start();
require_once 'data.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Задачи';
include 'header.php';

$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];


$storage = loadData();
$tasks = $storage['tasks'];


if ($user_role == 'Прораб') {
    $display_tasks = array_filter($tasks, function($t) use ($user_id) {
        return $t['assignee_id'] == $user_id;
    });
} elseif ($user_role == 'Менеджер' || $user_role == 'Директор') {
    $display_tasks = $tasks;
} else {
    $display_tasks = [];
}


usort($display_tasks, function($a, $b) {
    if ($a['status'] == 'done' && $b['status'] != 'done') return 1;
    if ($a['status'] != 'done' && $b['status'] == 'done') return -1;
    return strtotime($a['deadline']) - strtotime($b['deadline']);
});
?>
<div class="page-header">
    <h2>
        <?php 
        if ($user_role == 'Прораб') echo 'Мои задачи';
        elseif ($user_role == 'Менеджер') echo 'Все задачи';
        elseif ($user_role == 'Директор') echo 'Задачи сотрудников';
        else echo 'Задачи';
        ?>
    </h2>
    <?php if ($user_role == 'Менеджер'): ?>
    <div>
        <a href="task-add.php" class="btn-primary" style="margin-right: 10px;">
            <i class="fas fa-plus"></i> Новая задача
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="page-header">
    <h2>
        <?php 
        if ($user_role == 'Прораб') echo 'Мои задачи';
        elseif ($user_role == 'Менеджер') echo 'Все задачи';
        elseif ($user_role == 'Директор') echo 'Задачи сотрудников';
        else echo 'Задачи';
        ?>
    </h2>
</div>

<?php if (empty($display_tasks)): ?>
    <div class="empty-state">
        <i class="fas fa-tasks"></i>
        <p>Нет задач для отображения</p>
    </div>
<?php else: ?>
    <div class="tasks-filters">
        <button class="filter-btn active" onclick="filterTasks('all')">Все</button>
        <button class="filter-btn" onclick="filterTasks('todo')">К выполнению</button>
        <button class="filter-btn" onclick="filterTasks('inprogress')">В работе</button>
        <button class="filter-btn" onclick="filterTasks('review')">На проверке</button>
        <button class="filter-btn" onclick="filterTasks('done')">Выполнено</button>
    </div>

    <div class="tasks-list" id="tasksContainer">
        <?php foreach ($display_tasks as $task): ?>
        <div class="task-item" data-status="<?php echo $task['status']; ?>">
            
            <?php if ($user_role == 'Прораб'): ?>
              
                <a href="task-complete.php?id=<?php echo $task['id']; ?>" class="task-checkbox-link">
                    <div class="task-checkbox <?php echo $task['status'] == 'done' ? 'checked' : ''; ?>">
                        <?php if ($task['status'] == 'done'): ?>
                            <i class="fas fa-check"></i>
                        <?php endif; ?>
                    </div>
                </a>
            <?php else: ?>
              
                <div class="task-checkbox <?php echo $task['status'] == 'done' ? 'checked' : ''; ?>">
                    <?php if ($task['status'] == 'done'): ?>
                        <i class="fas fa-check"></i>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="task-info">
                <div class="task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                <div class="task-meta">
                    <span class="task-project"><?php echo htmlspecialchars($task['project']); ?></span>
                    <?php if ($user_role != 'Прораб'): ?>
                    <span class="task-user"><i class="fas fa-user"></i> <?php echo htmlspecialchars($task['assignee']); ?></span>
                    <?php endif; ?>
                    <span class="task-deadline"><i class="fas fa-calendar"></i> <?php echo date('d.m.Y', strtotime($task['deadline'])); ?></span>
                    <span class="priority-badge priority-<?php echo $task['priority']; ?>">
                        <?php 
                        echo $task['priority'] == 'high' ? 'Высокий' : 
                             ($task['priority'] == 'medium' ? 'Средний' : 'Низкий'); 
                        ?>
                    </span>
                </div>
            </div>
            
            <span class="status-badge status-<?php echo $task['status']; ?>">
                <?php 
                echo $task['status'] == 'todo' ? 'К выполнению' : 
                     ($task['status'] == 'inprogress' ? 'В работе' : 
                     ($task['status'] == 'review' ? 'На проверке' : 'Выполнено')); 
                ?>
            </span>
            <?php if ($user_role == 'Менеджер'): ?>
    <div style="display: flex; gap: 8px; margin-left: 15px;">
        <a href="task-edit.php?id=<?php echo $task['id']; ?>" class="btn-icon" title="Редактировать">
            <i class="fas fa-edit"></i>
        </a>
        <a href="task-delete.php?id=<?php echo $task['id']; ?>" class="btn-icon" title="Удалить" 
           onclick="return confirm('Вы уверены, что хотите удалить задачу?')">
            <i class="fas fa-trash"></i>
        </a>
    </div>
<?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function filterTasks(status) {
    const items = document.querySelectorAll('.task-item');
    const btns = document.querySelectorAll('.filter-btn');
    
    btns.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    items.forEach(item => {
        if (status == 'all' || item.dataset.status == status) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php include 'footer.php'; ?>