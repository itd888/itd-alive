<?php

namespace itd;

use Redis;

class Alive
{
    private static $instance;
    private $handler;

    public function __construct() {
        if (empty($this->handler)) {
            $host = iEnv("REDIS_A.HOST");
            $port = iEnv("REDIS_A.PORT");
            $pass = iEnv("REDIS_A.PASS");
            $expire = iEnv("REDIS_A.EXPIRE");
            $options = array(
                'host' => $host ?: '127.0.0.1',
                'port' => $port ?: 6379,
                'password' => $pass ?: '',
                'expire' => $expire
            );

            $this->handler = new Redis($options);
            $this->handler->connect($options ['host'], $options ['port']);
            if (isset($options['password'])) {
                $this->handler->auth($options ['password']);
            }
        }
    }

    private static function getIns(): Alive {
        if (self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function getHandle(): Redis {
        return $this->handler;
    }

    /** @throws */
    public static function setAlive($key) {
        self::getIns()->getHandle()->set(self::parseKey($key), date('Y-m-d H:i:s'));
    }

    /** @throws */
    public static function getAlive($key) {
        return self::getIns()->getHandle()->get(self::parseKey($key));
    }

    private static function parseKey($key) {
        return 'AL_' . $key;
    }

}