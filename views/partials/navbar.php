<?php if (isset($_SESSION['role'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Municipal System</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if ($_SESSION['role'] === 'citizen'): ?>
            <li class="nav-item"><a class="nav-link" href="../citizen/dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="../citizen/submit_request.php">Submit Request</a></li>
        <?php elseif ($_SESSION['role'] === 'employee'): ?>
            <li class="nav-item"><a class="nav-link" href="../employee/pending_requests.php">Pending</a></li>
            <li class="nav-item"><a class="nav-link" href="../employee/in_progress_requests.php">In Progress</a></li>
            <li class="nav-item"><a class="nav-link" href="../employee/resolved_requests.php">Resolved</a></li>
        <?php elseif ($_SESSION['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="../admin/reports.php">Reports</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="../../controllers/AuthController.php?action=logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>
