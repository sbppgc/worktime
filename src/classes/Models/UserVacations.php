<?php

namespace Sbppgc\Worktime\Models;

use Sbppgc\Worktime\ConvertDatesTrait;
use Sbppgc\Worktime\DateFormats;
use Sbppgc\Worktime\Models\UserVacationsInterface;
use \MysqliDb;

/**
 * User vacations model
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class UserVacations implements UserVacationsInterface
{
    use ConvertDatesTrait;

    /**
     * Table name
     */
    protected const TABLE_NAME = 'user_vacations';

    /**
     * DB object
     *
     * @var \MysqliDb
     */
    protected $oDB = null;

    /**
     * Constructor
     *
     * @param \MysqliDb $oDB DB object
     */
    public function __construct(MysqliDb $oDB)
    {
        $this->oDB = $oDB;
    }

    /**
     * Get all vacation dates of specified user, which intersects with date range.
     *
     * @param int $idUser User id
     * @param string $startDate First day of period
     * @param string $endDate Last day of period
     * @return array List of dates
     */
    public function getVacationDaysByPeriod(int $idUser, string $startDate, string $endDate): array
    {
        //$oDB = $this->oDB::getInstance();
        //$this->oDB->setTrace(true);
        $this->oDB->where('id_user', $idUser);
        $this->oDB->where('first_day', [$startDate, $endDate], 'BETWEEN');
        $this->oDB->orWhere('last_day', [$startDate, $endDate], 'BETWEEN');
        $aIntersectVacations = $this->oDB->get(static::TABLE_NAME, null, ['first_day', 'last_day']);
        //print_r($this->oDB->trace);
        return $this->getIntersectDayList($aIntersectVacations, $startDate, $endDate);
    }

    /**
     * Get list of vacation days in specified period (dates like 'YYYY-MM-DD')
     *
     * @param array $aIntersectVacations User vacations list
     * @param string $startDate Period first day
     * @param string $endDate Period last day
     * @return array Vacation days list (dates like 'YYYY-MM-DD')
     */
    protected function getIntersectDayList(array $aIntersectVacations, string $startDate, string $endDate): array
    {
        $aRes = [];

        $periodStartTstamp = $this->dateToTimestamp($startDate);
        $periodEndTstamp = $this->dateToTimestamp($endDate);

        foreach ($aIntersectVacations as $aRange) {

            $vacationStartTstamp = $this->dateToTimestamp($aRange['first_day']);
            $vacationEndTstamp = $this->dateToTimestamp($aRange['last_day']);

            for ($day = $vacationStartTstamp; $day <= $vacationEndTstamp; $day += DateFormats::DAY_SECONDS) {
                if ($day >= $periodStartTstamp && $day <= $periodEndTstamp) {
                    $aRes[] = date(DateFormats::DFMT, $day);
                }
            }
        }

        return $aRes;
    }

}
