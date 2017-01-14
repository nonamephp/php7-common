<?php declare(strict_types=1);
namespace Noname\Common;

class ArrTest extends \PHPUnit_Framework_TestCase
{
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
}
