# Ps Commission task skeleton

Example execution:
http://localhost/src/index.php

Input file is located in: 
\\src\input\input.csv

Outpts in the browser


Example Input from file:
2014-12-31,4,natural,cash_out,1200.00,EUR

2015-01-01,4,natural,cash_out,1000.00,EUR

2016-01-05,4,natural,cash_out,1000.00,EUR

2016-01-05,1,natural,cash_in,200.00,EUR

2016-01-06,2,legal,cash_out,300.00,EUR

2016-01-06,1,natural,cash_out,30000,JPY

2016-01-07,1,natural,cash_out,1000.00,EUR

2016-01-07,1,natural,cash_out,100.00,USD

2016-01-10,1,natural,cash_out,100.00,EUR

2016-01-10,2,legal,cash_in,1000000.00,EUR

2016-01-10,3,natural,cash_out,1000.00,EUR

2016-02-15,1,natural,cash_out,300.00,EUR

2016-02-19,5,natural,cash_out,3000000,JPY



Expected Output:

2014-12-31:  |  4  |  natural |  cash_out  |  1200EUR   |   1200.00EUR   ---> Fee: 0.60

2015-01-01:  |  4  |  natural |  cash_out  |  1000EUR   |   1000.00EUR   ---> Fee: 3.00

2016-01-05:  |  4  |  natural |  cash_out  |  1000EUR   |   1000.00EUR   ---> Fee: 0.00

2016-01-05:  |  1  |  natural |  cash_in  |  200.00EUR   ---> Fee: 0.06

2016-01-06:  |  1  |  natural |  cash_out  |  231.61EUR   |   30000JPY   ---> Fee: 0.00

2016-01-07:  |  1  |  natural |  cash_out  |  1000EUR   |   1000.00EUR   ---> Fee: 0.00

2016-01-07:  |  1  |  natural |  cash_out  |  86.98EUR   |   100.00USD   ---> Fee: 0.30

2016-01-10:  |  1  |  natural |  cash_out  |  100EUR   |   100.00EUR   ---> Fee: 0.30

2016-01-06:  |  2  |  legal |  cash_out  |  300EUR   |   300.00EUR   ---> Fee: 0.90

2016-01-10:  |  2  |  legal |  cash_in  |  1000000.00EUR   ---> Fee: 5.00

2016-01-10:  |  3  |  natural |  cash_out  |  1000EUR   |   1000.00EUR   ---> Fee: 0.00

2016-02-15:  |  1  |  natural |  cash_out  |  300EUR   |   300.00EUR   ---> Fee: 0.00

2016-02-19:  |  5  |  natural |  cash_out  |  23160.66EUR   |   3000000JPY   ---> Fee: 8,611.41
