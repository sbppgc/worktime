<?php

namespace Sbppgc\Worktime\Models;

use \MysqliDb;

/**
 * User day shedule model interface
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
interface UserDaySheduleInterface
{
    public function __construct(MysqliDb $oDB);
    public function get(int $idUser): array;
}
