<?php

namespace Sbppgc\Worktime\Tests;

use PHPUnit\Framework\TestCase;
use Sbppgc\Worktime\Factory;

class FactoryTest extends TestCase
{

    protected $oFactory = null;

    public function setUp(): void
    {
        $this->oFactory = new Factory('tests/configs/test_factory_config.php');
    }

    public function testGetSingletonConfig()
    {
        $oItem = $this->oFactory->getSingletonConfig();
        $this->assertInstanceOf('\Sbppgc\Worktime\Config', $oItem);
    }

    public function testGetRouter()
    {
        $oItem = $this->oFactory->getRouter();
        $this->assertInstanceOf('\Sbppgc\Worktime\Router', $oItem);
    }

    public function testGetUserDaySheduleModel()
    {
        $oItem = $this->oFactory->getUserDaySheduleModel();
        $this->assertInstanceOf('\Sbppgc\Worktime\Models\UserDaySheduleInterface', $oItem);
    }

    public function testGetUserVacationsModel()
    {
        $oItem = $this->oFactory->getUserVacationsModel();
        $this->assertInstanceOf('\Sbppgc\Worktime\Models\UserVacationsInterface', $oItem);
    }

    public function testGetRestDaysModel()
    {
        $oItem = $this->oFactory->getRestDaysModel();
        $this->assertInstanceOf('\Sbppgc\Worktime\Models\RestDaysInterface', $oItem);
    }

    public function testGetRestDaysSourceModel()
    {
        $oItem = $this->oFactory->getRestDaysSourceModel();
        $this->assertInstanceOf('\Sbppgc\Worktime\Models\RestDaysSourceInterface', $oItem);
    }

    public function testGetCorpRestEventsModel()
    {
        $oItem = $this->oFactory->getCorpRestEventsModel();
        $this->assertInstanceOf('\Sbppgc\Worktime\Models\CorpRestEventsInterface', $oItem);
    }

    public function testGetCurlHelper()
    {
        $oItem = $this->oFactory->getCurlHelper();
        $this->assertInstanceOf('\Curl\Curl', $oItem);
    }

}
