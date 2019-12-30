<?php

namespace Sbppgc\Worktime\Tests;

use PHPUnit\Framework\TestCase;
use Sbppgc\Worktime\Factory;

class SheduleEndpointControllerTest extends TestCase
{

    public function processOkDataProvider(): array
    {
        return [
            'intersects_with_rest_days_vacation_corp_event' => [
                '/shedule?startDate=2018-01-01&endDate=2018-01-14&userId=1',
                [
                    [
                        "day" => "2018-01-09",
                        "timeRanges" => [
                            [
                                "start" => "1000",
                                "end" => "1300",
                            ],
                            [
                                "start" => "1400",
                                "end" => "1900",
                            ],
                        ],
                    ],
                    [
                        "day" => "2018-01-10",
                        "timeRanges" => [
                            [
                                "start" => "1000",
                                "end" => "1300",
                            ],
                            [
                                "start" => "1400",
                                "end" => "1500",
                            ],
                        ],
                    ],
                    [
                        "day" => "2018-01-11",
                        "timeRanges" => [
                            [
                                "start" => "1000",
                                "end" => "1300",
                            ],
                            [
                                "start" => "1400",
                                "end" => "1900",
                            ],
                        ],
                    ],
                    [
                        "day" => "2018-01-12",
                        "timeRanges" => [
                            [
                                "start" => "1000",
                                "end" => "1300",
                            ],
                            [
                                "start" => "1400",
                                "end" => "1900",
                            ],
                        ],
                    ],
                ],

            ],

        ];
    }

    /**
     * @dataProvider processOkDataProvider
     */
    public function testProcessOk(string $request, array $aExpectedResult)
    {

        $oFactory = new Factory('tests/configs/test_shedule_endpoint_config.php');

        $oRouter = $oFactory->getRouter();

        $oEndpointController = $oRouter->getEndpointController($request);

        $aDependences = $oFactory->getDependences($oEndpointController->getDependencesList());

        $aRes = $oEndpointController->process($aDependences);

        $this->assertEquals($aExpectedResult, $aRes);
    }

}
