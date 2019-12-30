<?php

namespace Sbppgc\Worktime;

use Sbppgc\Worktime\Config;
use Sbppgc\Worktime\Endpoints\EndpointController;

/**
 * Detect and init endpoint controllers by request string
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class Router
{

    /**
     * Prepared config object
     *
     * @var \Sbppgc\Worktime\Config
     */
    protected $oConfig;

    /**
     * Constructor
     *
     * @param \Sbppgc\Worktime\Config $oConfig Config object
     * @return void
     */
    public function __construct(Config $oConfig)
    {
        $this->oConfig = $oConfig;
    }

    /**
     * Detect endpoint controller by request string
     *
     * @param string $aData Request vars array
     * @return \Sbppgc\Worktime\Endpoints\EndpointController Endpoint controller
     *
     * @throws \Exception Generate exception if endpoint controller is not found.
     *
     */
    public function getEndpointController(string $requestUri): EndpointController
    {
        $cleanUri = $this->stripParams($requestUri);
        if ($cleanUri == '') {
            throw new \Exception('Wrong request');
        }
        $aRoutes = $this->oConfig->get('routes');
        if (!isset($aRoutes[$cleanUri])) {
            throw new \Exception('Unsupported method \'' . $cleanUri . '\'');
        }
        return new $aRoutes[$cleanUri]($requestUri);
    }

    /**
     * Erase GET parameters from request string
     *
     * @param string $requestUri Original request string
     * @return string Request string without GET parameters
     */
    protected function stripParams(string $requestUri): string
    {
        $aParts = explode('?', $requestUri);
        $res = trim($aParts[0], '/');
        return $res;
    }

}
