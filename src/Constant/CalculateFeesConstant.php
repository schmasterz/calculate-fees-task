<?php
declare(strict_types=1);
namespace App\Constant;

class CalculateFeesConstant {
    const CSV_DELIMITER = ',';
    const DEPOSIT_FEE = 0.0003;
    const WITHDRAW_PRIVATE_FEE = 0.003;
    const WITHDRAW_BUSINESS_FEE = 0.005;
    const BUSINESS_CLIENT = 'business';
    const PRIVATE_CLIENT = 'private';
    const OPERATION_TYPE_WITHDRAW = 'withdraw';
    const OPERATION_TYPE_DEPOSIT = 'deposit';
    const PRIVATE_WEEKLY_LIMIT = 1000.00;
    const PRIVATE_FREE_OPERATIONS = 3;

}