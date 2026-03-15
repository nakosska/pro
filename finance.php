<?php
session_start();
require_once 'data.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


if ($_SESSION['user_role'] != 'Бухгалтер' && $_SESSION['user_role'] != 'Директор') {
    header('Location: dashboard.php');
    exit;
}

$page_title = 'Финансы';
include 'header.php';


$storage = loadData();
$projects = $storage['projects'] ?? [];
$acts = $storage['acts'] ?? [];

$total_budget = array_sum(array_column($projects, 'budget'));
$total_spent = array_sum(array_column($projects, 'spent'));
$acts_sum = array_sum(array_column($acts, 'sum'));
$paid_sum = array_sum(array_column(array_filter($acts, function($a) { return ($a['status'] ?? '') == 'paid'; }), 'sum'));
?>

<div class="page-header">
    <h2>Финансы</h2>
    <button class="btn-primary" onclick="alert('Экспорт данных будет доступен позже')">
        <i class="fas fa-file-excel"></i> Экспорт
    </button>
</div>


<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-ruble-sign stat-icon green"></i>
        <div>
            <div class="stat-value"><?php echo number_format($total_budget/1000000, 1); ?> млн</div>
            <div class="stat-label">Общий бюджет</div>
        </div>
    </div>
    
    <div class="stat-card">
        <i class="fas fa-check-circle stat-icon blue"></i>
        <div>
            <div class="stat-value"><?php echo number_format($paid_sum/1000000, 1); ?> млн</div>
            <div class="stat-label">Оплачено</div>
        </div>
    </div>
    
    <div class="stat-card">
        <i class="fas fa-clock stat-icon orange"></i>
        <div>
            <div class="stat-value"><?php echo number_format(($acts_sum - $paid_sum)/1000000, 1); ?> млн</div>
            <div class="stat-label">Ожидает оплаты</div>
        </div>
    </div>
</div>


<h2 style="margin: 30px 0 15px;">Акты выполненных работ</h2>

<?php if (empty($acts)): ?>
    <div class="empty-state">
        <i class="fas fa-file-invoice"></i>
        <p>Нет актов для отображения</p>
    </div>
<?php else: ?>
    <div class="finance-grid">
        <?php foreach ($acts as $act): ?>
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
                <div>
                    <?php if (($_SESSION['user_role'] == 'Бухгалтер') && ($act['status'] ?? '') == 'waiting'): ?>
                    <a href="act-pay.php?id=<?php echo $act['id'] ?? 0; ?>" class="btn-icon" title="Отметить оплату">
                        <i class="fas fa-check-circle"></i>
                    </a>
                    <?php endif; ?>
                    <button class="btn-icon" onclick="alert('Скачивание акта')" title="Скачать">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<h2 style="margin: 30px 0 15px;">Бюджеты проектов</h2>

<?php if (empty($projects)): ?>
    <div class="empty-state">
        <i class="fas fa-folder"></i>
        <p>Нет проектов для отображения</p>
    </div>
<?php else: ?>
    <div class="budgets-table">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Проект</th>
                    <th>Бюджет</th>
                    <th>Потрачено</th>
                    <th>Остаток</th>
                    <th>Прогресс</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?php echo htmlspecialchars($project['name'] ?? 'Без названия'); ?></td>
                    <td class="amount"><?php echo number_format($project['budget'] ?? 0); ?> ₽</td>
                    <td class="amount"><?php echo number_format($project['spent'] ?? 0); ?> ₽</td>
                    <td class="amount <?php echo (($project['budget'] ?? 0) - ($project['spent'] ?? 0)) > 0 ? 'positive' : 'negative'; ?>">
                        <?php echo number_format(($project['budget'] ?? 0) - ($project['spent'] ?? 0)); ?> ₽
                    </td>
                    <td>
                        <div class="progress-bar small">
                            <div class="progress-fill" style="width: <?php echo $project['progress'] ?? 0; ?>%"></div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>


<?php include 'footer.php'; ?>