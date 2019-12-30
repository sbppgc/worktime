<?php

namespace Sbppgc\Worktime;

/**
 * Trait for check and convert dates
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
trait ConvertDatesTrait
{

    /**
     * Convert date string to timestamp
     *
     * @param string $date Date value string
     * @param string $dfmt Date format. Default value 'Y-m-d'
     * @param string $checkDfmtRg Date validation regexp. Default value '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'
     * @return int Timestamp
     * @throws \Exception Throw exception if date string is invalid.
     */
    protected function dateToTimestamp(string $date, string $dfmt = 'Y-m-d', string $checkDfmtRg = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'): int
    {
        if (!$this->isValidDate($date, $checkDfmtRg)) {
            throw new \Exception('Date value is invalid: \'' . $date . '\'');
        }
        $oDate = \DateTime::createFromFormat($dfmt, $date);
        return $oDate->getTimestamp();
    }

    /**
     * Check date string is valid
     *
     * @param string $date Date value string
     * @param string $checkDfmtRg Date validation regexp. Default value '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'
     * @return bool Valid/Invalid string
     */
    protected function isValidDate(string $date, string $checkDfmtRg = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/'): bool
    {
        return (preg_match($checkDfmtRg, $date)) ? true : false;
    }

    /**
     * Convert datetime string to timestamp
     *
     * @param string $datetime Datetime value string
     * @param string $dfmt Date format. Default value 'Y-m-d'
     * @param string $checkDfmtRg Date validation regexp. Default value '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/'
     * @return int Timestamp
     * @throws \Exception Throw exception if datetime string is invalid.
     */
    protected function datetimeToTimestamp(string $datetime, string $dfmt = 'Y-m-d H:i:s', string $checkDfmtRg = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/'): int
    {
        if (!$this->isValidDatetime($datetime, $checkDfmtRg)) {
            throw new \Exception('Datetime value is invalid: \'' . $datetime . '\'');
        }
        $oDate = \DateTime::createFromFormat($dfmt, $datetime);
        return $oDate->getTimestamp();
    }

    /**
     * Check datetime string is valid
     *
     * @param string $datetime Date value string
     * @param string $checkDfmtRg Date validation regexp. Default value '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/'
     * @return bool Valid/Invalid string
     */
    protected function isValidDatetime(string $datetime, string $checkDfmtRg = '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/'): bool
    {
        return (preg_match($checkDfmtRg, $datetime)) ? true : false;
    }

}
