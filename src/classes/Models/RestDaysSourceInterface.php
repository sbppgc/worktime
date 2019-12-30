<?php

namespace Sbppgc\Worktime\Models;

use \Curl\Curl;

/**
 * Rest days source model interface (weekends and holidays)
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */
interface RestDaysSourceInterface
{
    public function __construct(array $aParams, curl $oCurl);
    public function getRestDaysList(): array;
}
