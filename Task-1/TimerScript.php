<?php

function fiveSecondTimer(): void
{
    echo("Скрипт 'fiveSecondTimer' начал работу. Ждем 5 секунд.\n");
    sleep(5);
    echo("Скрипт 'fiveSecondTimer' закончил работу.\n");
}

function tenSecondTimer(): void
{
    echo("Скрипт 'tenSecondTimer' начал работу. Ждем 10 секунд.\n");
    sleep(10);
    echo("Скрипт 'tenSecondTimer' закончил работу.\n");
}