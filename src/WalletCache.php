<?php

namespace CoonDesign\phpGridcoin;

class WalletCache {
    const REDIS = 1;
    const MEMCACHE = 1 << 1;

    /**
     * @var null|string
     */
    public $host = null;

    /**
     * @var null|string
     */
    public $port = null;

    /**
     * @var null
     */
    public $pass = null;

    protected $enabled = false;

    protected $handle = null;

    protected $storageType = null;

    public function __construct($host = 'localhost', $port = '6379', $pass = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->pass = $pass;

        $this->useRedis();

        if ($this->pass !== null)
            $this->auth();
    }


    public function auth()
    {
        if ($this->storageType === self::REDIS) {
            $this->handle->auth($this->pass);
        }
        return $this;
    }

    public function useMemcache()
    {
        $this->enabled = false;
        $this->storageType = self::MEMCACHE;
        if (!class_exists('\Memcache')) {
            return $this;
        }
        $this->handle = new \Memcache;
        $connected = @$this->handle->connect($this->host, intval($this->port));
        if ($connected) {
            $this->enabled = true;
        }
        return $this;
    }

    public function useRedis()
    {
        $this->enabled = false;
        $this->storageType = self::REDIS;
        $address = sprintf("%s:%s", $this->host, $this->port);
        if (@stream_socket_client($address) === false) {
            return $this;
        }
        $this->handle = new \TinyRedisClient($address);
        $this->enabled = true;
        return $this;
    }

    public function get($uniqueID)
    {
        switch ($this->storageType) {
            case self::MEMCACHE:
            case self::REDIS:
                // Luckily Redis and Мemcache interfaces are the same in this case.
                $data = $this->handle->get($uniqueID);
                
                return $data;
        }
        return null;
    }

    public function set($uniqueID, $data, $ttl = 900) {
        switch ($this->storageType) {
            case self::REDIS:
                $this->handle->set($uniqueID, $data);
                $this->handle->expire($uniqueID, $ttl);
                break;
            case self::MEMCACHE:
                $this->handle->set($uniqueID, $data, 0, $ttl);
                break;
        }
    }

    public function destroy($uniqueID) {
        switch ($this->storageType) {
            case self::REDIS:
                $this->handle->del($uniqueID);
                break;
            case self::MEMCACHE:
                $this->handle->delete($uniqueID);
                break;
        }
    }
    
    public function exists($uniqueID) {
        switch ($this->storageType) {
            case self::REDIS:
                return $this->handle->exists($uniqueID);
            case self::MEMCACHE:
                return $this->handle->get($uniqueID) !== false;
        }
        return false;
    }

}