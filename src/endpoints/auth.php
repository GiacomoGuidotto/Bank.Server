<?php

require '../../vendor/autoload.php';

use Services\Cors\Cors;
use Services\Database\ServiceImpl;
use Specifications\ErrorCases\ErrorCases;
use Specifications\ErrorCases\NullAttributes;


$service = new ServiceImpl();

$method = $_SERVER['REQUEST_METHOD'];

// ==== Cors check =================================================================
Cors::handle('auth');

if ($method == 'OPTIONS') return;

// ==== Invalid methods checks =====================================================
$validMethods = ['GET'];

if (!in_array($method, $validMethods)) {
    http_response_code(405);
    return;
}

// =================================================================================
// ==== GET case ===================================================================
if ($method == 'GET') {

    // ==== get parameters =========================================================
    $username = getallheaders()['username'];
    $password = getallheaders()['password'];

    // ==== Null check =============================================================
    if ($username == null || $password == null) {
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[NullAttributes::CODE]);
        echo json_encode(
            $service->generateErrorMessage(NullAttributes::CODE)
        );
        return;
    }

    // ==== Elaboration ============================================================
    $result = $service->authenticate($username, $password);

    // ==== Error case =============================================================
    if ($result['error'] != null) {
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[$result['error']]);
        echo json_encode($result);
        return;
    }

    // ==== Success case ===========================================================
    echo json_encode($result);
    return;
}