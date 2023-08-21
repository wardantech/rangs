<?php

namespace App\Http\Controllers\SMS;

use App\Traits\OTPTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OtpController extends Controller
{
    use OTPTrait;

    public function sendOTP(Request $request)
    {
        //--- Validation Section
        $rules = [
            'mobile' => 'required|string|unique:users',
            'otp_code' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends

        try {
            $otp = $request->otp_code;

            $text = "Thank you for signing up at sheanch-home. Your OTP is: " . $otp;
            $sms = $this->sendSms($request->mobile, $text);

            // if ($sms == true) {
            //     return $this->sendResponse('success', 'OTP send successfully');
            // } else {
            //     return $this->sendError('OTP Send error', ['error' => $sms]);
            // }

        } catch (\Exception $exception) {
            return $this->sendError('OTP Send error', ['error' => $exception->getMessage()]);
        }

    }
}
