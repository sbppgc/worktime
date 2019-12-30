<?php

namespace Sbppgc\Worktime\Models;

use \MysqliDb;
use \Sbppgc\Worktime\Models\UserDaySheduleInterface;

/**
 * User day shedule model
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
class UserDayShedule implements UserDaySheduleInterface
{

    /**
     * Table name
     */
    protected const TABLE_NAME = 'user_day_shedules';

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
     * Get list of working time ranges for one day
     *
     * @param int $idUser User id
     * @return array List of time ranges
     */
    public function get(int $idUser): array
    {
        $this->oDB->where('id_user', $idUser);
        $this->oDB->orderBy("start", "asc");
        return $this->oDB->get(static::TABLE_NAME, null, ['start', 'end']);
    }

}
