<?php

namespace Sbppgc\Worktime\Tests;

use PHPUnit\Framework\TestCase;
use Sbppgc\Worktime\Factory;

class SheduleEndpointControllerTest extends TestCase
{

    /**
     * Data provider for OK requests
     */
    public function processOkDataProvider(): array
    {
        return [
            /**
             * user #1
             * Vacation: 2019-01-11 - 2019-01-25
             * 2019 rest days: 2019-01-01 - 2019-01-08, 2019-01-12 - 2019-01-13
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'exclude_common_rest_days_and_vacation_and_copporate_event' => [
                '/shedule?startDate=2019-01-01&endDate=2019-01-14&userId=1',
                [
                    [
                        "day" => "2019-01-09",
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
                        "day" => "2019-01-10",
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
                ],
            ],

            /**
             * user #1
             * Vacation: 2019-01-11 - 2019-01-25, 2019-02-01 - 2019-02-15
             * 2019 rest days: 2019-03-16 - 2019-03-17
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'exclude_common_rest_days' => [
                '/shedule?startDate=2019-03-15&endDate=2019-03-18&userId=1',
                [
                    [
                        "day" => "2019-03-15",
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
                        "day" => "2019-03-18",
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

            /**
             * user #1
             * Vacation: 2019-01-11 - 2019-01-25, 2019-02-01 - 2019-02-15
             * 2019 rest days: 2019-01-26 - 2019-01-27, 2019-02-02 - 2019-02-03
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'exclude_vacation_start' => [
                '/shedule?startDate=2019-01-31&endDate=2019-02-05&userId=1',
                [
                    [
                        "day" => "2019-01-31",
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

            /**
             * user #1
             * Vacation: 2019-01-11 - 2019-01-25, 2019-02-01 - 2019-02-15
             * 2019 rest days: 2019-01-26 - 2019-01-27, 2019-02-02 - 2019-02-03
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'exclude_vacation_end_and_rest_days' => [
                '/shedule?startDate=2019-01-25&endDate=2019-01-28&userId=1',
                [
                    [
                        "day" => "2019-01-28",
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

            /**
             * user #1
             * Vacation: 2019-01-11 - 2019-01-25, 2019-02-01 - 2019-02-15
             * 2019 rest days: 2019-01-26 - 2019-01-27, 2019-02-02 - 2019-02-03
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'select_one_work_day' => [
                '/shedule?startDate=2019-01-30&endDate=2019-01-30&userId=1',
                [
                    [
                        "day" => "2019-01-30",
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

            /**
             * user #1
             * Vacation: 2019-01-11 - 2019-01-25, 2019-02-01 - 2019-02-15
             * 2019 rest days: 2019-01-26 - 2019-01-27, 2019-02-02 - 2019-02-03
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'select_one_rest_day' => [
                '/shedule?startDate=2019-01-27&endDate=2019-01-27&userId=1',
                [],
            ],

            /**
             * user #1
             * Vacation: 2019-01-11 - 2019-01-25, 2019-02-01 - 2019-02-15
             * 2019 rest days: 2019-01-26 - 2019-01-27, 2019-02-02 - 2019-02-03
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'dateStart_is_bigger_than_DateEnd' => [
                '/shedule?startDate=2019-01-31&endDate=2019-01-30&userId=1',
                [],
            ],

            /**
             * 2019 rest days: 2019-01-26 - 2019-01-27, 2019-02-02 - 2019-02-03
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'user_not_specified' => [
                '/shedule?startDate=2019-01-30&endDate=2019-01-30',
                [],
            ],

            /**
             * 2019 rest days: 2019-01-26 - 2019-01-27, 2019-02-02 - 2019-02-03
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'userId_is_not_numeric' => [
                '/shedule?startDate=2019-01-30&endDate=2019-01-30&userId=qwe',
                [],
            ],

            /**
             * userId always use as int, numbers after the decimal point are discarded
             * 2019 rest days: 2019-01-26 - 2019-01-27, 2019-02-02 - 2019-02-03
             * 2019 corp event: 2019-01-10 15:00:00 - 2019-01-11 00:00:00
             */
            'userId_is_float' => [
                '/shedule?startDate=2019-01-30&endDate=2019-01-30&userId=1.75',
                [
                    [
                        "day" => "2019-01-30",
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

    /**
     * Data provider for 'wrong dates' requests
     */
    public function processWrongDatesDataProvider(): array
    {
        return [
            'wrong_startDate_empty' => ['/shedule?startDate=&endDate=2019-01-31&userId=1'],
            'wrong_startDate_missing' => ['/shedule?endDate=2019-01-31&userId=1'],
            'wrong_startDate_invalid_format1' => ['/shedule?startDate=2019.01.31&endDate=2019-01-31&userId=1'],
            'wrong_startDate_invalid_format2' => ['/shedule?startDate=qwe&endDate=2019-01-31&userId=1'],
            'wrong_startDate_invalid_format3' => ['/shedule?startDate=2019-qw-03&endDate=2019-01-31&userId=1'],
            'wrong_startDate_invalid_format4' => ['/shedule?startDate=2019-3-4&endDate=2019-03-04&userId=1'],

            'wrong_endDate_empty' => ['/shedule?startDate=2019-01-31&endDate=&userId=1'],
            'wrong_endDate_missing' => ['/shedule?startDate=2019-01-31&userId=1'],
            'wrong_endDate_invalid_format1' => ['/shedule?startDate=2019-01-31&endDate=2019.01.31&userId=1'],
            'wrong_endDate_invalid_format2' => ['/shedule?startDate=2019-01-31&endDate=qwe&userId=1'],
            'wrong_endDate_invalid_format3' => ['/shedule?startDate=2019-01-31&endDate=201q-we-03&userId=1'],
            'wrong_startDate_invalid_format4' => ['/shedule?startDate=2019-03-04&endDate=2019-3-4&userId=1'],
        ];
    }

    /**
     * @dataProvider processWrongDatesDataProvider
     */
    public function testProcessWrongDates(string $request)
    {

        $oFactory = new Factory('tests/configs/test_shedule_endpoint_config.php');

        $oRouter = $oFactory->getRouter();

        $oEndpointController = $oRouter->getEndpointController($request);

        $aDependences = $oFactory->getDependences($oEndpointController->getDependencesList());

        $this->expectException('\Exception');

        $aRes = $oEndpointController->process($aDependences);

    }

}
