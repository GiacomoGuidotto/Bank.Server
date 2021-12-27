<?php

require '../../vendor/autoload.php';

use Services\Database\ServiceImpl;
use Specifications\ErrorCases\ExceedingMaxLength;
use Specifications\ErrorCases\ExceedingMinLength;
use Specifications\ErrorCases\NullAttributes;
use Specifications\ErrorCases\Unauthorized;

$codesAssociations = [
    NullAttributes::CODE => 400,
    ExceedingMaxLength::CODE => 400,
    ExceedingMinLength::CODE => 400,
    Unauthorized::CODE => 401,
];

$service = new ServiceImpl();

// ==== Invalid methods checks =====================================================
$validMethods = ['DELETE'];
$method = $_SERVER['REQUEST_METHOD'];

if (!in_array($method, $validMethods)) {
    http_response_code(405);
    return;
}

// =================================================================================
// ==== GET case ===================================================================
if ($method == 'DELETE') {

    // ==== get parameters =========================================================
    $token = getallheaders()['token'];

    // ==== Null check =============================================================
    if ($token == null) {
        http_response_code($codesAssociations[NullAttributes::CODE]);
        echo json_encode(
            $service->generateErrorMessage(NullAttributes::CODE)
        );
        return;
    }

    // ==== Elaboration ============================================================
    $result = $service->closeSession($token);

    // ==== Error case =============================================================
    if ($result['error'] != null) {
        http_response_code($codesAssociations[$result['error']]);
        echo json_encode($result);
        return;
    }

    // ==== Success case ===========================================================
    return;
}
