<?php

namespace itd;


class Alive
{
    private static $instance;
    private $handler;

    private const hashKey = 'ALIVE';

    public function __construct() {
        if (empty($this->handler)) {
            $host = iEnv("REDIS_A.HOST");
            $port = iEnv("REDIS_A.PORT");
            $pass = iEnv("REDIS_A.PASS");
            //$expire = iEnv("REDIS_A.EXPIRE"); 使用setex 参考tp Redis的重写
            $options = array(
                'host' => $host ?: '127.0.0.1',
                'port' => $port ?: 6379,
                'password' => $pass ?: '',
            );

            $this->handler = new \Redis;
            $this->handler->connect($options ['host'], $options ['port']);
            if (isset($options['password'])) {
                $this->handler->auth($options ['password']);
            }
        }
    }

    private static function getIns(): Alive {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function getHandle(): \Redis {
        return $this->handler;
    }

    /** hash永久保存 @throws */
    public static function hsetAlive($key) {
        self::getIns()->getHandle()->hset(self::getProjectHash(), $key, time());
    }

    /** @throws */
    public static function hgetAlive($key) {
        return self::getIns()->getHandle()->hget(self::getProjectHash(), $key);
    }

    public static function hcheckAlive($key, $limitTime) {
        return (time() - self::hGetAlive($key)) <= $limitTime;
    }

    /**
     * @throws
     */
    public static function hgetMultiAlive($keys) {
        return self::getIns()->getHandle()->hMGet(self::getProjectHash(), $keys);
    }

    /** 默认最多保存一天  @throws */
    public static function setAlive($key, $expire = 86400) {
        $key = self::getProjectKey($key);
        if ($expire > 0) {
            self::getIns()->getHandle()->setex($key, $expire, time());
        } else {
            self::getIns()->getHandle()->set($key, time());
        }
    }

    /** @throws */
    public static function getAlive($key) {
        $key = self::getProjectKey($key);
        return self::getIns()->getHandle()->get($key);
    }

    public static function checkAlive($key, $limitTime) {
        return (time() - self::getAlive($key)) <= $limitTime;
    }

    public static function setMoment($key, $momentTimeStamp, $expire = 86400) {
        $key = self::getProjectKey($key);
        return self::getIns()->getHandle()->setex($key, $expire, $momentTimeStamp);
    }

    /** @throws */
    public static function getMoment($key) {
        $key = self::getProjectKey($key);
        return self::getIns()->getHandle()->get($key);
    }

    /**
     * @throws
     */
    public static function getMultiAlive($keys) {
        $newKeys = [];
        foreach ($keys as $key) {
            $newKeys[] = self::getProjectKey($key);
        }
        return self::getIns()->getHandle()->mGet($newKeys);
    }

    private static function getProjectKey($key) {
        return self::getProjectName() . '_' . $key;
    }

    private static function getProjectHash() {
        return self::hashKey . '_' . self::getProjectName();
    }


    private static function getProjectName() {
        $arr = explode(DIRECTORY_SEPARATOR, __DIR__);
        $flag = false;
        foreach ($arr as $dir) {
            if ($flag) {
                return $dir;
            }
            if ($dir == 'www') {
                $flag = true;
            }
        }
    }
}