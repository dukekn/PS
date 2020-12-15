<?php
require '../vendor/autoload.php';

use Ps\CommissionTask\Factory\TransactionFactory;
use Ps\CommissionTask\Main\Broker;

$input_files = glob('input' . DIRECTORY_SEPARATOR .'*.csv');

foreach ($input_files as $key => $file)
{
    print($input_files[$key].'-->'. PHP_EOL);
    //get transactions array from file
    $transactions = TransactionFactory::getTransactions($file);

// get transaction fees via broker
    $calculate_fee = new Broker($transactions);
}

