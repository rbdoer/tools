<?php

for ($i=0; $i < 1000000; $i++) { 
    $str = guid();
    echo $str;echo "\n";
    file_put_contents("guid.log", $str."\n", FILE_APPEND);
}

/**
 * GUID生成算法
 *
 * 前10位为时间戳，精确到0.001s
 * 11-14位为机器名称（也可用ip）和 当前进程号做crc16编码
 * 15-17位为随机数
 * 18-20位为计数器
 *
 * @author luorenbin
 */
function guid(){
    
    static $cal = 0;
    //计数器+1
    $cal++;
    $cal >= 4096 ? $cal = 1 :'';
     
    //毫秒保证时间的唯一
    $time_dec = 1259510400000;
    $micro = microtime(true)*1000;
    $micro = $micro - $time_dec;

    $time = substr($micro,0,13);
    $time_str = dechex($time);

    //获取机器名称
    $dec1 = crc16(gethostname());
    //获取进程号
    $dec2 = posix_getpid();
    $proc_str = dechex($dec1+$dec2);
    $proc_str = substr($proc_str, -4);
    //生成随机数2位
    $rand = dechex(mt_rand(0,4095));
    //计数器3位
    $cal_str = dechex($cal);

    return sprintf('%010s%04s%03s%03s',$time_str,$proc_str,$rand,$cal_str);

}

/**
 * crc16算法
 * @param string $string
 * @author luorenbin
 */
function crc16($string) {
    $crc = 0xFFFF;
    for ($x = 0; $x < strlen ($string); $x++) {
        $crc = $crc ^ ord($string[$x]);
        for ($y = 0; $y < 8; $y++) {
            if (($crc & 0x0001) == 0x0001) {
                $crc = (($crc >> 1) ^ 0xA001);
            } else { $crc = $crc >> 1; }
        }
    }
    return $crc;
}
