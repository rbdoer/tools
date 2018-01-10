<?php

class Inspire
{

    protected $url = "http://lmsg.jiecaojingxuan.com:443/socket.io/?transport=polling&b64=1&sessionToken=1.950470.481270.GHU.6b39c9c5de9add66cda3f9fd09fae5aa";

    protected $searchUrl = "https://www.baidu.com/s?wd=";

    public function handle()
    {
        $isOpenFile = true;

        $fileHandle = fopen("chongding.txt", "a+");
        $newUrl = $this->url . "&sid=" . $this->getSid();
        echo $newUrl;

        while (true) {
            $response = $this->curlGet($newUrl);

            // $response = '201:42["showQuestion",{"answerTime":10,"desc":"11.以下要进行夏眠的动物是？","displayOrder":10,"liveId":78,"options":"[\"海星\",\"海参\",\"海胆\"]","questionId":884,"showTime":1515323525732,"status":0,"type":"showQuestion"}]';
            // $response = '["showQuestion",{"answerTime":10,"desc":"8.“做人如果没有梦想,跟咸鱼有什么区别” 出自于哪部电影？    ","displayOrder":7,"liveId":84,"options":"[\"功夫足球\",\"少林足球\",\"喜剧之王\"]","questionId":965,"showTime":1515474491690,"status":0,"type":"showQuestion"}]';

            $index = strpos($response, '[');
            $res = substr($response, $index);
            print_r($res . "\n");

            $arr = json_decode($res, true);
            if (isset($arr['code']) && $arr['code'] == 1) {
                $newUrl = $this->url . "&sid=" . $this->getSid();
                continue;
            }

            if(isset($arr[0]) && $arr[0] == "showQuestion"){
                $question = $arr[1]['desc'];
                $options = $arr[1]['options'];
                if(is_string($options)){
                    $options = json_decode($arr[1]['options'],true);
                }
                foreach ($options as $opt) {
                    $search  =  $question .$opt;
                    if($isOpenFile){
                        pclose(popen("open '".$this->searchUrl. $search . "'", "r"));
                    }
                }
                if($isOpenFile){
                    pclose(popen("open '".$this->searchUrl. $question. "'", "r"));
                }

                fwrite($fileHandle, $question . " ". implode(',', $options) ."\n");
            }

        }
    }


    public function getSid()
    {

        $response = $this->curlGet($this->url);
        $index = strpos($response, '{');
        $res = substr($response, $index);
        print_r($response . "\n");

        try {
            $sid = json_decode($res, true);
            return $sid['sid'];
        } catch (\Exception $e) {
            return '';
        }
    }

    public function curlGet($url){
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);

        if($data){
            $data = explode("\n", $data);
            return $data[count($data)-1];
        }
        return $data;
    }
}

(new Inspire())->handle();
