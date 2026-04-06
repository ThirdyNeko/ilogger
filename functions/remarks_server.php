<?php
session_start();
include '../config/db.php';
$pdo = qa_db();

// Get filters from POST
$user_id     = $_POST['user_id'] ?? null;
$username    = $_POST['username'] ?? null;
$program     = $_POST['program'] ?? null;
$remark_name = $_POST['remark_name'] ?? null;
$resolved    = $_POST['resolved'] ?? null;
$from_date   = $_POST['from_date'] ?? null;
$to_date     = $_POST['to_date'] ?? null;

// Include full day for to_date
if ($to_date) {
    $to_date .= ' 23:59:59';
}

// Convert resolved to boolean or NULL
if ($resolved !== null && $resolved !== '') {
    $resolved = (int)$resolved;
} else {
    $resolved = null;
}

// Call stored procedure
$stmt = $pdo->prepare("
    EXEC get_remarks
        @user_id     = :user_id,
        @username    = :username,
        @program     = :program,
        @remark_name = :remark_name,
        @resolved    = :resolved,
        @from_date   = :from_date,
        @to_date     = :to_date
");

$stmt->execute([
    ':user_id'     => $user_id ?: null,
    ':username'    => $username ?: null,
    ':program'     => $program ?: null,
    ':remark_name' => $remark_name ?: null,
    ':resolved'    => $resolved,
    ':from_date'   => $from_date ?: null,
    ':to_date'     => $to_date ?: null,
]);

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $isUnresolved = !$row['resolved']; // highlight if unresolved

    $data[] = [
        htmlspecialchars($row['user_id']),
        htmlspecialchars($row['username']),
        htmlspecialchars($row['session_id']),
        htmlspecialchars($row['iteration']),
        htmlspecialchars($row['remark_name']),
        "<span class='" . ($isUnresolved ? "text-danger fw-bold" : "") . "'>" . 
            htmlspecialchars($row['remark']) . 
        "</span>",
        $row['created_at'],
        $row['resolved'] ? 'Yes' : 'No',
        $row['resolved_at'] ?? '-',
        htmlspecialchars($row['resolved_by'] ?? '-'),
        htmlspecialchars($row['resolve_comment'] ?? '-')
    ];
}

echo json_encode([
    "data" => $data
]);