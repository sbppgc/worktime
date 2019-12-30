<?php

namespace Sbppgc\Worktime\Endpoints;

use Sbppgc\Worktime\ConvertDatesTrait;
use Sbppgc\Worktime\DateFormats;
use Sbppgc\Worktime\Endpoints\EndpointController;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use Spatie\Period\Precision;

/**
 * Endpoint to get emplyee working time
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class SheduleEndpointController extends EndpointController
{
    use ConvertDatesTrait;

    /**
     * List of dependences classes
     *
     * @var array
     */
    protected $aDependencesList = [
        'UserVacationsModel' => '\Sbppgc\Worktime\Models\UserVacationsInterface',
        'RestDaysModel' => '\Sbppgc\Worktime\Models\RestDaysInterface',
        'UserDaySheduleModel' => '\Sbppgc\Worktime\Models\UserDaySheduleInterface',
        'CorpRestEventsModel' => '\Sbppgc\Worktime\Models\CorpRestEventsInterface',
    ];

    /**
     * Process action
     *
     * @param array $aDependences Controller-specific dependences list
     *
     * @return array Response.
     */
    public function process(array $aDependences): array
    {
        $this->checkDependences($aDependences);

        list($userId, $startDate, $endDate) = $this->getRequestParams();

        $aVacationDays = $aDependences['UserVacationsModel']->getVacationDaysByPeriod($userId, $startDate, $endDate);

        $aRestDays = $aDependences['RestDaysModel']->getByPeriod($startDate, $endDate);

        $aAllRestDays = array_values(array_unique(array_merge($aVacationDays, $aRestDays)));

        $aWorkDays = $this->getWorkDaysByPeriod($startDate, $endDate, $aAllRestDays);

        $aOneDayShedule = $aDependences['UserDaySheduleModel']->get($userId);

        $aCorpRestEventsRanges = $aDependences['CorpRestEventsModel']->getByPeriod($startDate, $endDate);

        $aRes = [];
        foreach ($aWorkDays as $day) {
            $aRanges = $this->getDayWorkRanges($day, $aOneDayShedule, $aCorpRestEventsRanges);
            if (count($aRanges)) {
                $aRes[] = [
                    'day' => $day,
                    'timeRanges' => $aRanges,
                ];
            }
        }

        return $aRes;
    }

    /**
     * Check and return request params.
     * userId - parse to int
     * startDate, endDate - check for date format 'Y-m-d'.
     *
     * @return array Array with 3 elements: userId, startDate, endDate
     * @throws \Exception Throws exception if specifies dates not matches with format 'Y-m-d'.
     */
    protected function getRequestParams(): array
    {
        $startDate = trim($this->aRequest['startDate']);
        $endDate = trim($this->aRequest['endDate']);
        if (!$this->isValidDate($startDate)) {
            throw new \Exception('Date value in startDate is invalid: \'' . $startDate . '\'');
        }
        if (!$this->isValidDate($endDate)) {
            throw new \Exception('Date value in endDate is invalid: \'' . $endDate . '\'');
        }

        $userId = intval($this->aRequest['userId']);

        return [
            $userId,
            $startDate,
            $endDate,
        ];
    }

    /**
     * Get all work days in specified period.
     *
     * @param string $startDate Period start date
     * @param string $endDate Period end date
     * @param array $aAllRestDays All rest days in 'Y-m-d' format
     * @return array Days list in 'Y-m-d' format
     */
    protected function getWorkDaysByPeriod(string $startDate, string $endDate, array $aAllRestDays): array
    {
        $aRes = [];

        $startTstamp = $this->dateToTimestamp($startDate);
        $endTstamp = $this->dateToTimestamp($endDate);

        for ($day = $startTstamp; $day <= $endTstamp; $day += DateFormats::DAY_SECONDS) {

            $dayString = date(DateFormats::DFMT, $day);

            // Check is common rest day
            if (!in_array($dayString, $aAllRestDays)) {
                $aRes[] = $dayString;
            }
        }
        return $aRes;
    }

    /**
     * Prepare shedule for one day.
     * It takes typical shedule, and exclude corporate rest events.
     *
     * @param string $day Date in 'Y-m-d' format
     * @param array $aOneDayShedule Working periods list in one typical day
     * @param array $aCorpRestEventsRanges Corporate rest events
     * @return array Work time ranges
     */
    protected function getDayWorkRanges(string $day, array $aOneDayShedule, array $aCorpRestEventsRanges): array
    {
        $aRes = [];

        foreach ($aOneDayShedule as $aWorkRange) {
            $aRes = array_merge($aRes, $this->execludeCorpEvents($day, $aWorkRange, $aCorpRestEventsRanges));
        }

        return $aRes;
    }

    /**
     * Exclude corporate rest events from one work time period.
     *
     * @param string $day Date in 'Y-m-d' format
     * @param array $aWorkRange Continuous period of working time
     * @param array $aCorpRestEventsRanges Corporate rest events
     * @return array Work time ranges
     */
    protected function execludeCorpEvents(string $day, array $aWorkRange, array $aCorpRestEventsRanges): array
    {
        $oWorkPeriod = Period::make(
            $day . " " . $aWorkRange['start'],
            $day . " " . $aWorkRange['end'],
            Precision::MINUTE,
            Boundaries::EXCLUDE_NONE, DateFormats::FULL_DFMT
        );

        // Prepare list of overlaped rest periods.
        // Period::diff(), suddenly, returns result only for overlaped periods.
        $aOverlapedRestPeriods = $this->getOverlapedRestPeriods($oWorkPeriod, $aCorpRestEventsRanges);

        if (count($aOverlapedRestPeriods)) {
            // If found overlaped periods, calc difference over Period::diff method
            $aRes = $this->excludeOverlapedPeriods($oWorkPeriod, $aOverlapedRestPeriods);
        } else {
            // If have no overlaped periods, just return original working period.
            $aRes = [
                [
                    'start' => date(DateFormats::RES_TFMT, $oWorkPeriod->getStart()->getTimestamp()),
                    'end' => date(DateFormats::RES_TFMT, $oWorkPeriod->getEnd()->getTimestamp()),
                ],
            ];
        }
        return $aRes;
    }

    /**
     * Check is corporate rest events overlaps with working time period.
     * Prepare list only with overlaped corporate rest periods.
     *
     * @param Period $oWorkPeriod Working period
     * @param array $aCorpRestEventsRanges Corporate rest events
     * @return array Corporate rest events, that overlaps working period
     */
    protected function getOverlapedRestPeriods(Period $oWorkPeriod, array $aCorpRestEventsRanges): array
    {
        $aRes = [];
        foreach ($aCorpRestEventsRanges as $aRestRange) {

            $aRestPeriod = Period::make(
                $aRestRange['time_start'],
                $aRestRange['time_end'],
                Precision::MINUTE,
                Boundaries::EXCLUDE_ALL, DateFormats::FULL_DFMT
            );

            if ($oWorkPeriod->overlapsWith($aRestPeriod)) {
                $aRes[] = $aRestPeriod;
            }

        }
        return $aRes;
    }

    /**
     * Exclude overlaped corporate rest events from working period.
     * Result contains only time values, in specific format.
     *
     * @param Period $oWorkPeriod Working period
     * @param array $aOverlapedRestPeriods Corporate rest events, that overlaps working time
     * @return array Remaining working time periods
     */
    protected function excludeOverlapedPeriods(Period $oWorkPeriod, array $aOverlapedRestPeriods): array
    {
        $aRes = [];
        $aDiffPeriodsCollection = $oWorkPeriod->diff(...$aOverlapedRestPeriods);

        foreach ($aDiffPeriodsCollection as $aPeriod) {
            $aRes[] = [
                'start' => date(DateFormats::RES_TFMT, $aPeriod->getStart()->getTimestamp()),
                'end' => date(DateFormats::RES_TFMT, $aPeriod->getEnd()->getTimestamp()),
            ];
        }

        return $aRes;
    }

}
