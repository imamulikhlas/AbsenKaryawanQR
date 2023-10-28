<?php
header('Content-Type: application/json');

date_default_timezone_set('Asia/Jakarta');

$data = json_decode(file_get_contents("php://input"), true);

function convertDateFormat($dateStr) {
    $dateParts = explode("-", $dateStr);
    if (count($dateParts) == 3) {
        return $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];
    } else {
        return false;
    }
}

if (isset($data['date'])) {
    $inputDate = convertDateFormat($data['date']);
    if (!$inputDate) {
        echo json_encode(["error" => "Invalid date format"]);
        exit;
    }

    $selectedMonth = date('Y-M', strtotime($inputDate));

    $jsonFile = '../data-absensi-' . $selectedMonth . '.json';

    if (file_exists($jsonFile)) {
        $monthData = json_decode(file_get_contents($jsonFile), true);
        $outputData = [];

        if (isset($monthData[$inputDate])) {
            foreach ($monthData[$inputDate] as $email => $data) {
                $singleData = array_merge(['email' => $email], $data);
                $singleData['tanggal'] = $inputDate; // Tambahkan tanggal masuk
                $outputData[] = $singleData;
            }
        }

        echo json_encode($outputData);
    } else {
        echo json_encode(["error" => "File not found"]);
    }
} else {
    echo json_encode(["error" => "Date parameter missing"]);
}
?>
