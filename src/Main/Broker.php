<?php
declare(strict_types=1);

namespace Ps\CommissionTask\Main;

class Broker
{
    public $weekly_transact = [];

    public function __construct(array $weekly)
    {
        // @set array  $weekly_transact
        $this->getWeeklyTransactions($weekly);
        // get transactions fees
        $this->getTransactionFee();
    }

    private function getWeeklyTransactions(array $weekly): void
    {
        $weekly_tr = [];
        foreach ($weekly as $user_transactions) {
            array_push($weekly_tr, $user_transactions);
        }
        $this->weekly_transact = $weekly_tr;
    }

    private function getTransactionFee():void
    {
        $t_handler = new TransactionHandler();

        $t_handler->getTransactionFee($this->weekly_transact);
    }

}
