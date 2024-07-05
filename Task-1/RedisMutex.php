<?php

class RedisMutex
{
    private const REDIS_HOST = "localhost";
    private const REDIS_PORT = "6379";

    private const REDIS_TTL = 10;

    private Redis $redis;

    public function __construct()
    {
        try {
            $this->redis = new Redis();
            $this->redis->connect(self::REDIS_HOST, self::REDIS_PORT);
        } catch (Exception $e) {
            echo("Не удалось подключиться к Redis. Проверьте указанные данные.");
        }
    }

    /**
     * @throws RedisException
     */
    public function __destruct()
    {
        $this->redis->close();
        unset($this->redis);
    }

    /**
     * @throws RedisException
     */
    private function blockIfNecessary(string $scriptName): bool
    {
        if ($this->redis->get($scriptName)) {
            return false;
        }
        $this->redis->set($scriptName, new DateTime(), ['EX' => self::REDIS_TTL]);
        return true;
    }

    /**
     * @throws RedisException
     */
    private function unblockScript(string $scriptName): void
    {
        $this->redis->del($scriptName);
    }

    /**
     * @throws RedisException
     */
    public function runWithMutex(callable $scriptName): void
    {
        if ($this->blockIfNecessary($scriptName)) {
            call_user_func($scriptName);
            $this->unblockScript($scriptName);
            return;
        };
        echo("Скрипт '" . $scriptName . "' был запущен ранее. Подождите завершения работы.");
    }
}