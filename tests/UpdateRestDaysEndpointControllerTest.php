<?php

namespace Sbppgc\Worktime\Tests;

use PHPUnit\Framework\TestCase;
use Sbppgc\Worktime\Endpoints\UpdateRestDaysEndpointController;
use Sbppgc\Worktime\Models\DataGovRuRestDaysSource;
use Sbppgc\Worktime\Models\RestDays;

class UpdateRestDaysEndpointControllerTest extends TestCase
{

    protected $oController;

    protected function setUp(): void
    {
        $this->oController = new UpdateRestDaysEndpointController('');
    }

    public function testProcess()
    {
        $aTestData = ['2020-10-10'];

        $aDependences = [
            'RestDaysSourceModel' => $this->getMockRestDaysSourceModel($aTestData, $this->once()),
            'RestDaysModel' => $this->getMockRestDaysModel($aTestData, $this->once()),
        ];

        $aRes = $this->oController->process($aDependences);

        $this->assertIsArray($aRes);
        $this->assertEquals(0, $aRes['code']);
    }

    public function testProcessWithWrongDependences()
    {
        $aDependences = [
            'RestDaysSourceModel' => new \stdClass(),
            'RestDaysModel' => new \stdClass(),
        ];

        $this->expectException(\Exception::class);

        $aRes = $this->oController->process($aDependences);
    }

    public function testProcessWithoutSomeDependences()
    {
        $aTestData = ['2020-10-10'];

        $aDependences = [
            'RestDaysSourceModel' => $this->getMockRestDaysSourceModel($aTestData, $this->never()),
        ];

        $this->expectException(\Exception::class);

        $aRes = $this->oController->process($aDependences);
    }

    protected function getMockRestDaysSourceModel($aReturn, $oCallsCount)
    {
        $oRes = $this->getMockBuilder(DataGovRuRestDaysSource::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRestDaysList'])
            ->getMock();

        $oRes->expects($oCallsCount)
            ->method('getRestDaysList')
            ->willReturn($aReturn);

        return $oRes;
    }

    protected function getMockRestDaysModel(array $aWith, $oCallsCount)
    {
        $oRes = $this->getMockBuilder(RestDays::class)
            ->disableOriginalConstructor()
            ->setMethods(['updateList'])
            ->getMock();

        $oRes->expects($oCallsCount)
            ->method('updateList')
            ->with($aWith);

        return $oRes;
    }

}
