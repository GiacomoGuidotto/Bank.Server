<?php

require '../../vendor/autoload.php';

use Services\Database\ServiceImpl;
use Specifications\ErrorCases\ErrorCases;
use Specifications\ErrorCases\NullAttributes;

$service = new ServiceImpl();

// ==== Invalid methods checks =====================================================
$validMethods = ['POST', 'GET', 'DELETE', 'PUT'];
$method = $_SERVER['REQUEST_METHOD'];

if (!in_array($method, $validMethods)) {
    http_response_code(405);
    return;
}


// =================================================================================
// ==== POST case ==================================================================
if ($method == 'POST') {

    // ==== get parameters =========================================================
    $token = getallheaders()['token'];
    $name = getallheaders()['name'];
    $type = getallheaders()['type'];
    $amount = getallheaders()['amount'];

    // ==== Null check =============================================================
    if ($token == null || $name == null || $type == null) {
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[NullAttributes::CODE]);
        echo json_encode(
            $service->generateErrorMessage(NullAttributes::CODE)
        );
        return;
    }

    // ==== Elaboration ============================================================
    $result = $service->createDeposit($token, $name, $type, $amount);

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

// =================================================================================
// ==== GET case ===================================================================
if ($method == 'GET') {

    // ==== get parameters =========================================================
    $token = getallheaders()['token'];
    $name = getallheaders()['name'];

    // ==== Null check =============================================================
    if ($token == null) {
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[NullAttributes::CODE]);
        echo json_encode(
            $service->generateErrorMessage(NullAttributes::CODE)
        );
        return;
    }

    // ==== Elaboration ============================================================
    $result = $service->getDeposits($token, $name);

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

// =================================================================================
// ==== DELETE case ================================================================
if ($method == 'DELETE') {

    // ==== get parameters =========================================================
    $token = getallheaders()['token'];
    $name = getallheaders()['name'];
    $destination = getallheaders()['destination'];

    // ==== Null check =============================================================
    if ($token == null || $name == null || $destination == null) {
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[NullAttributes::CODE]);
        echo json_encode(
            $service->generateErrorMessage(NullAttributes::CODE)
        );
        return;
    }

    // ==== Elaboration ============================================================
    $result = $service->closeDeposit($token, $name, $destination);

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

// =================================================================================
// ==== PUT case ===================================================================
if ($method == 'PUT') {

}
