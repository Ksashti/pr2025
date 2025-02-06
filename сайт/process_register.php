<?php
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

// Получение данных из формы (пользователь)
$username = $_POST['username'];
$password = $_POST['password'];
$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$email = $_POST['email'];
$phoneNumber = $_POST['phone_number'];

// Получение данных из формы (продукт)
$productName = $_POST['product_name'];
$companyName = $_POST['company_name'];
$purchaseDate = $_POST['purchase_date'];
$warrantyDuration = $_POST['warranty_duration'];

// Проверка, что логин не занят (пример)
$stmt = $pdo->prepare("SELECT user_id FROM Users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    die("Имя пользователя уже занято."); // В РЕАЛЬНОМ КОДЕ НУЖНО ВЫВОДИТЬ НОРМАЛЬНОЕ СООБЩЕНИЕ ОБ ОШИБКЕ НА СТРАНИЦЕ
}

// Хеширование пароля
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Вставка данных в таблицу Users
$sqlUsers = "INSERT INTO Users (username, password, first_name, last_name, email, phone_number, registration_date, role) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'user')";
$stmtUsers = $pdo->prepare($sqlUsers);

try {
    $stmtUsers->execute([$username, $hashedPassword, $firstName, $lastName, $email, $phoneNumber]);
    $userId = $pdo->lastInsertId(); // Получаем ID нового пользователя

    // Проверяем, введены ли данные о продукте
    if (!empty($productName) && !empty($companyName) && !empty($purchaseDate) && !empty($warrantyDuration)) {
        // Получаем или создаем компанию
        $stmtCompany = $pdo->prepare("SELECT company_id FROM Companies WHERE company_name = ?");
        $stmtCompany->execute([$companyName]);
        $company = $stmtCompany->fetch();

        if ($company) {
            $companyId = $company['company_id'];
        } else {
            // Если компании нет, то создаем её
            $stmtInsertCompany = $pdo->prepare("INSERT INTO Companies (company_name) VALUES (?)");
            $stmtInsertCompany->execute([$companyName]);
            $companyId = $pdo->lastInsertId();
        }

        // Вставляем данные в таблицу Products (здесь нужны фиктивные значения для product_group_id и price)
        $sqlProducts = "INSERT INTO Products (product_name, company_id, user_id, product_group_id, price) VALUES (?, ?, ?, 1, 100)"; // Фиктивные значения: product_group_id = 1, price = 100
        $stmtProducts = $pdo->prepare($sqlProducts);
        $stmtProducts->execute([$productName, $companyId, $userId]);
        $productId = $pdo->lastInsertId();

        // Вставляем данные в таблицу Warranty_History
        $warrantyEndDate = date('Y-m-d', strtotime($purchaseDate . ' + ' . $warrantyDuration . ' months'));
	 $sqlWarranty = "INSERT INTO Warranty_History (product_id, warranty_start_date, status, warranty_end_date) VALUES (?, ?, ?, ?)";
        $stmtWarranty = $pdo->prepare($sqlWarranty);
	$stmtWarranty->execute([$productId, $purchaseDate, 'active', $warrantyEndDate]);

    }

    // Успешная регистрация
    header("Location: login.php");
    exit();

} catch (PDOException $e) {
    die("Ошибка при регистрации: " . $e->getMessage());
}

?>