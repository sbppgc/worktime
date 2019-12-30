<?php

namespace Sbppgc\Worktime\Models;

use \MysqliDb;

/**
 * Common rest days model interface
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
interface RestDaysInterface
{
    public function __construct(MysqliDb $oDB);
    public function getByPeriod(string $startDate, string $endDate): array;
}
