<?php
class GroupLogCache {
    private static $instance;
    private $redis;

    private function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function cacheMemberLogs($mt_idx, $sgdt_idx, $event_start_date, $logs) {
        $cacheKey = "member_logs_{$mt_idx}_{$sgdt_idx}_{$event_start_date}";
        $this->redis->setex($cacheKey, 3600, json_encode($logs)); // 1시간 동안 캐시
    }

    public function getMemberLogs($mt_idx, $sgdt_idx, $event_start_date) {
        $cacheKey = "member_logs_{$mt_idx}_{$sgdt_idx}_{$event_start_date}";
        $cachedLogs = $this->redis->get($cacheKey);
        return $cachedLogs !== false ? json_decode($cachedLogs, true) : null;
    }
}