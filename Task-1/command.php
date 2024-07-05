<?php

require_once("TimerScript.php");
require_once("RedisMutex.php");

$timerScript = new RedisMutex();
try {
    if (count($argv) == 2) {
        $timerScript->runWithMutex($argv[1]);
    } else {
        echo("Введите один входной параметр - название функции. Например, fiveSecondTimer");
    }
} catch (RedisException $e) {
    echo($e->getMessage());
} catch (TypeError $error) {
    echo("Не найден скрипт с таким названием. Попробуйте ввести php TimerScript.php fiveSecondTimer.");
}