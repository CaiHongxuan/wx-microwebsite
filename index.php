<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "hongxuan");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            $toUsername   = $postObj->FromUserName;
            $fromUsername = $postObj->ToUserName;
            $time         = time();
            $MsgType      = 'text';

            if( strtolower($postObj->MsgType) == 'event' ){
                // 如果是关注subscribe事件
                if( strtolower($postObj->Event) == 'subscribe' ){
                    $Content      = '欢迎关注我们的公众号';
                    $template     = "<xml>
                                    <ToUserName><![CDATA[%s]]></ToUserName>
                                    <FromUserName><![CDATA[%s]]></FromUserName>
                                    <CreateTime>%s</CreateTime>
                                    <MsgType><![CDATA[%s]]></MsgType>
                                    <Content><![CDATA[%s]]></Content>
                                    </xml>";
                    $info = sprintf($template, $toUsername, $fromUsername, $time, $MsgType, $Content);
                    echo $info;
                }
            }else{
                $keyword  = trim($postObj->Content);
                $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
                if(!empty( $keyword ))
                {
                    $contentStr = "回复消息";
                    $resultStr = sprintf($template, $toUsername, $fromUsername, $time, $MsgType, $contentStr);
                    echo $resultStr;
                }else{
                    echo "Input something...";
                }
            }
        }else {
            echo "";
            exit;
        }

    }

	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
