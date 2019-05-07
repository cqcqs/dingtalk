<?php

namespace DingTalk;

include '../TopSdk.php';

class DingTalk {

    private $access_token;

    //获取access_token
    private $get_access_token_url = "https://oapi.dingtalk.com/gettoken";

    //批量获取审批实例id
    private $get_process_instance_list_url = "https://oapi.dingtalk.com/topapi/processinstance/listids";

    //获取单个审批实例
    private $get_process_instance_url = "https://oapi.dingtalk.com/topapi/processinstance/get";

	public function __construct($appKey, $appSecret)
	{
        $client = new \DingTalkClient(\DingTalkConstant::$CALL_TYPE_OAPI, \DingTalkConstant::$METHOD_GET, \DingTalkConstant::$FORMAT_JSON);
        $request = new \OapiGettokenRequest();
        $request->setAppkey($appKey);
        $request->setAppsecret($appSecret);
        $response = $client->execute($request, null, $this->get_access_token_url);
        $this->access_token = $response->access_token;
	}

    /**
     * 获取所有审批实例ID
     * @param string $processCode
     * @param int|null $startTime
     * @param int $endTime
     * @return array
     * @throws \Exception
     */
    public function getProcessInstanceList($processCode = "", $startTime = 0, $endTime = 0): array
    {
        if(!$processCode){
            throw new \Exception('process_code is null');
        }
        if(!$startTime){
            $startTime = strtotime(date('Y-m-d'));
        }

        $client = new \DingTalkClient(\DingTalkConstant::$CALL_TYPE_OAPI, \DingTalkConstant::$METHOD_POST, \DingTalkConstant::$FORMAT_JSON);
        $request = new \OapiProcessinstanceListidsRequest();
        $request->setProcessCode($processCode);
        $request->setStartTime($startTime);
        if($endTime) {
            $request->setEndTime($endTime);
        }
        $response = $client->execute($request, $this->access_token, $this->get_process_instance_list_url);
        return $response->result['list'];
    }

    /**
     * 获取单个审批实例
     * @param string $instanceId
     * @return mixed
     */
    public function getProcessInstance(string $instanceId)
    {
        $client = new \DingTalkClient(\DingTalkConstant::$CALL_TYPE_OAPI, \DingTalkConstant::$METHOD_POST, \DingTalkConstant::$FORMAT_JSON);
        $request = new \OapiProcessinstanceGetRequest();
        $request->setProcessInstanceId($instanceId);
        $response = $client->execute($request, $this->access_token, $this->get_process_instance_url);
        return $response->process_instance;
    }
}