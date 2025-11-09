<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Koneksi ke mysql
$mysqli = new mysqli("localhost", "root", "", "db_posts");

// Input
$input = json_decode(file_get_contents("php://input"), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

// Cek dari database
$query = "SELECT * FROM tb_users WHERE username = '$username' AND password = '$password'";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    echo json_encode([
        'success' => true,
        'message' => "Login berhasil!"
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => "Username atau password salah!"
    ]);
}

?>