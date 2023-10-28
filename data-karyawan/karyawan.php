<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents("../data-karyawan.json"), true);
$output = ["data" => []];

foreach ($data as $email => $details) {
    $output["data"][] = [
        "email" => $email,
        "nama" => $details["nama"]
    ];
}

echo json_encode($output);
?>
