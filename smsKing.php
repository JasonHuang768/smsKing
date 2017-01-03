<?php
/**
 *	This file is part of sms.(簡訊王)
 *  https://www.kotsms.com.tw/
 * @author JasonHuang <>
 *
 * @package sms
 * @since sms Ver 1.0
**/

/**
 * [Response DataType]
 * @param [type] kmsgid, dstaddr, dlvtime, donetime, statusstr, DATE [description]
 */

namespace sms;

class smsKing{

    #未加密
    protected $singleEndpoint = "http://202.39.48.216/kotsmsapi-1.php"; #即時API發送速度: 666秒/1,000通
    protected $mutiEndpoint   = "http://202.39.48.216/kotsmsapi-2.php"; #大量API發送速度: 60秒/2,666通

    #加密
    protected $ensingleEndpoint = "https://api.kotsms.com.tw/kotsmsapi-1.php"; #即時API發送速度: 666秒/1,000通
    protected $enmutiEndpoint   = "https://api.kotsms.com.tw/kotsmsapi-2.php"; #大量API發送速度: 60秒/2,666通
    
    public function __construct($parametersArr = array(), $Mode = "single", $encryption = "false"){

        foreach ($parametersArr as $k => $v) {
            if ($v == null){
                throw new Exception($v.' are not set.');
            }
        }

        $this->parameters = array(
            "username" => $parametersArr['userName'],
            "password" => $parametersArr['passWord'],
            "dstaddr"  => "",
            "smbody"   => "",
            "response" => $parametersArr['response'],
        );

        $this->Mode     = $Mode;
        $this->enStatus = $encryption;
    }

    protected function setEndPoint(){
        switch ($this->Mode) {
            case 'single':
                if ($this->enStatus){
                    return $this->ensingleEndpoint;
                }else{
                    return $this->singleEndpoint;
                }
                break;
            case 'muti':
                if ($this->enStatus){
                    return $this->enmutiEndpoint;
                }else{
                    return $this->mutiEndpoint;
                }
                break;
        }
    }

    public function checkOut($params = array()){

        if ($params['smbody'] == null){
            throw new Exception('Content are not set.');
        }

        if (!preg_match("/^[0-9]{10}$/", $params['dstaddr'])){
            throw new Exception('Dstaddr are not set.');
        }

        $params = array_merge($this->parameters, $params);

        $params['smbody'] = iconv("UTF-8", "big5//TRANSLIT", $params['smbody']);

        $postString = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->setEndPoint()."?".$postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;

    }

    public function checkMsg($msgId){
    	switch ($msgId) {
    		case '-1':
    			$txt = "CGI string error，系統維護中或其他錯誤 ,帶入的參數異常,伺服器異常";
    			break;
    		case '-2':
    			$txt = "授權錯誤(帳號/密碼錯誤)";
    			break;
    		case '-4':
    			$txt = "A Number違反規則 發送端870短碼VCSN 設定異常";
    			break;
    		case '-5':
    			$txt = "B Number違反規則 接收端 門號錯誤";
    			break;
    		case '-6':
    			$txt = "Closed User 接收端的門號停話異常090 094 099 付費代號等";
    			break;
    		case '-20':
    			$txt = "Schedule Time錯誤 預約時間錯誤 或時間已過";
    			break;
    		case '-21':
    			$txt = "Valid Time錯誤 有效時間錯誤";
    			break;
    		case '-59999':
    			$txt = "帳務系統異常 簡訊無法扣款送出";
    			break;
    		case '-60002':
    			$txt = "您帳戶中的點數不足";
    			break;
    		case '-60014':
    			$txt = "該用戶已申請 拒收簡訊平台之簡訊 ( 2010 NCC新規)";
    			break;
    		case '-999959999':
    			$txt = "在12 小時內，相同容錯機制碼";
    			break;
    		case '-999969999':
    			$txt = "同秒, 同門號, 同內容簡訊";
    			break;
    		case '-999979999':
    			$txt = "鎖定來源IP";
    			break;
    		case '-999989999':
    			$txt = "簡訊為空";
    			break;

    		return $txt;
    	}
    }
}

?>