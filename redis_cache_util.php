<?php
class CacheUtil {
    private static $redis; // Redis 인스턴스를 저장할 정적 변수

    // Redis 서버와 연결을 설정하는 메서드
    public static function connect() {
        if (self::$redis === null) { // 아직 연결이 설정되지 않은 경우
            self::$redis = new Redis();
            self::$redis->connect('127.0.0.1', 6379); // 필요에 따라 호스트와 포트를 변경
        }
    }

    // 주어진 키로 Redis에서 데이터를 가져오는 메서드
    public static function get($key) {
        self::connect(); // 연결을 설정
        $data = self::$redis->get($key); // 데이터를 가져옴
        return $data ? json_decode($data, true) : null; // JSON 형식에서 PHP 배열로 디코딩
    }

    // 주어진 키로 데이터를 Redis에 저장하는 메서드
    public static function set($key, $value, $expiration = 1800) {
        self::connect(); // 연결을 설정
        $data = json_encode($value); // PHP 배열을 JSON 형식으로 인코딩
        self::$redis->setex($key, $expiration, $data); // 데이터를 저장하고 만료 시간 설정
    }
}
?>
