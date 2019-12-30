<?php

namespace Sbppgc\Worktime\Models;

use \MysqliDb;

/**
 * User vacations interface
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
interface UserVacationsInterface
{
    public function __construct(MysqliDb $oDB);
    public function getVacationDaysByPeriod(int $idUser, string $startDate, string $endDate): array;
}
