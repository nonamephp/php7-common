<?php declare(strict_types=1);
namespace Noname;

use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    /**
     * @covers Arr::flatten, Arr::dot
     */
    public function testFlatten()
    {
        $array = ['a' => 'b', 'c' => ['d' => 'e', 'f' => ['g' => [1, 2, 3 => []]]]];

        $flatArray = Arr::flatten($array);

        // Check for keys
        $this->assertArrayHasKey('a', $flatArray);
        $this->assertArrayHasKey('c.d', $flatArray);
        $this->assertArrayHasKey('c.f.g.0', $flatArray);
        $this->assertArrayHasKey('c.f.g.1', $flatArray);
        $this->assertArrayHasKey('c.f.g.3', $flatArray);

        // Check for values
        $this->assertEquals('b', $flatArray['a']);
        $this->assertEquals('e', $flatArray['c.d']);
        $this->assertEquals(1, $flatArray['c.f.g.0']);
        $this->assertEquals(2, $flatArray['c.f.g.1']);
        $this->assertEquals([], $flatArray['c.f.g.3']);
    }

    /**
     * @covers Arr::each
     */
    public function testEach()
    {
        $array = [1, 2, 3, 4, 5, [6, 7, 8, 9, 10]];

        $array2 = Arr::each($array, function ($value) {
            return $value * 2;
        });

        $this->assertEquals([2, 4, 6, 8, 10, [12, 14, 16, 18, 20]], $array2);
    }
}
