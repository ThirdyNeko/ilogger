<?php
session_start();
require_once __DIR__ . '/../auth/require_login.php';
require_once __DIR__ . '/../config/db.php';

$pdo = qa_db();
$userRole = $_SESSION['role'] ?? 'user'; // Get logged-in role

// Get session from URL
$session = $_GET['session'] ?? null;
if (!$session) {
    echo "<div class='container mt-4'>
            <div class='alert alert-danger'>Session is required.</div>
          </div>";
    exit;
}

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div class="content">

    <div class="container-fluid mt-4">

        <h4 class="mb-3">Session: <?= htmlspecialchars($session) ?></h4>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">

                <table id="iterationTable" class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 15%;">Iteration</th>
                            <th style="width: 20%;">Errors</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->prepare("EXEC dbo.get_iteration_summary @session_id = :session");
                        $stmt->execute([':session' => $session]);
                        $iterations = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($iterations)):
                        ?>
                            <tr>
                                <td colspan="3" class="text-center">No data found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($iterations as $row): ?>
                                <tr class="clickable-row <?= (($row['error_count'] + $row['backend_errors']) > 0) ? 'table-danger' : '' ?>"
                                    data-iteration="<?= htmlspecialchars($row['iteration']) ?>">
                                    <td><strong><?= htmlspecialchars($row['iteration']) ?></strong></td>
                                    <td>
                                        <?php if (($row['error_count'] > 0) || ($row['backend_errors'] > 0)): ?>
                                            <span class="badge bg-danger">
                                                <?= ($row['error_count'] + $row['backend_errors']) ?> error(s)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">No errors</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['remarks'])): ?>
                                            <?= nl2br(htmlspecialchars($row['remarks'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>

</div>

<script src="<?= BASE_URL ?>assets/js/jquery-4.0.0.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    const roleViewerMap = {
        admin: '<?= BASE_URL ?>viewers/admin_viewer.php',
        qa: '<?= BASE_URL ?>viewers/qa_viewer.php',
        developer: '<?= BASE_URL ?>viewers/dev_viewer.php',
        user: '<?= BASE_URL ?>viewers/qa_viewer.php' // default fallback
    };

    $('#iterationTable tbody').on('click', 'tr.clickable-row', function() {
        const iteration = $(this).data('iteration');
        const session = "<?= htmlspecialchars($session) ?>";

        const viewerPage = roleViewerMap['<?= $userRole ?>'] || roleViewerMap['user'];

        window.location.href = `${viewerPage}?session=${encodeURIComponent(session)}&iteration=${encodeURIComponent(iteration)}`;
    });
});
</script>