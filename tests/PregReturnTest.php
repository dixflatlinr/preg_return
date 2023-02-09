<?php

declare(strict_types=1);

use DF\App\Helper\RX;
use PHPUnit\Framework\TestCase;

class PregReturnTest extends TestCase
{
    protected string $testString = 'first_second_third';
    protected string $basePattern = '~^(first)_(second)_(?<third>third)$~is';

    protected string $zeroResult = 'first_second_third';
    protected string $firstResult = 'first';
    protected string $secondResult = 'second';
    protected string $thirdResult = 'third';

    protected string $namedResultIndex = 'third';
    protected string $namedResult = 'third';

    protected array $matches = [];

    public function setUp(): void
    {
        $this->matches =
        [
            0   =>      $this->zeroResult,//will contain the text that matched the full pattern
            1   =>      $this->firstResult,
            2   =>      $this->secondResult,
            "third" =>  $this->namedResult,
            3   =>      $this->thirdResult,
        ];
    }

    public function test_Preg_Return_Indexes_Typecheck(): void
    {
        //Throws an error, since only array|int|string are allowed
        $this->expectException(\InvalidArgumentException::class);
        RX::pregReturn($this->basePattern, $this->testString, new StdClass);
    }

    /**
     * Compares two functions' parameters
     *
     * @param ReflectionFunctionAbstract $fa Function A
     * @param ReflectionFunctionAbstract $fb Function B
     * @return bool
     */
    private function functionIdentical(ReflectionFunctionAbstract $fa, ReflectionFunctionAbstract $fb)
    {
        $faParameters = $fa->getParameters();
        $fbParameters = $fb->getParameters();

        if ( count($faParameters) != count($fbParameters) )
            return false;

        foreach($faParameters as $key => $param)
        {
            if ( (string)$faParameters[$key] !== (string)$fbParameters[$key] )
                return false;
        }

        return true;
    }

    /**
     * All the function aliases must have the same parameters as their RX::method counterparts
     */
    public function test_Preg_Return_Global_functions_parameter_check(): void
    {
        $func =
        [
            ['pregReturn', 'preg_return'],
            ['pregReturnAll', 'preg_return_all'],
            ['pregReturnReplace', 'preg_return_replace'],
        ];

        foreach($func as $functionPair)
        {
            $r = $this->functionIdentical(
                new \ReflectionMethod('DF\App\Helper\RX',$functionPair[0]),
                new \ReflectionFunction($functionPair[1])
            );
            $this->assertTrue($r, "{$functionPair[0]} and {$functionPair[1]} function parameters are not identical!}");
        }
    }

    public function test_Preg_Return_Global_functions(): void
    {
        $res = preg_return($this->basePattern, $this->testString, []);
        $this->assertEqualsCanonicalizing($res, $this->matches);

        $res = preg_return_all('~b~', 'abba');
        $this->assertEqualsCanonicalizing(2, $res);

        $res = preg_return_all('~b~', 'abba',[]);
        $this->assertEqualsCanonicalizing([0 => ['b','b']], $res);

        $replacement = 'nyuff';
        $testString = $this->testString;
        $res = preg_return_replace($this->basePattern, $replacement, $testString, 3);
        $this->assertEquals($this->thirdResult, $res);
        $this->assertEquals($testString, $replacement);
   }

    public function test_Preg_Return_Indexes_Array(): void
    {
        //Should return the whole matches array when an empty array or string is specified
        $res = RX::pregReturn($this->basePattern, $this->testString, []);
        $this->assertEqualsCanonicalizing($res, $this->matches);

        //Default $indexesToReturn = null
        $res = RX::pregReturn($this->basePattern, $this->testString);
        $this->assertEqualsCanonicalizing(1, $res);


        //Checking results by indexname - array
        $res = RX::pregReturn($this->basePattern, $this->testString, ["{$this->namedResultIndex}"]);
        $this->assertEquals([$this->namedResultIndex => $this->namedResult], $res);

        //Checking results by index no 0,1,2,3 - array
        $res = RX::pregReturn($this->basePattern, $this->testString, [0]);
        $this->assertEquals([0 => $this->zeroResult], $res);

        $res = RX::pregReturn($this->basePattern, $this->testString, [1]);
        $this->assertEquals([1 => $this->firstResult], $res);

        $res = RX::pregReturn($this->basePattern, $this->testString, ['2']);
        $this->assertEquals([2 => $this->secondResult], $res);

        $res = RX::pregReturn($this->basePattern, $this->testString, [3]);
        $this->assertEquals([3 => $this->thirdResult], $res);

        //Checking results by index 1-3 - array
        $res = RX::pregReturn($this->basePattern, $this->testString, ['1','3']);
        $this->assertEquals(
            [
                1 => $this->firstResult,
                3 => $this->thirdResult
            ], $res);

        $res = RX::pregReturn($this->basePattern, $this->testString, [0,1,2,'third', 3]);
        $this->assertEquals($this->matches, $res);

        //Nonexistent indexes should return null
        $res = RX::pregReturn($this->basePattern, $this->testString, [9,'beka']);
        $this->assertEquals(
            [
                9 => null, 'beka' => null
            ], $res);
    }

    public function test_Preg_Return_Indexes_Integer(): void
    {
        //Should return the whole matches array when no index specified
        $res = RX::pregReturn($this->basePattern, $this->testString, '');
        $this->assertEqualsCanonicalizing($res, $this->matches);

        //Checking results by indexname
        $res = RX::pregReturn($this->basePattern, $this->testString, $this->namedResultIndex);
        $this->assertEquals($this->thirdResult, $res);

        //Checking results by index no 0,1,2,3
        $res = RX::pregReturn($this->basePattern, $this->testString, 0);
        $this->assertEquals($this->zeroResult, $res);

        $res = RX::pregReturn($this->basePattern, $this->testString, 3);
        $this->assertEquals($this->thirdResult, $res);

        //Nonexistent index should return null
        $res = RX::pregReturn($this->basePattern, $this->testString, 9);
        $this->assertNull($res);
    }

    public function test_Preg_Return_Replace_Simple(): void
    {
        $testString = $this->testString;
        $replacement = 'nyuff';
        $res = RX::pregReturnReplace($this->basePattern, $replacement, $testString, 3);
        $this->assertEquals($this->thirdResult, $res);
        $this->assertEquals($testString, $replacement);
    }

    public function test_Preg_Return_All_Simple(): void
    {
        $res = preg_return_all('~b~', 'abba', '');
        $this->assertEqualsCanonicalizing([0 => ['b','b']], $res);

        $res = preg_return_all('~b~', 'abba');
        $this->assertEqualsCanonicalizing(2, $res);

        //Offset check: abba => ba
        $res = preg_return_all('~b~', 'abba',[], 0, 2);
        $this->assertEqualsCanonicalizing([0 => ['b']], $res);
    }
}
