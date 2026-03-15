<?php

$data_file = __DIR__ . '/project_data.json';


function loadData() {
    global $data_file;
    if (file_exists($data_file)) {
        $json = file_get_contents($data_file);
        $data = json_decode($json, true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [];
}


function saveData($data) {
    global $data_file;
    file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}


function initData() {
    $data = loadData();
    
    if (empty($data)) {
        $data = [
            'projects' => [
                [
                    'id' => 1,
                    'name' => 'ЖК "Восход"',
                    'address' => 'ул. Ленина, 15',
                    'client' => 'ООО "СтройИнвест"',
                    'status' => 'active',
                    'progress' => 75,
                    'budget' => 1500000,
                    'spent' => 1125000,
                    'deadline' => '2025-04-30',
                    'start_date' => '2025-01-15',
                    'tasks_total' => 12,
                    'tasks_completed' => 9,
                    'workers' => 3,
                    'manager' => 'Анна Смирнова',
                    'manager_id' => 3,
                    'description' => 'Строительство жилого комплекса'
                ],
                [
                    'id' => 2,
                    'name' => 'Коттедж "Берег"',
                    'address' => 'ул. Береговая, 7',
                    'client' => 'Иванов А.А.',
                    'status' => 'active',
                    'progress' => 30,
                    'budget' => 850000,
                    'spent' => 255000,
                    'deadline' => '2025-05-15',
                    'start_date' => '2025-02-01',
                    'tasks_total' => 8,
                    'tasks_completed' => 2,
                    'workers' => 2,
                    'manager' => 'Анна Смирнова',
                    'manager_id' => 3,
                    'description' => 'Частный коттедж'
                ],
                [
                    'id' => 3,
                    'name' => 'Ремонт офиса',
                    'address' => 'БЦ "Плаза", 3 этаж',
                    'client' => 'ООО "ТехноСервис"',
                    'status' => 'pause',
                    'progress' => 60,
                    'budget' => 2100000,
                    'spent' => 1260000,
                    'deadline' => '2025-05-10',
                    'start_date' => '2025-01-20',
                    'tasks_total' => 15,
                    'tasks_completed' => 9,
                    'workers' => 4,
                    'manager' => 'Анна Смирнова',
                    'manager_id' => 3,
                    'description' => 'Ремонт офиса'
                ]
            ],
            'tasks' => [
                ['id' => 1, 'title' => 'Сделать замеры', 'project' => 'ЖК "Восход"', 'project_id' => 1, 'assignee' => 'Иван Петров', 'assignee_id' => 1, 'deadline' => '2025-03-05', 'status' => 'todo', 'priority' => 'high'],
                ['id' => 2, 'title' => 'Завезти материалы', 'project' => 'Коттедж "Берег"', 'project_id' => 2, 'assignee' => 'Иван Петров', 'assignee_id' => 1, 'deadline' => '2025-03-03', 'status' => 'inprogress', 'priority' => 'high'],
                ['id' => 3, 'title' => 'Сдать акт', 'project' => 'Ремонт офиса', 'project_id' => 3, 'assignee' => 'Петр Сидоров', 'assignee_id' => 2, 'deadline' => '2025-03-07', 'status' => 'todo', 'priority' => 'medium'],
                ['id' => 4, 'title' => 'Проверить стяжку', 'project' => 'ЖК "Восход"', 'project_id' => 1, 'assignee' => 'Иван Петров', 'assignee_id' => 1, 'deadline' => '2025-03-04', 'status' => 'review', 'priority' => 'medium'],
                ['id' => 5, 'title' => 'Обновить чертежи', 'project' => 'Коттедж "Берег"', 'project_id' => 2, 'assignee' => 'Андрей Иванов', 'assignee_id' => 6, 'deadline' => '2025-03-06', 'status' => 'todo', 'priority' => 'low']
            ],
            'employees' => [
                ['id' => 1, 'name' => 'Иван Петров', 'role' => 'Прораб', 'phone' => '+7 (999) 123-45-67', 'email' => 'prorab@test.ru'],
                ['id' => 2, 'name' => 'Петр Сидоров', 'role' => 'Прораб', 'phone' => '+7 (999) 234-56-78', 'email' => 'prorab2@test.ru'],
                ['id' => 3, 'name' => 'Анна Смирнова', 'role' => 'Менеджер', 'phone' => '+7 (999) 567-89-01', 'email' => 'manager@test.ru'],
                ['id' => 4, 'name' => 'Алексей Морозов', 'role' => 'Директор', 'phone' => '+7 (999) 678-90-12', 'email' => 'director@test.ru'],
                ['id' => 5, 'name' => 'Елена Соколова', 'role' => 'Бухгалтер', 'phone' => '+7 (999) 789-01-23', 'email' => 'buh@test.ru'],
                ['id' => 6, 'name' => 'Андрей Иванов', 'role' => 'Прораб', 'phone' => '+7 (999) 890-12-34', 'email' => 'prorab3@test.ru']
            ],
            'acts' => [
                [
                    'id' => 1, 
                    'number' => 'АКТ-2025-001', 
                    'date' => '01.03.2025', 
                    'project' => 'ЖК "Восход"', 
                    'project_id' => 1, 
                    'sum' => 450000, 
                    'status' => 'paid',
                    'type' => 'Акт выполненных работ'
                ],
                [
                    'id' => 2, 
                    'number' => 'АКТ-2025-002', 
                    'date' => '28.02.2025', 
                    'project' => 'Коттедж "Берег"', 
                    'project_id' => 2, 
                    'sum' => 280000, 
                    'status' => 'waiting',
                    'type' => 'Акт приема-передачи'
                ],
                [
                    'id' => 3, 
                    'number' => 'АКТ-2025-003', 
                    'date' => '25.02.2025', 
                    'project' => 'Ремонт офиса', 
                    'project_id' => 3, 
                    'sum' => 730000, 
                    'status' => 'paid',
                    'type' => 'Акт выполненных работ'
                ]
            ],
            'last_id' => [
                'project' => 3,
                'task' => 5,
                'act' => 3
            ]
        ];
        saveData($data);
    }
    
    return $data;
}


function getNextId($type) {
    $data = loadData();
    if (!isset($data['last_id'][$type])) {
        $data['last_id'][$type] = 0;
    }
    $data['last_id'][$type]++;
    saveData($data);
    return $data['last_id'][$type];
}


$storage = initData();


$projects = &$storage['projects'];
$tasks = &$storage['tasks'];
$employees = &$storage['employees'];
$acts = &$storage['acts'];
?>