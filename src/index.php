<?php
require '../vendor/autoload.php';

use Ps\CommissionTask\Factory\TransactionFactory;
use Ps\CommissionTask\Main\Broker;

//get transactions array from file
$transactions = TransactionFactory::getTransactions('input'.DIRECTORY_SEPARATOR.'input.csv');

// get transaction fees via broker
$calculate_fee = new Broker($transactions);
