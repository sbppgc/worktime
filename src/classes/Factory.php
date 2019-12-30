<?php

namespace Sbppgc\Worktime;

use Curl\Curl;
use MysqliDb;
use Sbppgc\Worktime\Config;
use Sbppgc\Worktime\Models\CorpRestEvents;
use Sbppgc\Worktime\Models\CorpRestEventsInterface;
use Sbppgc\Worktime\Models\RestDays;
use Sbppgc\Worktime\Models\RestDaysInterface;
use Sbppgc\Worktime\Models\RestDaysSourceInterface;
use Sbppgc\Worktime\Models\UserDayShedule;
use Sbppgc\Worktime\Models\UserDaySheduleInterface;
use Sbppgc\Worktime\Models\UserVacations;
use Sbppgc\Worktime\Models\UserVacationsInterface;
use Sbppgc\Worktime\Router;

/**
 * Create objects
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class Factory
{

    /**
     * Config file name
     *
     * @var string
     */
    protected $configFileName;

    /**
     * Initialized config engine
     *
     * @var \Sbppgc\Worktime\Config
     */
    protected $oConfig = null;

    /**
     * Forced DB object
     *
     * @var \MysqliDb
     */
    protected $oForcedDB = null;

    /**
     * Constructor
     *
     * @param string $configFileName Configuration file name
     * @return void
     */
    public function __construct(string $configFileName, ?MysqliDb $oForcedDB = null)
    {
        $this->configFileName = $configFileName;
        $this->oForcedDB = $oForcedDB;
    }

    /**
     * Get initialized config engine
     *
     * @return \Sbppgc\Worktime\Config Config object
     */
    public function getSingletonConfig(): Config
    {
        if (is_null($this->oConfig)) {
            $this->oConfig = new Config($this->configFileName);
        }
        return $this->oConfig;
    }

    /**
     * Get DB object
     *
     * @return \MysqliDb Router object
     */
    public function getDb(): MysqliDb
    {
        if (!is_null($this->oForcedDB)) {
            return $this->oForcedDB;
        } else {
            $oConfig = $this->getSingletonConfig();
            $aParams = $oConfig->get('db');
            $className = $oConfig->get('dbClassName');

            if (!class_exists($className)) {
                throw new \Exception('Init db error: Class not exists \'' . $className . '\'');
            }

            return new $className($aParams);
        }
    }

    /**
     * Get Router object
     *
     * @return \Sbppgc\Worktime\Router Router object
     */
    public function getRouter(): Router
    {
        return new Router($this->getSingletonConfig());
    }

    /**
     * Get user day shedule model object
     *
     * @return \Sbppgc\Worktime\Models\UserDaySheduleInterface Instance of user day shedule model
     */
    public function getUserDaySheduleModel(): UserDaySheduleInterface
    {
        $className = $this->getSingletonConfig()->get('userDaySheduleModel');
        if (!class_exists($className)) {
            throw new \Exception('Init user day shedule model error: Class not exists \'' . $className . '\'');
        }
        return new $className($this->getDb());
    }

    /**
     * Get user vacations model object
     *
     * @return \Sbppgc\Worktime\Models\UserVacationsInterface Instance of user vacations model
     */
    public function getUserVacationsModel(): UserVacationsInterface
    {
        $className = $this->getSingletonConfig()->get('userVacationsModel');
        if (!class_exists($className)) {
            throw new \Exception('Init user vacations model error: Class not exists \'' . $className . '\'');
        }
        return new $className($this->getDb());
    }

    /**
     * Get Rest days model object
     *
     * @return \Sbppgc\Worktime\Models\RestDaysInterface Instance of rest days model
     */
    public function getRestDaysModel(): RestDaysInterface
    {
        $oConfig = $this->getSingletonConfig();
        $className = $oConfig->get('restDaysModel');
        if (!class_exists($className)) {
            throw new \Exception('Init rest days model error: Class not exists \'' . $className . '\'');
        }
        return new $className($this->getDb());
    }

    /**
     * Get Rest days source model object
     *
     * @return \Sbppgc\Worktime\Models\RestDaysSourceInterface Instance of rest days source model
     */
    public function getRestDaysSourceModel(): RestDaysSourceInterface
    {
        $oConfig = $this->getSingletonConfig();
        $optionName = $oConfig->get('restDaysSourceOptionName');

        $aConfig = $oConfig->get($optionName);

        $className = $aConfig['modelClassName'];

        if (!class_exists($className)) {
            throw new \Exception('Init rest days source model error: Class not exists \'' . $className . '\'');
        }
        return new $className($aConfig, $this->getCurlHelper());
    }

    /**
     * Get corporate rest events model object
     *
     * @return \Sbppgc\Worktime\Models\CorpRestEventsInterface Instance of custom exclude ranges model
     */
    public function getCorpRestEventsModel(): CorpRestEventsInterface
    {
        $oConfig = $this->getSingletonConfig();
        $className = $oConfig->get('corpRestEventsModel');
        if (!class_exists($className)) {
            throw new \Exception('Init custom exclude ranges model error: Class not exists \'' . $className . '\'');
        }
        return new $className($this->getDb());
    }

    /**
     * Get CURL helper object
     *
     * @return \Curl\Curl Instance of CURL helper
     */
    public function getCurlHelper(): Curl
    {
        return new \Curl\Curl();
    }

    /**
     * Create dependences by list
     *
     * @return array Array of dependences
     */
    public function getDependences(array $aList): array
    {
        $aRes = [];

        foreach ($aList as $item => $type) {
            $methodName = 'get' . trim($item);
            if (is_callable([$this, $methodName])) {
                $aRes[$item] = $this->$methodName();
            }
        }

        return $aRes;
    }

}
