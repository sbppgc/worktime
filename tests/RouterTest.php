<?php

namespace Sbppgc\Worktime\Tests;

use PHPUnit\Framework\TestCase;
use Sbppgc\Worktime\Factory;

class RouterTest extends TestCase
{

    protected $oRouter = null;

    public function setUp(): void
    {
        $oFactory = new Factory('tests/configs/test_router_config.php');
        $this->oRouter = $oFactory->getRouter();
    }

    public function getEndpointControllerDataProvider()
    {
        return [
            'update_rest_days' => ['/update_rest_days', 'Sbppgc\Worktime\Endpoints\UpdateRestDaysEndpointController'],
            'shedule' => ['/shedule', 'Sbppgc\Worktime\Endpoints\SheduleEndpointController'],
            'shedule_with_params' => ['/shedule?qwe=1', 'Sbppgc\Worktime\Endpoints\SheduleEndpointController'],
        ];
    }

    /**
     * @dataProvider getEndpointControllerDataProvider
     */
    public function testGetEndpointController($requestUri, $className)
    {
        $oItem = $this->oRouter->getEndpointController($requestUri);
        $this->assertInstanceOf('\Sbppgc\Worktime\Endpoints\EndpointController', $oItem);
        $this->assertEquals($className, get_class($oItem));
    }

    public function testGetEndpointControllerByWrongUri()
    {
        $this->expectException('\Exception');
        $oItem = $this->oRouter->getEndpointController('/some_wrong_uri');
    }

}
