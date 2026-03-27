<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_menu':
        getMenu();
        break;
    case 'get_categories':
        getCategories();
        break;
    case 'place_order':
        placeOrder();
        break;
    case 'get_order':
        getOrder();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function getMenu() {
    $conn = getConnection();
    $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
    $category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

    $sql = "SELECT * FROM MenuItem WHERE 1=1";
    if ($search) $sql .= " AND (Name LIKE '%$search%' OR Category LIKE '%$search%')";
    if ($category) $sql .= " AND Category = '$category'";
    $sql .= " ORDER BY Category, Name";

    $result = $conn->query($sql);
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode($items);
    $conn->close();
}

function getCategories() {
    $conn = getConnection();
    $result = $conn->query("SELECT DISTINCT Category FROM MenuItem ORDER BY Category");
    $cats = [];
    while ($row = $result->fetch_assoc()) {
        $cats[] = $row['Category'];
    }
    echo json_encode($cats);
    $conn->close();
}

function placeOrder() {
    $conn = getConnection();
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo json_encode(['error' => 'Invalid data']);
        return;
    }

    $type = $conn->real_escape_string($data['type']);
    $items = $data['items'];
    $method = $conn->real_escape_string($data['payment_method']);
    $total = 0;

    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $conn->begin_transaction();
    try {
        // Insert order
        $conn->query("INSERT INTO Orders (Total, Type, OrderTime) VALUES ($total, '$type', NOW())");
        $orderID = $conn->insert_id;

        // Insert order items
        foreach ($items as $item) {
            $menuID = (int)$item['menu_id'];
            $qty = (int)$item['quantity'];
            $price = (float)$item['price'];
            $subtotal = $qty * $price;
            $conn->query("INSERT INTO OrderItem (OrderID, MenuID, Quantity, SubTotal) VALUES ($orderID, $menuID, $qty, $subtotal)");
        }

        // Insert payment
        $conn->query("INSERT INTO Payment (OrderID, Method, PaymentTime) VALUES ($orderID, '$method', NOW())");

        $conn->commit();
        echo json_encode(['success' => true, 'order_id' => $orderID, 'total' => $total]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    }
    $conn->close();
}

function getOrder() {
    $conn = getConnection();
    $orderID = (int)($_GET['order_id'] ?? 0);

    $order = $conn->query("SELECT * FROM Orders WHERE OrderID = $orderID")->fetch_assoc();
    if (!$order) {
        echo json_encode(['error' => 'Order not found']);
        return;
    }

    $items = [];
    $result = $conn->query("
        SELECT oi.*, m.Name, m.Category 
        FROM OrderItem oi 
        JOIN MenuItem m ON oi.MenuID = m.MenuID 
        WHERE oi.OrderID = $orderID
    ");
    while ($row = $result->fetch_assoc()) $items[] = $row;

    $payment = $conn->query("SELECT * FROM Payment WHERE OrderID = $orderID")->fetch_assoc();

    echo json_encode(['order' => $order, 'items' => $items, 'payment' => $payment]);
    $conn->close();
}
?>
