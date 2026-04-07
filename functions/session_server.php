<?php
include '../config/db.php';
$pdo = qa_db();

$program   = $_POST['program'] ?? null;
$branch    = $_POST['branch'] ?? null;
$user_id   = $_POST['user_id'] ?? null;
$client_ip = $_POST['client_ip'] ?? null;
$from_date = $_POST['from_date'] ?: null;
$to_date   = $_POST['to_date'] ?: null;
$from_time = $_POST['from_time'] ?: null;
$to_time   = $_POST['to_time'] ?: null;

// Call stored procedure
$stmt = $pdo->prepare("
    EXEC get_sessions
        @program = :program,
        @branch = :branch,
        @user_id = :user_id,
        @client_ip = :client_ip,
        @from_date = :from_date,
        @to_date = :to_date,
        @from_time = :from_time,
        @to_time = :to_time
");

$stmt->execute([
    ':program'   => $program,
    ':branch'    => $branch,
    ':user_id'   => $user_id,
    ':client_ip' => $client_ip,
    ':from_date' => $from_date,
    ':to_date'   => $to_date,
    ':from_time' => $from_time,
    ':to_time'   => $to_time,
]);

$data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Combine all errors into a single count
    $total_errors = ($row['errors'] ?? 0) + ($row['backend_errors'] ?? 0);

    $data[] = [
        $row['program_name'],
        $row['session_id'],
        $row['branch_id'],
        $row['user_id'],
        $row['client_ip'],

        // Single errors column
        $total_errors > 0 
            ? "<span class='text-danger fw-bold'>{$total_errors}</span>" 
            : "0",

        $row['last_updated'] 
            ? date('Y-m-d H:i:s', strtotime($row['last_updated'])) 
            : '-'
    ];
}

echo json_encode([
    "data" => $data
]);