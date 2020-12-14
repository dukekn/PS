<?php
declare(strict_types=1);

namespace Ps\CommissionTask\Factory;

use Ps\CommissionTask\Main\TransactionHandler;


abstract class TransactionFactory
{

    public static function getTransactions( string $transactions_file) : array
    {
        if(file_exists($transactions_file))
        {
            //  @return array  getWeeklyTransactions (IN csvToArray)
            $transactions_arr = self::csvToArray($transactions_file);

           return self::getWeeklyTransactions($transactions_arr);
        }
    }
    private  function csvToArray(string $file): array
    {
            $values   = array_map('str_getcsv', file($file));
            $keys =  ['t_id','date','u_id','type','action','amount','currency'];
            $result   = array();
            foreach($values as $value)
            {
                array_unshift($value , uniqid() );
                $result[] = array_combine($keys, $value);
            }
            return $result;
    }

    private function getWeeklyTransactions(array $arr): array
    {
        $weekly_array = [];
        foreach ($arr as $transaction)
        {
            $t_id = $transaction['t_id'];
            $u_id = $transaction['u_id'];
            $t_amount       = floatval($transaction['amount']);
            $t_currency    = $transaction['currency'];
            $t_date = $transaction['date'];
            $t_week = date('oW', strtotime($t_date));
            $t_year = date('Y', strtotime($t_date));

            if($t_currency != 'EUR')
            {
                $exchange = new TransactionHandler();
                $eur_equivalent = $exchange->exchange($t_amount , $t_currency);
                $transaction = $transaction +['eur_equivalent' => $eur_equivalent] ;
            }else{
                $transaction = $transaction +  ['eur_equivalent' => $t_amount] ;
            }

            // year exists?
            if (isset($weekly_array[$t_week]))
            {
                // user in week in year exists?
                if (isset($weekly_array[$t_week][$u_id]))
                {
                    // t_id  not exists in array?
                    if (!in_array("$t_id", array_column($weekly_array[$t_week][$u_id], 't_id'), true))
                    {
                        array_push($weekly_array[$t_week][$u_id], $transaction);
                    }
                } else {
                    // create user in week in year
                    $weekly_array[$t_week][$u_id] = [$transaction];
                }
            } else {
                // create user in week in year
                $weekly_array[$t_week][$u_id] = [$transaction];
            }

        }
        return $weekly_array;
    }


}

