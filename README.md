# Benford Calculator
Benford's Law states that, in many naturally occurring collections of numbers, the leading significant digit is likely
to be small. Specifically, the number 1 appears as the leading significant digit about 30% of the time, while 9 appears
as the leading significant digit less than 5% of the time. The same percentages can be extrapolated out to the second
and third digits. (After the third digit, all numbers have about the same likelihood of appearing.)

[Benford's law](https://en.wikipedia.org/wiki/Benford%27s_law)

## Usage
Include in your project:
```
$ composer require balsama/benford
```

Calculating the percentage that digits [0-9] appear in the first three places of a set of numbers.

```php
$set = [123, 1, 707, 2];
$distibution = Balsama\Benford::getBenfordDistrubution($set);
print_r($distibution);
//  [
//      [1] => [0, 50, 25, 0, 0, 0, 0, 25, 0, 0],
//      [2] => [50, 0, 50, 0, 0, 0, 0, 0, 0, 0],
//      [3] => [0, 0, 0, 50, 0, 0, 0, 50, 0, 0],
//  ]
```

Calculating the deviation from the Benford prediction.

```php
$set = [...]; // A large set of numbers spanning multiple orders of magnitude for best results.
$deviation = Balsama\Benford::getBenfordDeviationScoreFromSet($set);
print $deviation;
// A float. 20 is a good number (meaning likely to be a naturally occurring number set. Use your own data sets to
// determine what a good or bad score is for your purposes.
```
