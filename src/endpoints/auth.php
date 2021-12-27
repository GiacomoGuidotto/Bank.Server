<?php

require '../../vendor/autoload.php';

use Services\Database\ServiceImpl;
use Specifications\ErrorCases\ExceedingMaxLength;
use Specifications\ErrorCases\ExceedingMinLength;
use Specifications\ErrorCases\IncorrectPattern;
use Specifications\ErrorCases\NotFound;
use Specifications\ErrorCases\NullAttributes;

$codesAssociations = [
    NullAttributes::CODE => 400,
    ExceedingMaxLength::CODE => 400,
    ExceedingMinLength::CODE => 400,
    IncorrectPattern::CODE => 400,
    NotFound::CODE => 404
];

$service = new ServiceImpl();

// ==== Available methods checks ===============================================
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    return;
}

// ==== get parameters =========================================================
$username = getallheaders()['username'];
$password = getallheaders()['password'];

// ==== Null check =============================================================
if ($username == null || $password == null) {
    http_response_code(400);
    echo json_encode($service->generateErrorMessage(NullAttributes::CODE));
    return;
}

// ==== Elaboration ============================================================
$result = $service->authenticate($username, $password);

// ==== Error case =============================================================
if ($result['error'] != null) {
    http_response_code($codesAssociations[$result['error']]);
    echo json_encode($result);
    return;
}

// ==== Success case ===========================================================
echo json_encode($result);