<?php

namespace Sbppgc\Worktime\Models;

use \Curl\Curl;

/**
 * Rest days source for data.gov.ru
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class DataGovRuRestDaysSource implements RestDaysSourceInterface
{

    /**
     * Year key name in data.gov.ru response
     */
    const YEAR = 'Год/Месяц';

    /**
     * Month key names in data.gov.ru response
     */
    const MONTHS = [
        'Январь' => 1,
        'Февраль' => 2,
        'Март' => 3,
        'Апрель' => 4,
        'Май' => 5,
        'Июнь' => 6,
        'Июль' => 7,
        'Август' => 8,
        'Сентябрь' => 9,
        'Октябрь' => 10,
        'Ноябрь' => 11,
        'Декабрь' => 12,
    ];

    /**
     * data.gov.ru connection params
     *
     * @var array
     */
    protected $aParams = null;

    /**
     * Curl helper object
     *
     * @var \Curl\Curl
     */
    protected $oCurl = null;

    /**
     * Constructor
     *
     * @param \Sbppgc\Worktime\Factory $oFactory Factory object
     */
    public function __construct(array $aParams, curl $oCurl)
    {
        $this->aParams = $aParams;
        $this->oCurl = $oCurl;
    }

    /**
     * Receive rest days list from data.gov.ru
     *
     * @return array Associative array with rest days.
     * Top level keys is years, sublevel - months (from 1)
     */
    public function getRestDaysList(): array
    {
        // Get last version name
        $version = $this->getLastVersion();

        $aData = $this->getVersionContent($version);

        return $this->prepareData($aData);
    }

    /**
     * Receive rest days list versions from data.gov.ru and return actual version name.
     *
     * @return string Actual data version name.
     * @throws \Exception Throws exception if request fail or received data is empty.
     */
    protected function getLastVersion(): string
    {
        $url = $this->aParams['baseUrl'] . $this->aParams['versionsUrl']
        . '?access_token=' . $this->aParams['accessToken'];

        $this->oCurl->get($url);
        if ($this->oCurl->httpStatusCode != 200) {
            throw new \Exception('Get rest days versions list fail. Wrong HTTP response code: ' . $this->oCurl->httpStatusCode);
        }

        $res = trim($this->oCurl->rawResponse);
        if ($res == '') {
            throw new \Exception('Receive rest days versions list fail.');
        }

        $aVersions = json_decode($res, true);
        if (!count($aVersions)) {
            throw new \Exception('Have no one rest days versions list. Update fail.');
        }
        return trim($aVersions[0]['created']);
    }

    /**
     * Receive rest days data from data.gov.ru by actual data version name.
     *
     * @param string $varsion Data version name
     *
     * @return array Raw rest days data.
     *
     * @throws \Exception Throws exception if actial data version name is empty (version exists, but name is empty).
     * @throws \Exception Throws exception on any error in request to data.gov.ru.
     */
    protected function getVersionContent(string $version): array
    {

        if ($version == '') {
            throw new \Exception('Get rest days content fail. Version name is empty.');
        }

        $url = $this->aParams['baseUrl'] . $this->aParams['versionsUrl']
        . $version . $this->aParams['contentUrlPostfix']
        . '?access_token=' . $this->aParams['accessToken'];

        $this->oCurl->get($url);
        if ($this->oCurl->httpStatusCode != 200) {
            throw new \Exception('Get rest days content fail. Wrong HTTP response code: ' . $this->oCurl->httpStatusCode);
        }

        $res = trim($this->oCurl->rawResponse);
        if ($res == '') {
            throw new \Exception('Receive rest days content fail (empty data).');
        }

        return json_decode($res, true);
    }

    /**
     * Convert raw rest days data to usable structure.
     *
     * @param array $aData Raw rest days data
     *
     * @return array Prepared data.
     */
    protected function prepareData(array $aData): array
    {

        $aRes = [];
        foreach ($aData as $aYearData) {
            $aPreparedYear = $this->prepareYearData($aYearData);
            $aFlatYearDays = $this->getFlatYearData($aPreparedYear);
            $aRes = array_merge($aRes, $aFlatYearDays);
        }
        return $aRes;
    }

    /**
     * Convert raw rest days data to usable structure for one year.
     *
     * @param array $aYearData Raw rest days data for one year
     *
     * @return array Prepared data. Associative array. Keys is months numbers from 1. Values - arrays with rest days.
     */
    protected function prepareYearData(array $aYearData): array
    {
        $aRes = [];
        foreach ($aYearData as $key => $val) {
            if ($key == self::YEAR) {
                $aRes['year'] = intval($val);
            }
            if (isset(self::MONTHS[$key])) {
                $aRes['aMonths'][self::MONTHS[$key]] = $this->parseMonthDays($val);
            }
        }
        return $aRes;
    }

    /**
     * Convert raw rest days data to usable structure for one month.
     *
     * @param string $daysList Raw rest days data for one month
     *
     * @return array Prepared data. Simple array with rest days list.
     */
    protected function parseMonthDays(string $daysList): array
    {
        $aRes = [];
        if (preg_match_all('/[0-9]+/', $daysList, $aMatch)) {
            $aRes = $aMatch[0];
        }
        return $aRes;
    }

    /**
     * Convert raw rest days data to usable structure for one month.
     *
     * @param string $daysList Raw rest days data for one month
     *
     * @return array Prepared data. Simple array with rest days list.
     */
    protected function getFlatYearData(array $aYearData): array
    {
        $aRes = [];
        foreach ($aYearData['aMonths'] as $month => $aDays) {
            foreach ($aDays as $day) {
                $aRes[] = $aYearData['year'] . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day);
            }
        }
        return $aRes;
    }

}
