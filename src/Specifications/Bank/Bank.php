<?php

namespace Specifications\Bank;

interface Bank
{
    // IBAN details:
    // Country code | Checks digits | National check digit | Bank code | Branch code | Bank account number
    // 2 chars      | 2 digits      | 1 char               | 5 digits  | 5 digits    | 12 digits

    const COUNTRY_CODE = 'IT';
    const BANK_CODE = '25836';
    const BRANCH_CODE = '37592';

    const MINIMUM_SAVING_AMOUNT = 0;
}