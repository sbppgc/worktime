<?php

namespace Sbppgc\Worktime\Endpoints;

/**
 * Abstract class with endpoints common functionality
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
abstract class EndpointController
{

    /**
     * List of dependences classes
     * Key is name of dependence (Factory megtod without 'get' prefix).
     * Value is expected type of object
     *
     * @var array
     */
    protected $aDependencesList = [];

    /**
     * Constructor
     *
     * @param string $requestUri Original request string
     */
    public function __construct(string $requestUri)
    {
        $this->aRequest = $this->parseRequestUri($requestUri);
    }

    /**
     * Returns dependences list
     *
     * @return array $requestUri Original request string
     */
    public function getDependencesList(): array
    {
        return $this->aDependencesList;
    }

    /**
     * Extract params from original request string
     *
     * @param string $requestUri Original request string
     * @return array GET params
     */
    protected function parseRequestUri(string $requestUri): array
    {
        $aRes = [];
        $aParts = explode('?', $requestUri);
        $aParams = explode('&', $aParts[1]);
        foreach ($aParams as $param) {
            $aPair = explode('=', $param);
            $key = trim($aPair[0]);
            if ($key != '') {
                $aRes[$key] = trim(urldecode($aPair[1]));
            }
        }
        return $aRes;
    }

    /**
     * Check dependences
     *
     * @param array $aDependences Controller-specific dependences list
     * @throws \Exception If any dependence is not exisit or wrong type
     */
    protected function checkDependences(array $aDependences)
    {
        foreach ($this->aDependencesList as $item => $type) {
            if (!isset($aDependences[$item])) {
                throw new \Exception("aDependences['" . $item . "'] is not specified");
            }

            if (!($aDependences[$item] instanceof $type)) {
                throw new \Exception("aDependences['" . $item . "'] is not instance of " . $type);
            }

        }
    }

    /**
     * Process action
     *
     * @param array $aDependences Controller-specific dependences list
     *
     * @return array Response
     */
    abstract public function process(array $aDependences): array;

}
