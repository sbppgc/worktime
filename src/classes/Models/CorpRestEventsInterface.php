<?php

namespace Sbppgc\Worktime\Models;

use \MysqliDb;

/**
 * Corporate rest events interface
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
interface CorpRestEventsInterface
{
    public function __construct(MysqliDb $oDB);
    public function getByPeriod(string $startDate, string $endDate): array;
}
