<?php
session_start();
require_once 'data.php';

echo "<h2>Проверка данных для team.php</h2>";

$storage = loadData();
echo "<h3>Содержимое storage:</h3>";
echo "<pre>";
print_r($storage);
echo "</pre>";

echo "<h3>Сотрудники (employees):</h3>";
if (isset($storage['employees'])) {
    echo "<pre>";
    print_r($storage['employees']);
    echo "</pre>";
} else {
    echo "Массив employees не найден!";
}

echo "<h3>Задачи (tasks):</h3>";
if (isset($storage['tasks'])) {
    echo "<pre>";
    print_r($storage['tasks']);
    echo "</pre>";
} else {
    echo "Массив tasks не найден!";
}
?>