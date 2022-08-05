<?php

namespace App\Http\Controllers;

use App\Models\ContextualClick;
use App\Models\ContextualImpression;
use App\Models\GlobalClick;
use App\Models\GlobalImpression;
use App\Models\GlobalSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
/**
 * @OA\Info(
 *    title="FM Display PHP/JSON Rest API",
 *    description="FM Display PHP/JSON Rest API Endpoint Specification",
 *    version="1.0.0",
 * )
 */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function getRandclickKey()
    {
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $str = '';
        $length = strlen($charset);
        $count = 9;
        while ($count--) {
            $str .= $charset[mt_rand(0, $length - 1)];
        }

        if (Cache::has('getRandclickKey')){
            $key = Cache::get('getRandclickKey');
            $str = substr($str,0,-(strlen($key)));
            return $str.$key;
        }

        $data = ContextualClick::where("clickKey", $str)->get();
        if (count($data) == 0) {
            return $str;
        } else {
            $this->getRandclickKey();
        }
    }
    public function getRandImpressionKey()
    {
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $str = '';
        $length = strlen($charset);
        $count = 9;
        while ($count--) {
            $str .= $charset[mt_rand(0, $length - 1)];
        }

        if (Cache::has('getRandImpressionKey')){
            $key = Cache::get('getRandImpressionKey');
            $str = substr($str,0,-(strlen($key)));
            return $str.$key;
        }

        $data = ContextualImpression::where("impressionKey", $str)->get();
        if (count($data) == 0) {
            return $str;
        } else {
            $this->getRandImpressionKey();
        }
    }
    public function getRandGlobalclickKey()
    {
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $str = '';
        $length = strlen($charset);
        $count = 9;
        while ($count--) {
            $str .= $charset[mt_rand(0, $length - 1)];
        }

        if (Cache::has('getRandGlobalclickKey')){
            $key = Cache::get('getRandGlobalclickKey');
            $str = substr($str,0,-(strlen($key)));
            return $str.$key;
        }

        $data = GlobalClick::where("clickKey", $str)->get();
        if (count($data) == 0) {
            return $str;
        } else {
            $this->getRandGlobalclickKey();
        }
    }
    public function getRandGlobalimpressionKey()
    {
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $str = '';
        $length = strlen($charset);
        $count = 9;
        while ($count--) {
            $str .= $charset[mt_rand(0, $length - 1)];
        }

        if (Cache::has('getRandGlobalimpressionKey')){
            $key = Cache::get('getRandGlobalimpressionKey');
            $str = substr($str,0,-(strlen($key)));
            return $str.$key;
        }


        $data = GlobalImpression::where("impressionsKey", $str)->get();
        if (count($data) == 0) {
            return $str;
        } else {
            $this->getRandGlobalimpressionKey();
        }
    }
    public function getRandGlobalSessionKey()
    {
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $str = '';
        $length = strlen($charset);
        $count = 9;
        while ($count--) {
            $str .= $charset[mt_rand(0, $length - 1)];
        }

        if (Cache::has('getRandGlobalSessionKey')){
            $key = Cache::get('getRandGlobalSessionKey');
            $str = substr($str,0,-(strlen($key)));
            return $str.$key;
        }


        $data = GlobalSession::where("sessionKey", $str)->get();
        if (count($data) == 0) {
            return $str;
        } else {
            $this->getRandGlobalSessionKey();
        }
    }
    public function getRandGlobalUserKey()
    {
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $str = '';
        $length = strlen($charset);
        $count = 9;
        while ($count--) {
            $str .= $charset[mt_rand(0, $length - 1)];
        }

        if (Cache::has('getRandGlobalUserKey')){
            $key = Cache::get('getRandGlobalUserKey');
            $str = substr($str,0,-(strlen($key)));
            return $str.$key;
        }


        $data = GlobalSession::where("userKey", $str)->get();
        if (count($data) == 0) {
            return $str;
        } else {
            $this->getRandGlobalUserKey();
        }
    }

}
