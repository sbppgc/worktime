<?php

namespace Sbppgc\Worktime\Models;

use Sbppgc\Worktime\ConvertDatesTrait;
use \MysqliDb;

/**
 * Corporate rest events model
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class CorpRestEvents implements CorpRestEventsInterface
{
    use ConvertDatesTrait;

    /**
     * Table name
     */
    protected const TABLE_NAME = 'corp_rest_events';

    /**
     * SQL format time value for a day start.
     */
    protected const SQL_DAY_START_DATE_POSTFIX = ' 00:00:00';

    /**
     * SQL format time value for a day end.
     */
    protected const SQL_DAY_END_DATE_POSTFIX = ' 23:59:59';

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
     * Get corp events date-time ranges list for specified period
     *
     * @param string $startDate First day of period
     * @param string $endDate Last day of period
     * @return array List of date-time ranges
     */
    public function getByPeriod(string $startDate, string $endDate): array
    {
        if (!$this->isValidDate($startDate)) {
            throw new \Exception('Invalid date string in startDate: \'' . $startDate . '\'');
        }
        if (!$this->isValidDate($endDate)) {
            throw new \Exception('Invalid date string in endDate: \'' . $endDate . '\'');
        }
        $startFullDate .= $startDate . static::SQL_DAY_START_DATE_POSTFIX;
        $endFullDate .= $endDate . static::SQL_DAY_END_DATE_POSTFIX;

        $this->oDB->where('time_start', $endFullDate, '<');
        $this->oDB->where('time_end', $startFullDate, '>');
        return $this->oDB->get(static::TABLE_NAME, null, ['time_start', 'time_end']);
    }
}
