<?php

namespace JianliaoNotifier;

use JianliaoNotifier\Jobs\ExceptionReport;

class Notifier
{
    public static function notify($e)
    {
        dispatch(new ExceptionReport($e));
    }
}
