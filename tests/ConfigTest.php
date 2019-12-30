<?php

namespace Sbppgc\Worktime\Tests;

use PHPUnit\Framework\TestCase;
use Sbppgc\Worktime\Config;

class ConfigTest extends TestCase
{

    /**
     *
     */
    public function testCreateConfigWithValidFile()
    {
        try {
            $oConfig = new Config('tests/configs/test_config_ok_config.php');
            $this->assertInstanceOf('\Sbppgc\Worktime\Config', $oConfig);
        } catch (InvalidArgumentException $notExpected) {
            $this->fail();
        }
    }

    public function testCreateConfigWithInvalidFile()
    {
        $this->expectException('\Exception');
        $oConfig = new Config('tests/configs/test_config_bad_config.php');
    }

    public function testCreateConfigWithMissingFile()
    {
        $this->expectException('\Exception');
        $oConfig = new Config('tests/configs/test_config_no_file.php');
    }

    public function testGet()
    {
        $oConfig = new Config('tests/configs/test_config_ok_config.php');

        // Get exists text value
        $str = $oConfig->get('someText');
        $this->assertEquals('qwe', $str);

        // Get exists array value
        $aArr = $oConfig->get('someArray');
        $this->assertIsArray($aArr);

        // Get not exists value
        $notExistsVal = $oConfig->get('someNotExistsKey');
        $this->assertNull($notExistsVal);

        // Get not exists value with specified default value
        $notExistsVal = $oConfig->get('someNotExistsKey', 'asd');
        $this->assertEquals('asd', $notExistsVal);
    }

}
