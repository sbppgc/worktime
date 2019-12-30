<?php return [
    'routes' => [
        'shedule' => '\Sbppgc\Worktime\Endpoints\SheduleEndpointController',
        'not_at_work' => '\Sbppgc\Worktime\Endpoints\NotAtWorkEndpointController',
        'update_rest_days' => '\Sbppgc\Worktime\Endpoints\UpdateRestDaysEndpointController',
    ],

    'dbClassName' => '\Sbppgc\Worktime\Tests\Stub\MysqliDbStub',

    'db' => [
        'host' => 'localhost',
        'username' => 'wrong_user',
        'password' => 'wrong_password',
        'db'=> 'wrong_db',
        'charset' => 'utf8',
    ],

    'userDaySheduleModel' => '\Sbppgc\Worktime\Models\UserDayShedule',
    'userVacationsModel' => '\Sbppgc\Worktime\Models\UserVacations',
    'restDaysModel' => '\Sbppgc\Worktime\Models\RestDays',
    'corpRestEventsModel' => '\Sbppgc\Worktime\Models\CorpRestEvents',

    'restDaysSourceOptionName' => 'DataGovRu',

    'DataGovRu' => [
        'modelClassName' => '\Sbppgc\Worktime\Models\DataGovRuRestDaysSource',
        'accessToken' => 'wrong_token',
        'baseUrl' => 'https://data.gov.ru/api/json/dataset/7708660670-proizvcalendar/',
        'versionsUrl' => 'version/',
        'contentUrlPostfix' => '/content',
    ],

];