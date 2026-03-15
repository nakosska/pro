<?php
session_start();
require_once 'data.php';

$page_title = 'Поиск';
include 'header.php';

$query = $_GET['q'] ?? '';
$results = [];

if ($query) {
    $query_lower = strtolower($query);
    
   
    foreach ($projects as $project) {
        if (strpos(strtolower($project['name']), $query_lower) !== false ||
            strpos(strtolower($project['address']), $query_lower) !== false ||
            strpos(strtolower($project['client']), $query_lower) !== false) {
            $results[] = ['type' => 'project', 'data' => $project];
        }
    }
    
    
    foreach ($tasks as $task) {
        if (strpos(strtolower($task['title']), $query_lower) !== false ||
            strpos(strtolower($task['assignee']), $query_lower) !== false) {
            $results[] = ['type' => 'task', 'data' => $task];
        }
    }
    
    
    foreach ($employees as $emp) {
        if (strpos(strtolower($emp['name']), $query_lower) !== false ||
            strpos(strtolower($emp['role']), $query_lower) !== false) {
            $results[] = ['type' => 'employee', 'data' => $emp];
        }
    }
}
?>

<div class="search-container">
    <h2>Поиск</h2>
    
    <form method="GET" class="search-form">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" name="q" placeholder="Поиск по проектам, задачам, сотрудникам..." 
                   value="<?php echo htmlspecialchars($query); ?>" autofocus>
            <button type="submit" class="btn-primary">Найти</button>
        </div>
    </form>
    
    <?php if ($query): ?>
        <div class="search-results">
            <h3>Результаты поиска: <?php echo count($results); ?></h3>
            
            <?php if (empty($results)): ?>
                <p class="no-results">Ничего не найдено</p>
            <?php endif; ?>
            
            <?php foreach ($results as $result): ?>
                <?php if ($result['type'] == 'project'): ?>
                    <div class="result-item project-item">
                        <div class="result-icon"><i class="fas fa-folder"></i></div>
                        <div class="result-content">
                            <h4><?php echo htmlspecialchars($result['data']['name']); ?></h4>
                            <p><?php echo htmlspecialchars($result['data']['address']); ?></p>
                            <p>Клиент: <?php echo htmlspecialchars($result['data']['client']); ?></p>
                        </div>
                        <a href="projects.php" class="btn-small">Перейти</a>
                    </div>
                    
                <?php elseif ($result['type'] == 'task'): ?>
                    <div class="result-item task-item">
                        <div class="result-icon"><i class="fas fa-tasks"></i></div>
                        <div class="result-content">
                            <h4><?php echo htmlspecialchars($result['data']['title']); ?></h4>
                            <p>Проект: <?php echo htmlspecialchars($result['data']['project']); ?></p>
                            <p>Исполнитель: <?php echo htmlspecialchars($result['data']['assignee']); ?></p>
                        </div>
                        <a href="tasks.php" class="btn-small">Перейти</a>
                    </div>
                    
                <?php elseif ($result['type'] == 'employee'): ?>
                    <div class="result-item employee-item">
                        <div class="result-icon"><i class="fas fa-user"></i></div>
                        <div class="result-content">
                            <h4><?php echo htmlspecialchars($result['data']['name']); ?></h4>
                            <p>Должность: <?php echo htmlspecialchars($result['data']['role']); ?></p>
                            <p>Email: <?php echo htmlspecialchars($result['data']['email']); ?></p>
                        </div>
                        <a href="team.php" class="btn-small">Перейти</a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>