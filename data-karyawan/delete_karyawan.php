<?php
$data = json_decode(file_get_contents("../data-karyawan.json"), true);
$email = $_POST['email'];

if (!isset($data[$email])) {
    echo json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan!']);
    exit;
}

unset($data[$email]);
file_put_contents("../data-karyawan.json", json_encode($data));

echo json_encode(['status' => 'success']);
?>
