<?php
// Подключение к базе данных
$host = 'localhost'; // Замените на Ваш хост
$db = 'shop'; // Замените на имя Вашей базы данных
$user = 'root'; // Замените на Ваше имя пользователя
$pass = ''; // Замените на Ваш пароль

$conn = new mysqli($host, $user, $pass, $db);

// Проверка соединения
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Ошибка подключения к базе данных: ' . $conn->connect_error]));
}

// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $conn->real_escape_string($data['product_id']);
$product_name = $conn->real_escape_string($data['product_name']);
$product_price = $conn->real_escape_string($data['product_price']);

// Проверка, существует ли товар в корзине
$sql = "SELECT * FROM cart WHERE product_id = '$product_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Если товар уже в корзине, увеличиваем количество
    $sql = "UPDATE cart SET quantity = quantity + 1 WHERE product_id = '$product_id'";
} else {
    // Если товара нет в корзине, добавляем его
    $sql = "INSERT INTO cart (product_id, product_name, product_price) VALUES ('$product_id', '$product_name', '$product_price')";
}

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Товар добавлен в корзину.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка: ' . $conn->error]);
}

$conn->close();
?>
