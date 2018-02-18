<?php

/**
 * Class UtilsTest
 */
class UtilsTest extends \PHPUnit\Framework\TestCase {

    public function testgetParamType(): void
    {
        $this->assertEquals('i',\exporter\utils\utils::getParamType((int)5));
        $this->assertEquals('s',\exporter\utils\utils::getParamType('i am a string'));
        $this->assertEquals('d',\exporter\utils\utils::getParamType((double)5.55));
    }

}