<?php
session_start();
require_once 'data.php';

if ($_SESSION['user_role'] != 'Директор') {
    header('Location: dashboard.php');
    exit;
}

$page_title = 'KPI';
include 'header.php';


$total_projects = count($projects);
$active_projects = count(array_filter($projects, function($p) { return $p['status'] == 'active'; }));
$total_budget = array_sum(array_column($projects, 'budget'));
$total_spent = array_sum(array_column($projects, 'spent'));
$total_tasks = count($tasks);
$completed_tasks = count(array_filter($tasks, function($t) { return $t['status'] == 'done'; }));
$task_completion = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100) : 0;


$total_employees = count($employees);
$prorabs_count = count(array_filter($employees, function($e) { return $e['role'] == 'Прораб'; }));


$total_acts = array_sum(array_column($acts, 'sum'));
$paid_acts = array_sum(array_column(array_filter($acts, function($a) { return $a['status'] == 'paid'; }), 'sum'));
?>

<div class="kpi-dashboard">
    <h2 class="kpi-title">Панель ключевых показателей</h2>
    
   
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?php echo $active_projects; ?></div>
                <div class="kpi-label">Активных проектов</div>
                <div class="kpi-trend positive">
                    <i class="fas fa-arrow-up"></i> +2 за месяц
                </div>
            </div>
        </div>
        
        <div class="kpi-card orange">
            <div class="kpi-icon">
                <i class="fas fa-ruble-sign"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?php echo number_format($total_budget/1000000, 1); ?> млн</div>
                <div class="kpi-label">Общий бюджет</div>
                <div class="kpi-trend positive">
                    <i class="fas fa-arrow-up"></i> +15%
                </div>
            </div>
        </div>
        
        <div class="kpi-card green">
            <div class="kpi-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?php echo $task_completion; ?>%</div>
                <div class="kpi-label">Выполнение задач</div>
                <div class="kpi-trend <?php echo $task_completion > 70 ? 'positive' : 'negative'; ?>">
                    <i class="fas fa-<?php echo $task_completion > 70 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                    <?php echo $task_completion > 70 ? 'Хорошо' : 'Нужно улучшить'; ?>
                </div>
            </div>
        </div>
        
        <div class="kpi-card purple">
            <div class="kpi-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?php echo $total_employees; ?></div>
                <div class="kpi-label">Сотрудников</div>
                <div class="kpi-trend positive">
                    <i class="fas fa-arrow-up"></i> +1 новый
                </div>
            </div>
        </div>
    </div>

    
    <div class="kpi-charts">
        <div class="chart-card">
            <h3>Распределение проектов</h3>
            <div class="pie-chart">
                <div class="pie-segment" style="--percentage: <?php echo ($active_projects/$total_projects)*100; ?>; --color: #10b981;"></div>
                <div class="pie-segment" style="--percentage: <?php echo (count(array_filter($projects, function($p) { return $p['status'] == 'pause'; }))/$total_projects)*100; ?>; --color: #f97316;"></div>
                <div class="pie-segment" style="--percentage: <?php echo (count(array_filter($projects, function($p) { return $p['status'] == 'planning'; }))/$total_projects)*100; ?>; --color: #6b7280;"></div>
            </div>
            <div class="chart-legend">
                <div><span class="legend-color" style="background: #10b981;"></span> Активные (<?php echo $active_projects; ?>)</div>
                <div><span class="legend-color" style="background: #f97316;"></span> На паузе (<?php echo count(array_filter($projects, function($p) { return $p['status'] == 'pause'; })); ?>)</div>
                <div><span class="legend-color" style="background: #6b7280;"></span> Планирование (<?php echo count(array_filter($projects, function($p) { return $p['status'] == 'planning'; })); ?>)</div>
            </div>
        </div>

        <div class="chart-card">
            <h3>Финансовые показатели</h3>
            <div class="progress-stats">
                <div class="stat-item">
                    <div class="stat-label">Освоено бюджета</div>
                    <div class="progress-bar large">
                        <div class="progress-fill" style="width: <?php echo ($total_spent/$total_budget)*100; ?>%"></div>
                    </div>
                    <div class="stat-numbers">
                        <span><?php echo number_format($total_spent/1000000, 1); ?> млн ₽</span>
                        <span>из <?php echo number_format($total_budget/1000000, 1); ?> млн ₽</span>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Оплачено по актам</div>
                    <div class="progress-bar large">
                        <div class="progress-fill" style="width: <?php echo ($paid_acts/$total_acts)*100; ?>%"></div>
                    </div>
                    <div class="stat-numbers">
                        <span><?php echo number_format($paid_acts/1000000, 1); ?> млн ₽</span>
                        <span>из <?php echo number_format($total_acts/1000000, 1); ?> млн ₽</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

  
    <div class="kpi-section">
        <h3>Эффективность сотрудников</h3>
        <div class="employee-efficiency">
            <table class="efficiency-table">
                <thead>
                    <tr>
                        <th>Сотрудник</th>
                        <th>Должность</th>
                        <th>Задач</th>
                        <th>Выполнено</th>
                        <th>Эффективность</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                        <?php 
                        $emp_tasks = array_filter($tasks, function($t) use ($emp) {
                            return $t['assignee'] == $emp['name'];
                        });
                        $total = count($emp_tasks);
                        $completed = count(array_filter($emp_tasks, function($t) {
                            return $t['status'] == 'done';
                        }));
                        $efficiency = $total > 0 ? round(($completed / $total) * 100) : 0;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($emp['name']); ?></td>
                            <td><?php echo $emp['role']; ?></td>
                            <td><?php echo $total; ?></td>
                            <td><?php echo $completed; ?></td>
                            <td>
                                <div class="efficiency-bar">
                                    <div class="efficiency-fill" style="width: <?php echo $efficiency; ?>%"></div>
                                    <span class="efficiency-value"><?php echo $efficiency; ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

  
    <div class="kpi-section">
        <h3>Ближайшие дедлайны</h3>
        <div class="deadlines-list">
            <?php 
            $upcoming_tasks = array_filter($tasks, function($t) {
                return $t['status'] != 'done' && strtotime($t['deadline']) > time();
            });
            usort($upcoming_tasks, function($a, $b) {
                return strtotime($a['deadline']) - strtotime($b['deadline']);
            });
            $upcoming_tasks = array_slice($upcoming_tasks, 0, 5);
            ?>
            
            <?php foreach ($upcoming_tasks as $task): ?>
            <div class="deadline-item">
                <div class="deadline-info">
                    <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                    <span><?php echo htmlspecialchars($task['project']); ?> • <?php echo $task['assignee']; ?></span>
                </div>
                <div class="deadline-date <?php echo (strtotime($task['deadline']) - time()) < 3*24*3600 ? 'urgent' : ''; ?>">
                    <i class="fas fa-calendar"></i> <?php echo date('d.m.Y', strtotime($task['deadline'])); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>

.pie-chart {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: conic-gradient(
        #10b981 0deg <?php echo ($active_projects/$total_projects)*360; ?>deg,
        #f97316 <?php echo ($active_projects/$total_projects)*360; ?>deg <?php echo (($active_projects + count(array_filter($projects, function($p) { return $p['status'] == 'pause'; })))/$total_projects)*360; ?>deg,
        #6b7280 <?php echo (($active_projects + count(array_filter($projects, function($p) { return $p['status'] == 'pause'; })))/$total_projects)*360; ?>deg 360deg
    );
    margin: 0 auto 20px;
}
</style>

<?php include 'footer.php'; ?>