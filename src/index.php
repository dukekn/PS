<?php
require '../vendor/autoload.php';

use Ps\CommissionTask\Factory\TransactionFactory;
use Ps\CommissionTask\Main\Broker;

$transactions = TransactionFactory::getTransactions('input'.DIRECTORY_SEPARATOR.'input.csv');

$calculate_fee = new Broker($transactions);

//print("<pre>".print_r($transactions,true)."</pre>");