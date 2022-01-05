<?php

require '../../vendor/autoload.php';

use Services\Cors\Cors;
use Services\Database\ServiceImpl;
use Specifications\ErrorCases\ErrorCases;
use Specifications\ErrorCases\NullAttributes;


$service = new ServiceImpl();

$method = $_SERVER['REQUEST_METHOD'];

// ==== Cors check =================================================================
Cors::handle('exit');

if ($method == 'OPTIONS') return;

// ==== Invalid methods checks =====================================================
$validMethods = ['DELETE'];

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
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[NullAttributes::CODE]);
        echo json_encode(
            $service->generateErrorMessage(NullAttributes::CODE)
        );
        return;
    }

    // ==== Elaboration ============================================================
    $result = $service->closeSession($token);

    // ==== Error case =============================================================
    if ($result['error'] != null) {
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[$result['error']]);
        echo json_encode($result);
        return;
    }

    // ==== Success case ===========================================================
    return;
}
