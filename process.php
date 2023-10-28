<?php
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Email tidak valid.']);
        exit;
    }

    $karyawanData = json_decode(file_get_contents('data-karyawan.json'), true);

    // Cek apakah email ada di data karyawan
    if (!isset($karyawanData[$email])) {
        echo json_encode(['status' => 'error', 'message' => 'Kamu tidak terdaftar sebagai karyawan.']);
        exit;
    }

    $currentTime = new DateTime();
    $currentMonth = $currentTime->format('Y-M');
    $jsonFile = 'data-absensi-' . $currentMonth . '.json';
    $existingData = [];

    if (file_exists($jsonFile)) {
        $existingData = json_decode(file_get_contents($jsonFile), true);
    }

    $date = $currentTime->format('Y-m-d');
    $time = $currentTime->format('H:i');
    $currentHour = (int)$currentTime->format('H');

    if (!isset($existingData[$date])) {
        $existingData[$date] = [];
    }

    // Logika untuk absen masuk (01:00 - 03:00)
    if ($currentHour >= 1 && $currentHour <= 3) {
        if (isset($existingData[$date][$email])) {
            echo json_encode(['status' => 'error', 'message' => 'Anda sudah absen masuk hari ini.']);
            exit;
        }

        $existingData[$date][$email] = [
            'waktu_masuk' => $time,
            'waktu_pulang' => null,
            'status' => 'Tepat Waktu'
        ];

    } elseif ($currentHour >= 16 && $currentHour <= 21) {  // Logika untuk absen pulang (16:00 - 21:00)
        if (!isset($existingData[$date][$email]) || is_null($existingData[$date][$email]['waktu_masuk'])) {
            echo json_encode(['status' => 'error', 'message' => 'Anda harus absen masuk terlebih dahulu.']);
            exit;
        }

        $existingData[$date][$email]['waktu_pulang'] = $time;

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tidak dalam rentang waktu absen yang diizinkan.']);
        exit;
    }

    file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT));
    echo json_encode(['status' => 'success', 'message' => 'Absensi berhasil.']);
} else {
    include('scanner.html');
}

?>
