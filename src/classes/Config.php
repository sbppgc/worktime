<?php

namespace Sbppgc\Worktime;

/**
 * Get employee working time service.
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class Config
{

    /**
     * Prepared config data
     *
     * @var array
     */
    private $aData = null;

    /**
     * Constructor.
     *
     * @param string $configFileName File name with config data.
     * @return void
     */
    public function __construct(string $configFileName)
    {
        if (file_exists($configFileName)) {
            $this->aData = require $configFileName;
            if (!is_array($this->aData)) {
                throw new \Exception('Config error: Config file content is invalid.');
            }
        } else {
            throw new \Exception('Config error: File not found \'' . $configFileName . '\'');
        }
    }

    /**
     * Get config item value
     *
     * @param string $key Config item key.
     * @param mixed $defaultVal Default value (optional). If not specified, default value is NULL.
     * @return mixed
     */
    public function get($key, $defaultVal = null)
    {
        if (isset($this->aData[$key])) {
            return $this->aData[$key];
        } else {
            return $defaultVal;
        }
    }

}
