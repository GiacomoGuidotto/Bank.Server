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

// ==== Invalid methods checks =====================================================
$validMethods = ['GET', 'POST', 'DELETE'];
$method = $_SERVER['REQUEST_METHOD'];

if (!in_array($method, $validMethods)) {
    http_response_code(405);
    return;
}


// =================================================================================
// ==== GET case ===================================================================
if ($method == 'GET') {
    return;
}

// =================================================================================
// ==== POST case ===================================================================
if ($method == 'POST') {
    return;
}

// =================================================================================
// ==== DELETE case ===================================================================
if ($method == 'DELETE') {
    return;
}