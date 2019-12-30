<?php

namespace Sbppgc\Worktime\Models;

use Sbppgc\Worktime\ConvertDatesTrait;
use \MysqliDb;
use \Sbppgc\Worktime\Models\RestDaysInterface;

/**
 * Common rest days model
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class RestDays implements RestDaysInterface
{
    use ConvertDatesTrait;

    /**
     * Table name
     */
    protected const TABLE_NAME = 'rest_days';

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
     * Get common rest days list for specified period
     *
     * @param string $startDate First day of period
     * @param string $endDate Last day of period
     * @return array List of dates
     */
    public function getByPeriod(string $startDate, string $endDate): array
    {
        if (!$this->isValidDate($startDate)) {
            throw new \Exception('Invalid date string in startDate: \'' . $startDate . '\'');
        }
        if (!$this->isValidDate($endDate)) {
            throw new \Exception('Invalid date string in endDate: \'' . $endDate . '\'');
        }

        $this->oDB->where('day', $startDate, '>=');
        $this->oDB->where('day', $endDate, '<=');
        $aRes = $this->oDB->get(static::TABLE_NAME, null, ['day']);

        // Make flat days list
        array_walk($aRes, function (&$item) {
            $item = $item['day'];
        });

        return $aRes;
    }

    /**
     * Replace rest days list to specified
     *
     * @param array List of dates
     */
    public function updateList(array $aList)
    {

        // Drop old data
        $this->dropList();

        // Kick invalid vales
        array_walk($aList, array($this, 'cleanInvalidDatesCB'));
        $aStripVals = [''];
        $aValidVals = array_values(array_diff($aList, $aStripVals));

        // Make associative values for db query
        array_walk($aValidVals, function (&$date) {
            $date = ['day' => $date];
        });

        // Insert data to db
        $this->oDB->insertMulti(static::TABLE_NAME, $aValidVals);

    }

    /**
     * Drop all old data from table
     *
     * @param array List of dates
     * @return bool Delete request result
     */
    protected function dropList(): bool
    {
        return $this->oDB->delete(static::TABLE_NAME);
    }

    /**
     * Callback to check is date string valid. If not valid, replace value by empty string.
     *
     * @param string Date to check
     */
    protected function cleanInvalidDatesCB(&$date)
    {
        if (!$this->isValidDate($date)) {
            $date = '';
        }
    }
}
