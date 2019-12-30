<?php
namespace Sbppgc\Worktime\Tests\Stub;

use MysqliDb;

class MysqliDbStub extends MysqliDb
{
    public function __construct($host = null, $username = null, $password = null, $db = null, $port = null, $charset = 'utf8', $socket = null)
    {
        return $this;
    }
}
