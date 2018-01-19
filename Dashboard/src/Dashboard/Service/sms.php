<?php
namespace Dashboard\Service;

class sms{
    public function sendSms($param) {
        $mob=$param['mobile'];
        $sendmsg=$param['message'];
        //cURL resource initialization
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_URL => "http://nimbusit.net/api.php?username=T4bhandarinavin&password=925422&sender=grodrm&sendto=$mob&message=$sendmsg",
                    ));
                    /////send Request and save Response
                    $resp = curl_exec($curl);
                    //////close req. to clear up resources
                    curl_close($curl);
                    return $resp;
    }
}