<?php

require __DIR__ . "/../vendor/autoload.php";

\Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

define("TEMP_DIR", __DIR__ . "/../tmp");

\Tester\Helpers::purge(TEMP_DIR);
\Tracy\Debugger::$logDirectory = TEMP_DIR;
