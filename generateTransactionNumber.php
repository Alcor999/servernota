<?php
header('Content-Type: application/json');

$host = "localhost"; // Server Database
$user = "root"; // Username Database
$password = ""; // Password Database
$database = "nota"; // Nama Database

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Mulai Transaksi
$conn->begin_transaction();

try {
    // Ambil nomor transaksi terakhir
    $query = "SELECT transaction_number FROM transaction_sequence ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastTransactionNumber = $row['transaction_number'];
    } else {
        throw new Exception("Failed to fetch the last transaction number");
    }

    // Tambahkan nomor transaksi baru
    $newTransactionNumber = $lastTransactionNumber + 1;
    $insertQuery = "INSERT INTO transaction_sequence (transaction_number) VALUES ($newTransactionNumber)";
    if (!$conn->query($insertQuery)) {
        throw new Exception("Failed to insert new transaction number");
    }

    // Commit Transaksi
    $conn->commit();
    echo json_encode(["status" => "success", "transactionNumber" => $newTransactionNumber]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>
