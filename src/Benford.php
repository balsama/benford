<?php

namespace Balsama;

class Benford
{

    /**
     * @var array
     *   The expected distribution of digits in a large data set.
     *
     * @see https://en.wikipedia.org/wiki/Benford%27s_law
     */
    const BENFORD_DISTRIBUTION = [
        1 => [0, 30.1, 17.6, 12.5, 9.7, 7.9, 6.7, 5.8, 5.1, 4.6],
        2 => [12, 11.4, 10.9, 10.4, 10, 9.7, 9.3, 9, 8.8, 8.5],
        3 => [10.2, 10.1, 10.1, 10.1, 10, 10, 9.9, 9.9, 9.9, 9.8]
    ];

    /**
     * @param int[] $set
     * @return array
     *   A multidimensional array keyed by digit position and digit containing the percentage that each of the first
     *   three digits in the provided set appears. E.g, for the set [123, 1, 707, 2]:
     *     [
     *       [1] => [0, 50, 25, 0, 0, 0, 0, 25, 0, 0],
     *       [2] => [50, 0, 50, 0, 0, 0, 0, 0, 0, 0],
     *       [3] => [0, 0, 0, 50, 0, 0, 0, 50, 0, 0],
     *     ]
     * @throws \Exception
     */
    public static function getBenfordDistrubution($set) {
        $nths = self::getNthDigits($set);
        $nthsPercentages = self::getNthBenfordPercentages($nths);
        return $nthsPercentages;
    }

    /**
     * Gets the sum of the abs difference between the percentage of apperance of a set's first, second, and third digits
     * and the expected Benford percentages.
     *
     * @param int[] $set
     *   An array of integers.
     * @throws \Exception
     * @return float $deviationScore
     */
    public static function getBenfordDeviationScoreFromSet($set) {
        $nths = self::getNthDigits($set);
        $nthsPercentages = self::getNthBenfordPercentages($nths);
        $deviationScore = self::getBenfordDeviationScore($nthsPercentages);
        return $deviationScore;
    }

    /**
     * @param array $percentages
     *   A ten-item long array of percentages for each digit.
     * @param int $position
     *   The position that the digits appeared in the original number set.
     *
     * @return int $benfordDeviationScore
     *   The sum of the difference between the expected Benford percentage for each digit.
     */
    protected static function getSingleBenfordDeviation($percentages, $position) {
        if (count((array) $percentages) < 1) {
            return;
        }
        $BenfordDeviationScore = 0;
        $i = 0;
        foreach ($percentages as $percentage) {
            $deviation = abs($percentage - self::BENFORD_DISTRIBUTION[$position][$i]);
            $BenfordDeviationScore = $BenfordDeviationScore + $deviation;
            $i++;
        }
        return $BenfordDeviationScore;
    }

    /**
     * @param int[] $digitSet
     *   An array of one-digit integers representing the first, second, or third digits of number in a set.
     * @return array
     */
    protected static function getBenfordPercentages($digitSet) {
        $setSize = count($digitSet);
        if ($setSize < 1) {
            return;
        }
        $counts = [];
        $percentages = [];
        for ($i = 1; $i <= 10; $i++) {
            $counts[] = 0;
            $percentages[] = 0;
        }

        foreach ($digitSet as $digit) {
            $counts[$digit]++;
        }

        $i = 0;
        foreach ($counts as $count) {
            $percentages[$i] = ($count / $setSize) * 100;
            $i++;
        }

        return $percentages;
    }

    /**
     * @param array $allPercentages
     * @throws \Exception
     * @return float
     */
    protected static function getBenfordDeviationScore($allPercentages) {
        if (count($allPercentages) != 3) {
            throw new \Exception('$allPercentages must be exactly three items long.');
        }
        $position = 1;
        $deviation = 0;
        foreach ($allPercentages as $percentages) {
            $deviation = $deviation + self::getSingleBenfordDeviation($percentages, $position);
            $position++;
        }

        return $deviation;
    }

    /**
     * @param int[] $set
     *   An array of integers.
     * @return mixed
     *   An array containing the first, second, and third digits that appear in the entire set. Given the following set:
     *   `[123, 90, 5]`
     *   The method would return:
     *   `[1, 9, 5], [2, 0], [3]`
     * @throws \Exception
     */
    protected static function getNthDigits(array $set) {
        $nths[1] = [];
        $nths[2] = [];
        $nths[3] = [];
        foreach ($set as $number) {
            $number = (int) $number;
            if (!is_int($number)) {
                throw new \Exception('All members of set must be integers');
            }
            $first = null;
            $second = null;
            $third = null;
            $first  = substr($number, 0, 1);
            if ($first != 0) {
                $nths[1][] = $first;
            }
            if (strlen($number) > 1) {
                $second = substr($number, 1, 1);
                $nths[2][] = $second;
            }
            if (strlen($number) > 2) {
                $third = substr($number, 2, 1);
                $nths[3][] = $third;
            }

        }
        return $nths;
    }

    /**
     * @param $nths
     *   An array of digits from a set of integers broken into three arrays by position.
     * @see self::getNthDigits()
     * @return array
     *   An array of ten-item long arrays that represent the percentages that each digit appeared in the first three
     *   positions.
     */
    protected static function getNthBenfordPercentages($nths) {
        $percentages = [];
        $i = 1;
        foreach ($nths as $nth) {
            $percentages[$i] = self::getBenfordPercentages($nths[$i]);
            $i++;
        }
        return $percentages;
    }
}