<?php

use Services\Database\ServiceImpl;

require '../../vendor/autoload.php';

$service = new ServiceImpl();

$testedArray = $service->getUser(
    getallheaders()['token']
);

echo json_encode($testedArray);