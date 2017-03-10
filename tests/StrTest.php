<?php declare(strict_types=1);
namespace Noname\Common;

class StrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Str::testStartsWith
     */
    public function testStartsWith()
    {
        $caseSensitiveTrue  = Str::startsWith('Case-sensitive', 'C');
        $caseSensitiveFalse = Str::startsWith('Case-sensitive', 'c');
        $caseInsensitive    = Str::startsWith('case-insensitive', 'C', false);

        $this->assertTrue($caseSensitiveTrue);
        $this->assertFalse($caseSensitiveFalse);
        $this->assertTrue($caseInsensitive);
    }

    /**
     * @covers Str::testEndsWidth
     */
    public function testEndsWidth()
    {
        $caseSensitiveTrue  = Str::endsWith('sensitive-End', 'End');
        $caseSensitiveFalse = Str::endsWith('sensitive-End', 'end');
        $caseInsensitive    = Str::endsWith('insensitive-end', 'End', false);

        $this->assertTrue($caseSensitiveTrue);
        $this->assertFalse($caseSensitiveFalse);
        $this->assertTrue($caseInsensitive);
    }

    /**
     * @covers Str::equals
     */
    public function testEquals()
    {
        $caseSensitiveTrue  = Str::equals('case-sensitive', 'case-sensitive');
        $caseSensitiveFalse = Str::equals('case-sensitive', 'Case-sensitive');
        $caseInsensitive    = Str::equals('case-insensitive', 'Case-insensitive', false);

        $this->assertTrue($caseSensitiveTrue);
        $this->assertFalse($caseSensitiveFalse);
        $this->assertTrue($caseInsensitive);
    }
}