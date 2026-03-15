<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['user_role'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проект-Менеджер - <?php echo $page_title ?? ''; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="app-container">
     
        <aside class="sidebar">
            <div class="logo">
                <i class="fas fa-hard-hat"></i>
                <span>Проект-Менеджер</span>
            </div>
            
            <div class="user-info-side">
                <i class="fas fa-user-circle"></i>
                <div>
                    <div class="user-name"><?php echo $_SESSION['user_name']; ?></div>
                    <div class="user-role"><?php echo $_SESSION['user_role']; ?></div>
                </div>
            </div>
            
           
            <div class="sidebar-search">
                <form action="search.php" method="GET">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="Быстрый поиск...">
                </form>
            </div>
            
            <nav class="nav-menu">
                <a href="dashboard.php" class="nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Дашборд</span>
                </a>
                
                <?php if ($user_role == 'Директор'): ?>
                    <a href="kpi.php" class="nav-item <?php echo $current_page == 'kpi.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>KPI</span>
                    </a>
                <?php endif; ?>
                
                <a href="projects.php" class="nav-item <?php echo $current_page == 'projects.php' ? 'active' : ''; ?>">
                    <i class="fas fa-folder"></i>
                    <span>Проекты</span>
                </a>
                
                <?php if ($user_role == 'Менеджер'): ?>
                    <a href="project-add.php" class="nav-item <?php echo $current_page == 'project-add.php' ? 'active' : ''; ?>">
                        <i class="fas fa-plus-circle"></i>
                        <span>+ Новый проект</span>
                    </a>
                <?php endif; ?>
                
                <a href="tasks.php" class="nav-item <?php echo $current_page == 'tasks.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tasks"></i>
                    <span>Задачи</span>
                    <?php 
                    $task_count = count(array_filter($tasks, function($t) { 
                        return $t['status'] == 'todo'; 
                    }));
                    if ($task_count > 0): 
                    ?>
                    <span class="badge"><?php echo $task_count; ?></span>
                    <?php endif; ?>
                </a>
                
                <?php if ($user_role == 'Менеджер' || $user_role == 'Директор'): ?>
                <a href="team.php" class="nav-item <?php echo $current_page == 'team.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Команда</span>
                </a>
                <?php endif; ?>
                
                <?php if ($user_role == 'Бухгалтер' || $user_role == 'Директор'): ?>
                <a href="finance.php" class="nav-item <?php echo $current_page == 'finance.php' ? 'active' : ''; ?>">
                    <i class="fas fa-file-invoice"></i>
                    <span>Финансы</span>
                </a>
                <?php endif; ?>
                
                <a href="profile.php" class="nav-item <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    <span>Профиль</span>
                </a>
                
                <a href="search.php" class="nav-item <?php echo $current_page == 'search.php' ? 'active' : ''; ?>">
                    <i class="fas fa-search"></i>
                    <span>Расширенный поиск</span>
                </a>
                
                <a href="logout.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Выйти</span>
                </a>
            </nav>
        </aside>

       
        <main class="main-content">
            <header class="header">
                <h1><?php echo $page_title ?? 'Проект-Менеджер'; ?></h1>
                <div class="header-actions">
                    <form action="search.php" method="GET" class="header-search">
                        <i class="fas fa-search"></i>
                        <input type="text" name="q" placeholder="Поиск...">
                    </form>
                    <span class="user-badge"><?php echo $_SESSION['user_role']; ?></span>
                </div>
            </header>

            <div class="content">