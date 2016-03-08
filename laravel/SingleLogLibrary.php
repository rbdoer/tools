<?php
/**
 * 自定义日志类
 *
 * 实现功能（区别于系统日志）：
 * 1、自定义日志文件
 * 2、记录文件，行号
 * 3、只重写了error warning notice info debug五个方法
 *
 * 用法example
 * $slog = new  SingleLogLibrary('logfilename');
 * $slog->error('test string');
 *
 * @author luorenbin
 * @version  2016-03-08
 */
namespace App\Libs;

use Illuminate\Log\Writer;
use Monolog\Logger as MonologLogger;

class SingleLogLibrary extends Writer
{
    //file
    private $currentFile = NULL;
    //line
    private $currentLine = NULL;
    //trace depth
    const TRACE_DEPTH = 2;
    //default format
    const DEFAULT_LOG_FORMAT = '[url:%s][file:%s:%d][msg:%s]';

    /**
     * construct function
     * @param string $logfile filename
     */
    public function __construct($logfile){

        if(empty($logfile)){
            throw new \Exception("you must set log file name!", 1);
        }

        parent::__construct(new MonologLogger('Slog'));

        $this->useDailyFiles(storage_path('logs').'/'.$logfile);
    }


    /**
     * Write a message to Monolog.
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    protected function writeLog($level, $message, $context){

        $this->getFileInfo(self::TRACE_DEPTH);

        $formatMessage = vsprintf(self::DEFAULT_LOG_FORMAT, array($this->getUrl(), $this->currentFile, $this->currentLine, $this->formatMessage($message)));
        $this->fireLogEvent($level, $formatMessage, $context);

        $this->monolog->{$level}($formatMessage, $context);
    }


    /**
     * get file and line number
     * @param  integer $depth 
     * @return void         
     */
    private function getFileInfo($depth = 0){

        $trace = debug_backtrace();
        $depth2 = $depth + 1;

        if ($depth >= count($trace)){
            $depth = count($trace) - 1;
            $depth2 = $depth;
        }

        $this->currentFile = isset($trace[$depth]['file']) ? $trace[$depth]['file'] : "" ;
        $this->currentLine = isset($trace[$depth]['line']) ? $trace[$depth]['line'] : "" ;
    }


    /**
     * get request url
     */
    public static function getUrl(){

        if (defined('YN_URL')){
           return YN_URL;
        }
            
        if(isset($_SERVER['REQUEST_URI'])){
            define('YN_URL',$_SERVER['REQUEST_URI']);
        }else{
            define('YN_URL','Unknown');
        }

        return YN_URL;
    }


}
