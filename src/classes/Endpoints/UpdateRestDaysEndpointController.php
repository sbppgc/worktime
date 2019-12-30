<?php

namespace Sbppgc\Worktime\Endpoints;

use Sbppgc\Worktime\Endpoints\EndpointController;

/**
 * Endpoint to update local rest days list from remote service
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class UpdateRestDaysEndpointController extends EndpointController
{

    /**
     * List of dependences classes
     *
     * @var array
     */
    protected $aDependencesList = [
        'RestDaysSourceModel' => '\Sbppgc\Worktime\Models\RestDaysSourceInterface',
        'RestDaysModel' => '\Sbppgc\Worktime\Models\RestDaysInterface',
    ];

    /**
     * Process action
     *
     * @param array $aDependences Controller-specific dependences list
     *
     * @return array A response to send.
     */
    public function process(array $aDependences): array
    {
        $this->checkDependences($aDependences);

        $aList = $aDependences['RestDaysSourceModel']->getRestDaysList();

        $aDependences['RestDaysModel']->updateList($aList);

        return [
            'code' => 0,
            'msg' => 'Rest days list updated',
        ];
    }

}
