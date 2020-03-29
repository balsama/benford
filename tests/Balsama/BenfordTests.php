<?php

namespace Balsama;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BenfordTests extends TestCase
{
    /* @var \Balsama\Benford */
    private $benford;

    public function setUp(): void {
        $this->benford = new Benford();
        parent::setUp();
    }

    public function testGetBenfordDeviationScoreFromSet() {
        $set = $this->getBigSet('../../data/nst-est2019-alldata.csv');
        $deviationScores[] = $this->benford->getBenfordDeviationScoreFromSet($set);

        $set = $this->getBigSet('../../data/us_state_emplchange_2015-2016.txt');
        $deviationScores[] = $this->benford->getBenfordDeviationScoreFromSet($set);

        foreach ($deviationScores as $deviationScore) {
            $this->assertLessThan(20, $deviationScore);
            $this->assertGreaterThan(5, $deviationScore);
        }
    }

    public function testGetBenfordDistrubution() {
        $set = [123, 1, 707, 2];
        $distribution = $this->benford::getBenfordDistrubution($set);

        $this->assertEquals(50, $distribution[1][1]);
        $this->assertEquals(25, $distribution[1][2]);
        $this->assertEquals(25, $distribution[1][7]);

        $this->assertEquals(50, $distribution[2][0]);
        $this->assertEquals(50, $distribution[2][2]);

        $this->assertEquals(50, $distribution[3][3]);
        $this->assertEquals(50, $distribution[3][7]);

        foreach ($distribution as $digitPercentages) {
            $this->assertCount(10, $digitPercentages);
            $this->assertEquals(100, array_sum($digitPercentages));

        }
    }

    public function testGetNthDigits() {
        $set = [123, 90, 5, 100];
        $nthDigits = $this->invokeMethod($this->benford, 'getNthDigits', [$set]);
        $this->assertCount(4, $nthDigits[1]);
        $this->assertCount(3, $nthDigits[2]);
        $this->assertCount(2, $nthDigits[3]);

        $set = [0, 12];
        $nthDigits = $this->invokeMethod($this->benford, 'getNthDigits', [$set]);
        $this->assertCount(1, $nthDigits[1]);
        $this->assertCount(1, $nthDigits[2]);
    }

    public function testGetBenfordPercentages() {
        $set = [1, 1, 1, 1, 1, 2, 3, 4, 5, 6];
        $percentages = $this->invokeMethod($this->benford, 'getBenfordPercentages', [$set]);
        $this->assertArrayHasKey(0, $percentages);
        $this->assertArrayHasKey(1, $percentages);
        $this->assertArrayHasKey(2, $percentages);
        $this->assertArrayHasKey(3, $percentages);
        $this->assertArrayHasKey(4, $percentages);
        $this->assertArrayHasKey(5, $percentages);
        $this->assertArrayHasKey(6, $percentages);
        $this->assertArrayHasKey(7, $percentages);
        $this->assertArrayHasKey(8, $percentages);
        $this->assertArrayHasKey(9, $percentages);
        $this->assertEquals(0, $percentages[0]);
        $this->assertEquals(50, $percentages[1]);
        $this->assertEquals(10, $percentages[2]);
        $this->assertEquals(10, $percentages[3]);
        $this->assertEquals(10, $percentages[4]);
        $this->assertEquals(10, $percentages[5]);
        $this->assertEquals(10, $percentages[6]);
        $this->assertEquals(0, $percentages[7]);
        $this->assertEquals(0, $percentages[8]);
        $this->assertEquals(0, $percentages[9]);
        $this->assertArrayNotHasKey(10, $percentages);

        $set = [0, 9];
        $percentages = $this->invokeMethod($this->benford, 'getBenfordPercentages', [$set]);
        $this->assertArrayHasKey(0, $percentages);
        $this->assertArrayHasKey(1, $percentages);
        $this->assertArrayHasKey(2, $percentages);
        $this->assertArrayHasKey(3, $percentages);
        $this->assertArrayHasKey(4, $percentages);
        $this->assertArrayHasKey(5, $percentages);
        $this->assertArrayHasKey(6, $percentages);
        $this->assertArrayHasKey(7, $percentages);
        $this->assertArrayHasKey(8, $percentages);
        $this->assertArrayHasKey(9, $percentages);
        $this->assertArrayNotHasKey(10, $percentages);

        $this->assertEquals(50, $percentages[0]);
        $this->assertEquals(50, $percentages[9]);
    }

    public function testGetNthBenfordPercentages() {
        $nths = [
            1 => [1, 1],
            2 => [0, 1, 8, 9],
            3 => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
        ];
        $nthsBenfordPercentages = $this->invokeMethod($this->benford, 'getNthBenfordPercentages', [$nths]);

        $this->assertEquals(0, $nthsBenfordPercentages[1][0]);
        $this->assertEquals(100, $nthsBenfordPercentages[1][1]);
        $this->assertEquals(25, $nthsBenfordPercentages[2][0]);
        $this->assertEquals(25, $nthsBenfordPercentages[2][1]);
    }

    /**
     * Invokes an object's private method.
     * @param $object
     *   The object to instantiate.
     * @param $methodName
     *   The methos to invoke.
     * @param array $parameters
     * @return mixed
     *   Param to pass to the method.
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = []) {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    private function getBigSet($file) {
        $csv = file_get_contents($file);
        $data = str_getcsv($csv);
        $set = [];
        foreach ($data as $datum) {
            if (is_numeric($datum)) {
                $datum = str_replace('.', '', $datum);
                if ($datum != 0) {
                    $set[] = abs($datum);
                }
            }
        }
        return $set;
    }

}