<?php
session_start(); // Запуск сессии

// Подключение к базе данных (замените на свои данные)
$host = 'localhost';
$db = 'your_database_name';
$user = 'your_username';
$pass = 'your_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}


// Получение данных из формы
$username = $_POST['username'];
$password = $_POST['password'];

// Поиск пользователя в базе данных
$stmt = $pdo->prepare("SELECT user_id, username, password FROM Users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user) {
    // Проверка пароля
    if (password_verify($password, $user['password'])) {
        // Успешный вход
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        header("Location: index.php"); // Перенаправление на главную страницу (или любую другую)
        exit();
    } else {
        // Неверный пароль
        die("Неверный пароль."); // В РЕАЛЬНОМ КОДЕ НУЖНО ВЫВОДИТЬ НОРМАЛЬНОЕ СООБЩЕНИЕ ОБ ОШИБКЕ НА СТРАНИЦЕ
    }
} else {
    // Пользователь не найден
    die("Пользователь не найден."); // В РЕАЛЬНОМ КОДЕ НУЖНО ВЫВОДИТЬ НОРМАЛЬНОЕ СООБЩЕНИЕ ОБ ОШИБКЕ НА СТРАНИЦЕ
}
?>