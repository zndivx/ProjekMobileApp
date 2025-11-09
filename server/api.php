<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// koneksi ke mysql
$conn = new mysqli("localhost", "root", "", "db_posts");

if ($conn->connect_error) {
    echo json_encode(['ERROR' => $conn->connect_error]);
    exit();
}

$METHOD = $_SERVER['REQUEST_METHOD'];

switch ($METHOD) {
    case 'GET':
        $sql = $conn->query("SELECT * FROM tb_posts ORDER BY id DESC");
        echo json_encode($sql->fetch_all(MYSQLI_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $judul = $conn->real_escape_string($data->judul);
        $deskripsi = $conn->real_escape_string($data->deskripsi);
        $sql = $conn->query("INSERT INTO tb_posts (judul, deskripsi) VALUES ('$judul', '$deskripsi')");
        echo json_encode(["id" => $conn->insert_id, "judul" => $judul, "deskripsi" => $deskripsi]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $id = (int)$data->id;
        $judul = $conn->real_escape_string($data->judul);
        $deskripsi = $conn->real_escape_string($data->deskripsi);
        $conn->query("UPDATE tb_posts SET judul='$judul', deskripsi='$deskripsi' WHERE id=$id");
        echo json_encode(["message" => "updated"]);
        break;

    case 'DELETE':
        parse_str($_SERVER['QUERY_STRING'], $query);
        $id = (int)$query['id'];
        $conn->query("DELETE FROM tb_posts WHERE id=$id");
        echo json_encode(["message" => "deleted"]);
        break;
}

$conn->close();
?>