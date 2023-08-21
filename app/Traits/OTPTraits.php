<?php
namespace App\Traits;
trait OTPTraits{

    public function sendSMS($number, $message)
    {
        // try {
        //     $url="http://66.45.237.70/api.php";
        //     $data=array(
        //         'username' =>"01322644599",
        //         'password' =>"4NBHSC3G",
        //         'number' =>$number,
        //         'message' =>$message
        //     );
        //     $ch=curl_init();
        //     curl_setopt($ch, CURLOPT_URL,$url);
        //     curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        //     $smsresult=curl_exec($ch);
        //     $p=explode("|",$smsresult);
        //     $sendstatus=$p[0];
        //     return true;
        // } catch (\Exception $exception) {
        //     return $exception->getMessage();
        // }
        try {
            $url="https://smsplus.sslwireless.com/api/v3/send-sms";
            $params = [
                "api_token" => "ub0xwa5y-dhzk4vfn-mrafjuto-jnq0lgkz-ehmi4uzr",
                "sid" => "RANGSBRANDAPI",
                "msisdn" => $number,
                "sms" => $message,
                "csms_id" => "123456789"
            ];
            $params = json_encode($params);
            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params),
                'accept:application/json'
            ));
        
            $response = curl_exec($ch);
        
            curl_close($ch);
        
            return $response;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}