<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);

include 'config/db.php';
include 'auth/require_login.php';
include 'partials/header.php';
include 'partials/sidebar.php';

$pdo = qa_db();
?>

<div class="content">

    <style>
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .clickable-row:hover {
        background-color: #f1f1f1;
    }
    #logsTable th,
    #logsTable td {
        text-align: center;
        vertical-align: middle;
    }
    </style>

    <div class="container-fluid">

        <!-- HEADER -->
        <div class="row mb-3">
            <div class="col d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0">Logs</h4>

                <button class="btn btn-outline-primary" id="refreshLogs">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row g-2">

                    <div class="col-md-2">
                        <label class="form-label">Program</label>
                        <input type="text" id="filterProgram" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Branch</label>
                        <input type="text" id="filterBranch" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">User ID</label>
                        <input type="text" id="filterUserId" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Client IP</label>
                        <input type="text" id="filterClientIP" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">From</label>
                        <input type="date" id="filterFrom" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">To</label>
                        <input type="date" id="filterTo" class="form-control">
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">

                    <table id="logsTable" class="table table-striped table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Program</th>
                                <th>Session</th>
                                <th>Branch</th>
                                <th>User ID</th>
                                <th>Client IP</th>
                                <th>Errors</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="assets/js/jquery-4.0.0.min.js"></script>
<script src="assets/js/datatables.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {

    const table = $('#logsTable').DataTable({
        processing: true,
        serverSide: false, // ✅ FIXED
        ajax: {
            url: 'functions/session_server.php',
            type: 'POST',
            data: function (d) {
                d.program   = $('#filterProgram').val();
                d.branch    = $('#filterBranch').val();
                d.user_id   = $('#filterUserId').val();
                d.client_ip = $('#filterClientIP').val();
                d.from_date = $('#filterFrom').val();
                d.to_date   = $('#filterTo').val();
            }
        },
        pageLength: 25,
        searching: false,
        ordering: false
    });

    // 🔄 Refresh button
    $('#refreshLogs').on('click', function () {
        table.ajax.reload();
    });

    // 🔍 Auto reload on filter change
    $('#filterProgram, #filterBranch, #filterUserId, #filterClientIP, #filterFrom, #filterTo')
        .on('change keyup', function () {
            table.ajax.reload();
        });

    const roleViewerMap = {
        admin: 'viewers/admin_viewer.php',
        qa: 'viewers/qa_viewer.php',
        developer: 'viewers/dev_viewer.php'
    };

    $('#logsTable tbody').on('click', 'tr', function () {
        const data = table.row(this).data();
        if (!data) return;

        const program = data[0];
        const session = data[1];

        const viewerPage = roleViewerMap[userRole];

        window.location.href =
            `${viewerPage}?program=${encodeURIComponent(program)}&session=${encodeURIComponent(session)}`;
    });

});
</script>

</body>
</html>