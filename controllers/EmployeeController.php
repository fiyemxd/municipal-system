<?php
require_once 'models/Request.php';

function showEmployeeRequests() {
    $requests = Request::getAssigned(); // tüm istekleri al
    include 'views/employee/request_list.php';
}

function updateRequestStatus() {
    $id = $_POST['id'];
    $status = $_POST['status'];
    Request::updateStatus($id, $status);
    echo "Status updated.";
}
