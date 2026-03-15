<?php
session_start();


if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}


$users = [
    ['id' => 1, 'name' => 'Иван Петров', 'email' => 'prorab@test.ru', 'password' => '123', 'role' => 'Прораб'],
    ['id' => 2, 'name' => 'Петр Сидоров', 'email' => 'prorab2@test.ru', 'password' => '123', 'role' => 'Прораб'],
    ['id' => 3, 'name' => 'Анна Смирнова', 'email' => 'manager@test.ru', 'password' => '123', 'role' => 'Менеджер'],
    ['id' => 4, 'name' => 'Алексей Морозов', 'email' => 'director@test.ru', 'password' => '123', 'role' => 'Директор'],
    ['id' => 5, 'name' => 'Елена Соколова', 'email' => 'buh@test.ru', 'password' => '123', 'role' => 'Бухгалтер'],
    ['id' => 6, 'name' => 'Андрей Иванов', 'email' => 'prorab3@test.ru', 'password' => '123', 'role' => 'Прораб'],
];

$error = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    foreach ($users as $user) {
        if ($user['email'] == $email && $user['password'] == $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: dashboard.php');
            exit;
        }
    }
    $error = 'Неверный email или пароль';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Проект-Менеджер</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <i class="fas fa-hard-hat"></i>
                <h2>Проект-Менеджер</h2>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="manager@test.ru" required>
                </div>
                
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" value="123" required>
                </div>
                
                <button type="submit" class="btn-primary btn-block">Войти</button>
            </form>
            
            <div class="login-info">
                <p><strong>Тестовые пользователи:</strong></p>
                <p> Менеджер: manager@test.ru / 123</p>
                <p> Прораб: prorab@test.ru / 123</p>
                <p> Директор: director@test.ru / 123</p>
                <p> Бухгалтер: buh@test.ru / 123</p>
            </div>
        </div>
    </div>
</body>
</html>