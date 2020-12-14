<?php
declare(strict_types=1);

namespace Ps\CommissionTask\Main;


class TransactionHandler
{
    private array $currency_rates = ['EUR' => [
        ['pair' => 'USD', 'rate' => '1.1497'],
        ['pair' => 'JPY', 'rate' => '129.53']
    ],
        'USD' => [
            ['pair' => 'EUR', 'rate' => '1.1497'],
            ['pair' => 'JPY', 'rate' => '129.53']
        ]];  // currency rates

    private array $transaction_rules = [
        'cash_in' => ['fee_default' => '0.03', 'max_fee' => '5'],
        'cash_out' => [
            'natural' => ['fee_default' => '0.3', 'no_fee_week_threshold' => '1000', 'no_fee_transactions' => '3'],
            'legal' => ['fee_default' => '0.3', 'min_fee' => '0.5']
        ]];
    private $threshold_passed = false;
    private $counter = 0;
    private $transactions_total = 0;

    function __construct(array $rate_pairs = null, array $transaction_rules = null)
    {
        if (isset($rate_pairs)) {
            $this->currency_rates = $rate_pairs;
        }
        if (isset($transaction_rules)) {
            $this->transaction_rules = $transaction_rules;
        }
    }

    public
    function getTransactionFee(array $weekly_transactions)
    {

        foreach ($weekly_transactions as $users) {
            foreach ($users as $transactions) {
                $this->counter = 0;
                $this->transactions_total = 0;
                $this->threshold_passed = false;
//                print("<pre>".print_r($transactions,true)."</pre>");
                foreach ($transactions as  $key => $transaction) {
                    $t_action = $transaction['action'];

                    if ($t_action == 'cash_in' && (array_key_exists($t_action, $this->transaction_rules))) {
                        $this->getWeeklyCashIn($transaction);
                    }

                    if ($t_action == 'cash_out' && (array_key_exists($t_action, $this->transaction_rules))) {
                        $this->getWeeklyCashOut($transaction);
                    }
                }

            }
        }

    }


    public function exchange(float $amount, string $currency_from, string $currency_to = 'EUR'): float
    {

        if (array_key_exists($currency_to, $this->currency_rates)) {
            foreach ($this->currency_rates[$currency_to] as $currency_rate) {
                if ($currency_rate['pair'] == $currency_from) {
                    return round($amount / $currency_rate['rate'], 2);
                }
            }
        } else {
            foreach (array_values($this->currency_rates) as $key => $currency_pair) {
                $rate_from = $this->currency_rates[array_keys($this->currency_rates)[$key]];

                if (in_array($currency_to, array_column($rate_from, 'pair'))) {
                    foreach ($rate_from as $rate) {
                        if ($rate['pair'] == $currency_to) {
                            return round($amount * $rate['rate'], 2);
                        }
                    }
                }
            }

        }
    }

    private function getWeeklyCashIn(array $transaction)
    {

        $commision_terms = $this->transaction_rules['cash_in'];
        $fee_default_percent = floatval($commision_terms['fee_default'] / 100) ?? null; // @var % - default fee
        $max_fee = floatval($commision_terms['max_fee']) ?? null; //@var int - max fee per transaction in EUR

                $t_date = $transaction['date'];
                $u_id = $transaction['u_id'];
                $t_type = $transaction['type'];
                $t_action = $transaction['action'];
                $t_amount = $transaction['amount'];
                $t_currency = $transaction['currency'];
                $t_eur_eq = floatval($transaction['eur_equivalent']);
                $fee_eur = $t_eur_eq * $fee_default_percent;
                $fee_eur = ($fee_eur <= $max_fee) ? $fee_eur : $max_fee;

                $fee = ($t_currency == 'EUR') ? $fee_eur : $this->exchange($fee_eur, 'EUR', $t_currency);

                print("<pre>" . print_r($t_date.':  |  '.$u_id.'  |  '.$t_type.' |  '.$t_action.'  |  '.$t_amount .$t_currency.'   ---> Fee: '.number_format($fee , 2), true) . "</pre>");
    }

    private function getWeeklyCashOut(array $transaction)
    {
        $this->counter  ++;
        $commision_terms = $this->transaction_rules['cash_out'];

        // natural transaction params
        $n_fee_default_percent = floatval($commision_terms["natural"]['fee_default'] / 100); // @var % - default fee
        $n_no_fee_week_threshold = floatval($commision_terms["natural"]['no_fee_week_threshold']); //@var int - no fee amount threshold in EUR / week
        $n_no_fee_transactions = floatval($commision_terms["natural"]['no_fee_transactions']); //@var int - no fee for first N transactions / week

        // legal transaction params
        $l_fee_default_percent = floatval($commision_terms["legal"]['fee_default'] / 100); // @var % - default fee
        $l_min_fee = floatval($commision_terms["legal"]['min_fee']); //@var int - max fee per transaction in EUR

            $transactions_no_fee = $n_no_fee_week_threshold;
//        print("<pre>".print_r($transactions,true)."</pre>");


                $t_date = $transaction['date'];
                $u_id = $transaction['u_id'];
                $t_type = $transaction['type'];
                $t_action = $transaction['action'];
                $t_amount = $transaction['amount'];
                $t_currency = $transaction['currency'];
                $u_type = $transaction['type'];
                $t_eur_eq = floatval($transaction['eur_equivalent']);

                $this->transactions_total +=$t_eur_eq;
                $transactions_no_fee -= $t_eur_eq;

                if ($u_type == 'legal') {
                    $fee_eur = $t_eur_eq * $l_fee_default_percent;
                    $fee = ($t_currency == 'EUR') ? $fee_eur : $this->exchange($fee_eur, 'EUR', $t_currency);
                }

                if ($u_type == 'natural') {

                    if ($this->counter > $n_no_fee_transactions) {
                        $fee_eur = $t_eur_eq * $n_fee_default_percent;
                        $fee = ($t_currency == 'EUR') ? $fee_eur : $this->exchange($fee_eur, 'EUR', $t_currency);

                    } else {
                        //if not passed the threshold

                        if (!$this->threshold_passed) {

//                            var_dump($this->transactions_total <= $n_no_fee_week_threshold);
                            if ($this->transactions_total <= $n_no_fee_week_threshold) {
                                $fee = 0.00;
                            } else {
                                $this->threshold_passed = true;
                                $fee_eur = abs($transactions_no_fee) * $n_fee_default_percent;
                                $fee = ($t_currency == 'EUR') ? $fee_eur : $this->exchange($fee_eur, 'EUR', $t_currency);
                            }
                        } else {
                            //has passed threshold
                            $fee_eur = $t_eur_eq * $n_fee_default_percent;
                            $fee = ($t_currency == 'EUR') ? $fee_eur : $this->exchange($fee_eur, 'EUR', $t_currency);
                        }
                    }
                }

                print("<pre>" . print_r($t_date . ':  |  ' . $u_id . '  |  ' . $t_type . ' |  ' . $t_action . '  |  '.$t_eur_eq.'EUR   |   ' . $t_amount . $t_currency . '   ---> Fee: ' . number_format($fee, 2), true) . "</pre>");

    }

}