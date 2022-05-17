<?php

namespace Tests;

use Carbon\Carbon;
use O21\Support\FreeObject;
use PHPUnit\Framework\TestCase;

class FreeObjectTest extends TestCase
{
    private const TEST_PROPS = [
        'camelProp' => true,
        'date' => 1652814027,
        'snake_prop' => true
    ];

    protected FreeObject $obj;

    protected function setUp(): void
    {
        $this->obj = new class(self::TEST_PROPS) extends FreeObject {
            protected array $dates = ['date'];
        };
    }

    public function testCamelProp(): void
    {
        $this->assertTrue($this->obj->camelProp);
    }

    public function testDate(): void
    {
        $this->assertInstanceOf(Carbon::class, $this->obj->date);
    }

    public function testSnakeProp(): void
    {
        $this->assertTrue($this->obj->snakeProp);
    }
}
