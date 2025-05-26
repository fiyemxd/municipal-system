<?php
require_once 'models/Request.php';

function showReports() {
    $stats = Request::getStatistics();
    include 'views/admin/reports.php';
}
