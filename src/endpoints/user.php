<?php

require '../../vendor/autoload.php';

use Services\Database\ServiceImpl;
use Specifications\ErrorCases\ErrorCases;
use Specifications\ErrorCases\NullAttributes;

$service = new ServiceImpl();

// ==== Invalid methods checks =====================================================
$validMethods = ['POST', 'GET', 'DELETE'];
$method = $_SERVER['REQUEST_METHOD'];

if (!in_array($method, $validMethods)) {
    http_response_code(405);
    return;
}


// =================================================================================
// ==== POST case ===================================================================
if ($method == 'POST') {

    // ==== get parameters =========================================================
    $username = getallheaders()['username'];
    $password = getallheaders()['password'];
    $name = getallheaders()['name'];
    $surname = getallheaders()['surname'];

    // ==== Null check =============================================================
    if ($username == null || $password == null || $name == null || $surname == null) {
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[NullAttributes::CODE]);
        echo json_encode(
            $service->generateErrorMessage(NullAttributes::CODE)
        );
        return;
    }

    // ==== Elaboration ============================================================
    $result = $service->createUser($username, $password, $name, $surname);

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

    // ==== Null check =============================================================
    if ($token == null) {
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[NullAttributes::CODE]);
        echo json_encode(
            $service->generateErrorMessage(NullAttributes::CODE)
        );
        return;
    }

    // ==== Elaboration ============================================================
    $result = $service->getUser($token);

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
// ==== DELETE case ===================================================================
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
    $result = $service->closeUser($token);

    // ==== Error case =============================================================
    if ($result['error'] != null) {
        http_response_code(ErrorCases::CODES_ASSOCIATIONS[$result['error']]);
        echo json_encode($result);
        return;
    }

    // ==== Success case ===========================================================
    return;
}