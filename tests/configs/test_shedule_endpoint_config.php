<?php return [
    'routes' => [
        'shedule' => 'Sbppgc\Worktime\Endpoints\SheduleEndpointController',
        'not_at_work' => 'Sbppgc\Worktime\Endpoints\NotAtWorkEndpointController',
        'update_rest_days' => 'Sbppgc\Worktime\Endpoints\UpdateRestDaysEndpointController',
    ],

    'dbClassName' => '\MysqliDb',

    'db' => [
        'host' => 'localhost',
        'username' => 'username',
        'password' => 'password',
        'db'=> 'db_name_test',
        'charset' => 'utf8',
    ],

    'userDaySheduleModel' => 'Sbppgc\Worktime\Models\UserDayShedule',
    'userVacationsModel' => 'Sbppgc\Worktime\Models\UserVacations',
    'restDaysModel' => 'Sbppgc\Worktime\Models\RestDays',
    'corpRestEventsModel' => 'Sbppgc\Worktime\Models\CorpRestEvents',

    'restDaysSourceOptionName' => 'DataGovRu',

    'DataGovRu' => [
        'modelClassName' => 'Sbppgc\Worktime\Models\DataGovRuRestDaysSource',
        'accessToken' => 'get_tokern_on_data.gov.ru',
        'baseUrl' => 'https://data.gov.ru/api/json/dataset/7708660670-proizvcalendar/',
        'versionsUrl' => 'version/',
        'contentUrlPostfix' => '/content',
    ],

];