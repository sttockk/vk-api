<?php

namespace MyProject\Library;

class Vk
{
    private string $v = "5.131";
    private string $groupId;
    private string $token;
    private $membersId = [];

    public function __construct(string $token, string $groupId)
    {
        $this->token = $token;
        $this->groupId = $groupId;
    }

    private function apiRequest($method, $data = [])
    {
        $data['v'] = $this->v;
        $data['access_token'] = $this->token;

        $url = 'https://api.vk.com/method/' . $method;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response, true);

        return $response;
    }

    public function countMembers()
    {
        $data = [
            'group_id' => $this->groupId,
        ];

        $members = $this->apiRequest('groups.getMembers', $data);

        return $members["response"]["count"] ?? NULL;
    }

    public function getMembersId()
    {
        $offset = 0;
        $count = 1000;
        $request = 25000;

        do {

            $data = [
                'code' => 'var members = []; var offset =' . $offset . '; var count =' . $count . '; while (offset <' . $request . ') { var response = API.groups.getMembers({"group_id": '.$this->groupId.', "count": count, "offset": offset}); members = members + response.items; offset = offset + count; } return members;',
            ];

            $response = $this->apiRequest('execute', $data);

            if (isset($response["execute_errors"]) || isset($response["error"])) {
                throw new \Exception('Execute error');
            }

            if (is_null($response)) {
                break;
            }

            $this->membersId = array_merge($this->membersId, $response["response"]);

            $responseСount = $this->countMembers();

            $request += 25000;

            $offset = count($this->membersId);

        } while($offset < $responseСount);

        return $this->membersId;
    }
}