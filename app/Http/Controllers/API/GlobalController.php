<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContextualAdvert;
use App\Models\ContextualClick;
use App\Models\ContextualImpression;
use App\Models\GlobalAvert;
use App\Models\GlobalClick;
use App\Models\GlobalImpression;
use App\Models\GlobalPublisher;
use App\Models\GlobalSession;
use App\Models\GlobalSystemStatus;
use App\Models\GlobalUser;
//use Illuminate\Filesystem\Cache;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
class GlobalController extends Controller
{
/**
 * @OA\Post(
 *  path="/hlola/global/isipho",
 *  summary="global isipho",
 *  description="Create a new global_clicks record",
 *  operationId="global_isipho",
 *  tags={"API Reference"},
 *  @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"publisherKey","sessionKey","advertKey","impressionKey"},
 *       @OA\Property(property="publisherKey", type="string",  example="HD3kd92JA"),
 *       @OA\Property(property="sessionKey", type="string",  example="AJD18Sgh1"),
 *       @OA\Property(property="advertKey", type="string", example="wCkbp9XHF"),
 *       @OA\Property(property="impressionKey", type="string", example="sQ67rfADk"),
 *    ),
 *  ),
 *
 *  @OA\Response(
 *    response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Success"),
 *       @OA\Property(property="clickKey", type="string", example="I9gJM3eDg"),
 *       @OA\Property(property="dateCreated", type="string", example="2022-07-11 05:15:29"),
 *     )
 *   ),
 *
 *  @OA\Response(
 *    response=400,
 *    description="Failed",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Failed"),
 *     )
 *   )
 *
 * )
 */
    public function global_isipho(Request $request){

        $global_status= Cache::remember('global_systemStatusId',24*60*60,function (){
            return GlobalSystemStatus::where("global_systemStatusId", 1)->first();
        });

        $publisher= Cache::remember('publisherKey_'.$request->publisherKey,24*60*60,function() use ($request){
            return $publisher = GlobalPublisher::where("publisherKey", $request->publisherKey)->first();
        });



        $validation = Validator::make($request->all(),[
            'publisherKey' => 'required',
            'sessionKey' => 'required',
            'advertKey' => 'required',
            'impressionKey' => 'required',
        ]);
        if($validation->fails()){
            $data["status"] = "Failed";
        }else{
            if(!empty($global_status) && !empty($publisher) && $global_status->global_systemStatusValue == "ACTIVE" &&  $publisher->publisherStatus == "ACTIVE"){
                $globalclick = new GlobalClick();
                $globalclick->clickKey = $this->getRandGlobalclickKey();
                $globalclick->sessionKey = $request->sessionKey;
                $globalclick->advertKey = $request->advertKey;
                $globalclick->impressionKey = $request->impressionKey;

                if($globalclick->save()){
                    Cache::put('getRandGlobalclickKey',$globalclick->clickId);
                    GlobalSession::where("sessionKey", $request->sessionKey)->update(["sessionClick"=>'TRUE']);
                    $data["status"] = "Success";
                    $data["clickKey"] = $globalclick->clickKey;
                    $data["dateCreated"] = date("Y-m-d H:i:s");
                }else{
                    $data["status"] = "Failed";
                }
            }else{
                $data["status"] = "Failed";
            }
        }

        return response()->json($data, 200);
    }
/**
 * @OA\Post(
 *  path="/hlola/global/mamatheka",
 *  summary="global mamatheka",
 *  description="Create a new global_impressions record",
 *  operationId="global_mamatheka",
 *  tags={"API Reference"},
 *  @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"publisherKey","sessionKey","campaignKey","advertKey","impressionsUrl"},
 *       @OA\Property(property="publisherKey", type="string",  example="HD3kd92JA"),
 *       @OA\Property(property="sessionKey", type="string",  example="AJD18Sgh1"),
 *       @OA\Property(property="campaignKey", type="string", example="22L2CyxmQ"),
 *       @OA\Property(property="advertKey", type="string", example="3ZBpZtHqp"),
 *       @OA\Property(property="impressionsUrl", type="string", example="exampleimpressionurl.com"),
 *       @OA\Property(property="impressionsBackfill", type="string", example="FALSE"),
 *       @OA\Property(property="backfillKey", type="string", example=""),
 *    ),
 *  ),
 *
 *  @OA\Response(
 *    response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Success"),
 *       @OA\Property(property="impressionKey", type="string", example="I9gJM3eDg"),
 *       @OA\Property(property="dateCreated", type="string", example="2022-07-11 05:15:29"),
 *     )
 *   ),
 *
 *  @OA\Response(
 *    response=400,
 *    description="Failed",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Failed"),
 *     )
 *   )
 *
 * )
 */
    public function global_mamatheka(Request $request){
        $global_status= Cache::remember('global_systemStatusId',24*60*60,function (){
            return GlobalSystemStatus::where("global_systemStatusId", 1)->first();
        });

        $publisher= Cache::remember('publisherKey_'.$request->publisherKey,24*60*60,function() use ($request){
            return $publisher = GlobalPublisher::where("publisherKey", $request->publisherKey)->first();
        });


        $validation = Validator::make($request->all(),[
            'publisherKey' => 'required',
            'sessionKey' => 'required',
            'campaignKey' => 'required',
            'advertKey' => 'required',
            'impressionsUrl' => 'required',
        ]);
        if($validation->fails()){
            $data["status"] = "Failed";
        }else{
            if(!empty($global_status) && !empty($publisher) && $global_status->global_systemStatusValue == "ACTIVE" &&  $publisher->publisherStatus == "ACTIVE"){
                $impression = new GlobalImpression();
                $impression->impressionsKey = $this->getRandGlobalimpressionKey();
                $impression->advertKey = $request->advertKey;
                $impression->campaignKey = $request->campaignKey;
                $impression->sessionKey = $request->sessionKey;
                $impression->impressionsUrl = $request->impressionsUrl;
                $impression->impressionsBackfill = $request->impressionsBackfill;
                $impression->backfillKey = $request->backfillKey;

                if($impression->save()){
                    Cache::put('getRandGlobalimpressionKey',$impression->impressionsId);
                    $global_avert = GlobalAvert::where("advertKey", $request->advertKey)->first();
                    if($global_avert){
                        $global_avert->advertImpressionCount = $global_avert->advertImpressionCount +1;
                        $global_avert->save();
                    }
                    $data["status"] = "Success";
                    $data["impressionKey"] = $impression->impressionsKey;
                    $data["dateCreated"] =date("Y-m-d H:i:s");
                }else{
                    $data["status"] = "Failed";
                }
            }else{
                $data["status"] = "Failed";
            }
        }

        return response()->json($data, 200);
    }
/**
 * @OA\Post(
 *  path="/hlola/global/ubambo",
 *  summary="global ubambo",
 *  description="Create a new global_sessions record",
 *  operationId="global_ubambo",
 *  tags={"API Reference"},
 *  @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"publisherKey","sessionIp","sessionOs","sessionBrowser","sessionCountry","userInboundUrl","userLandingUrl"},
 *       @OA\Property(property="publisherKey", type="string",  example="HD3kd92JA"),
 *       @OA\Property(property="sessionIp", type="string",  example="111.111.111"),
 *       @OA\Property(property="sessionOs", type="string", example="ANDROID"),
 *       @OA\Property(property="sessionBrowser", type="string", example="CHROME"),
 *       @OA\Property(property="sessionCountry", type="string", example="USA"),
 *       @OA\Property(property="userInboundUrl", type="string", example="https://inbound.com"),
 *       @OA\Property(property="userLandingUrl", type="string", example="https://landing.com"),
 *    ),
 *  ),
 *
 *  @OA\Response(
 *    response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Success"),
 *       @OA\Property(property="sessionKey", type="string", example="I9gJM3eDg"),
 *       @OA\Property(property="userKey", type="string", example="xngY1vGCF"),
 *       @OA\Property(property="dateCreated", type="string", example="2022-07-11 05:15:29"),
 *     )
 *   ),
 *
 *  @OA\Response(
 *    response=400,
 *    description="Failed",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Failed"),
 *     )
 *   )
 *
 * )
 */
    public function global_ubambo(Request $request){

        $global_status= Cache::remember('global_systemStatusId',24*60*60,function (){
            return GlobalSystemStatus::where("global_systemStatusId", 1)->first();
        });

        $publisher= Cache::remember('publisherKey_'.$request->publisherKey,24*60*60,function() use ($request){
            return $publisher = GlobalPublisher::where("publisherKey", $request->publisherKey)->first();
        });


        $validation = Validator::make($request->all(),[
            'publisherKey' => 'required',
            'sessionIp' => 'required',
            'sessionOs' => 'required',
            'sessionBrowser' => 'required',
            'sessionCountry' => 'required',
            'userInboundUrl' => 'required',
            'userLandingUrl' => 'required',
        ]);
        if($validation->fails()){
            $data["status"] = "Failed";
        }else{
            if(!empty($global_status) && !empty($publisher) && $global_status->global_systemStatusValue == "ACTIVE" &&  $publisher->publisherStatus == "ACTIVE"){
                $global_session = new GlobalSession();
                $global_session->sessionKey = $this->getRandGlobalSessionKey();
                $global_session->userKey = $this->getRandGlobalUserKey();
                $global_session->sessionIp = $request->sessionIp;
                $global_session->sessionOs = $request->sessionOs;
                $global_session->sessionBrowser = $request->sessionBrowser;
                $global_session->sessionCountry = $request->sessionCountry;

                $global_user = new GlobalUser();
                $global_user->userKey = $global_session->userKey;
                $global_user->userIp = $request->sessionIp;
                $global_user->userDeviceType = $request->sessionBrowser;
                $global_user->userOs = $request->sessionOs;
                $global_user->userInboundUrl = $request->userInboundUrl;
                $global_user->userLandingUrl = $request->userLandingUrl;
                $global_user->userCountry = $request->sessionCountry;

                if($global_session->save() && $global_user->save()){

                    Cache::put('getRandGlobalSessionKey',$global_session->sessionId);
                    Cache::put('getRandGlobalUserKey',$global_user->userId);
                    $data["status"] = "Success";
                    $data["sessionKey"] = $global_session->sessionKey;
                    $data["userKey"] = $global_session->userKey;
                    $data["dateCreated"] = date("Y-m-d H:i:s");
                }else{
                    $data["status"] = "Failed";
                }
            }else{
                $data["status"] = "Failed";
            }
        }

        return response()->json($data, 200);
    }
/**
 * @OA\Post(
 *  path="/hlola/global/ubambo/check",
 *  summary="global ubambo check",
 *  description="Output data from global_sessions db table",
 *  operationId="global_ubambo_check",
 *  tags={"API Reference"},
 *  @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"publisherKey","sessionKey"},
 *       @OA\Property(property="publisherKey", type="string",  example="HD3kd92JA"),
 *       @OA\Property(property="sessionKey", type="string",  example="YaQb39PQj"),
 *    ),
 *  ),
 *
 *  @OA\Response(
 *    response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Success"),
 *       @OA\Property(property="sessionKey", type="string", example="I9gJM3eDg"),
 *       @OA\Property(property="userKey", type="string", example="xngY1vGCF"),
 *       @OA\Property(property="dateCreated", type="string", example="2022-07-11 05:15:29"),
 *     )
 *   )
 * )
 */
    public function global_ubambo_check(Request $request){

        $global_status= Cache::remember('global_systemStatusId',24*60*60,function (){
            return GlobalSystemStatus::where("global_systemStatusId", 1)->first();
        });

        $publisher= Cache::remember('publisherKey_'.$request->publisherKey,24*60*60,function() use ($request){
            return $publisher = GlobalPublisher::where("publisherKey", $request->publisherKey)->first();
        });

        $validation = Validator::make($request->all(),[
            'publisherKey' => 'required',
            'sessionKey' => 'required',
        ]);
        if($validation->fails()){
            $data["status"] = "Failed";
        }else{
            if(!empty($global_status) && !empty($publisher) && $global_status->global_systemStatusValue == "ACTIVE" &&  $publisher->publisherStatus == "ACTIVE"){

                $session= Cache::remember('sessionKey_'.$request->sessionKey,24*60*60,function() use ($request){
                    return $session = GlobalSession::where("sessionKey", $request->sessionKey)->first();
                });

                if($session){
                     $data["status"] = "Success";
                     $data["sessionKey"] = $session->sessionKey;
                     $data["userKey"] = $session->userKey;
                     $data["dateCreated"] = date("Y-m-d H:i:s");
                }else{
                 $data["status"] = "Failed";
                }
             }else{
                 $data["status"] = "Failed";
             }
        }

        return response()->json($data, 200);
    }
/**
 * @OA\Post(
 *  path="/hlola/global/funa",
 *  summary="global funa",
 *  description="Output data from global_adverts db table",
 *  operationId="global_funa",
 *  tags={"API Reference"},
 *  @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"publisherKey"},
 *       @OA\Property(property="publisherKey", type="string",  example="HD3kd92JA"),
 *    ),
 *  ),
 *
 *  @OA\Response(
 *    response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Success"),
 *       @OA\Property(property="publisherKey", type="string", example="I9gJM3eDg"),
 *       @OA\Property(property="globalDisplay", type="array",
 *              example={{
 *                  "advertPosition": "1",
 *                  "advertKey": "jbc7eFDdR",
 *                  "advertImagePath": "https://picsum.photos/536/354",
 *                  "advertUrl": "https://example.com",
 *                }, {
 *                  "advertPosition": "2",
 *                  "advertKey": "wCkbp9XHF",
 *                  "advertImagePath": "https://picsum.photos/536/354",
 *                  "advertUrl": "https://example.com",
 *                }, {
 *                  "advertPosition": "3",
 *                  "advertKey": "qYv4E9SDf",
 *                  "advertImagePath": "https://picsum.photos/536/354",
 *                  "advertUrl": "https://example.com",
 *                }, {
 *                  "advertPosition": "4",
 *                  "advertKey": "qH92p7s28",
 *                  "advertImagePath": "https://picsum.photos/536/354",
 *                  "advertUrl": "https://example.com",
 *                }, {
 *                  "advertPosition": "5",
 *                  "advertKey": "nf8kgh2Wt",
 *                  "advertImagePath": "https://picsum.photos/536/354",
 *                  "advertUrl": "https://example.com",
 *                }},
 *                @OA\Items(
*                      @OA\Property(
*                         property="advertPosition",
*                         type="string",
*                      ),
*                      @OA\Property(
*                         property="advertKey",
*                         type="string",
*                      ),
*                      @OA\Property(
*                         property="advertImagePath",
*                         type="string",
*                      ),
*                      @OA\Property(
*                         property="advertUrl",
*                         type="string",
*                      ),
*                ),
 *         ),
 *       @OA\Property(property="dateCreated", type="string", example="2022-07-11 05:15:29"),
 *     )
 *   ),
 *
 *  @OA\Response(
 *    response=400,
 *    description="Failed",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Failed"),
 *     )
 *   )
 *
 * )
 */
    public function global_funa(Request $request){

        $global_status= Cache::remember('global_systemStatusId',24*60*60,function (){
            return GlobalSystemStatus::where("global_systemStatusId", 1)->first();
        });

        $publisher= Cache::remember('publisherKey_'.$request->publisherKey,24*60*60,function() use ($request){
            return $publisher = GlobalPublisher::where("publisherKey", $request->publisherKey)->first();
        });
        $validation = Validator::make($request->all(),[
            'publisherKey' => 'required',
        ]);
        if($validation->fails()){
            $data["status"] = "Failed";
        }else{
            if(!empty($global_status) && !empty($publisher) && $global_status->global_systemStatusValue == "ACTIVE" &&  $publisher->publisherStatus == "ACTIVE"){

                /*$adverts = GlobalAvert::select("global_adverts.*")
                    ->leftJoin("global_clients", "global_clients.clientKey", "=", "global_adverts.clientKey")
                    ->where("global_adverts.advertStatus", "ACTIVE")
                    ->whereRaw('global_adverts.advertImpressionCount <= global_adverts.advertImpressionLimit')
                    ->where("global_adverts.publisherKey", $request->publisherKey)
                    ->get()
                    ->toArray();*/

                $adverts = GlobalAvert::leftJoin("global_clients", "global_clients.clientKey", "=", "global_adverts.clientKey")
                    ->where("global_adverts.advertStatus", "ACTIVE")
                    ->whereRaw('global_adverts.advertImpressionCount <= global_adverts.advertImpressionLimit')
                    ->where("global_adverts.publisherKey", $request->publisherKey);

               $globalDisplay= array();
                for($i=1; $i<=6; $i++){
                    if($i==6){
                        /*$adverts = GlobalAvert::select("global_adverts.advertPosition","global_adverts.advertPosition","global_adverts.advertKey","global_adverts.advertUrl" )
                            ->leftJoin("global_clients", "global_clients.clientKey", "=", "global_adverts.clientKey")
                            ->where("global_adverts.advertStatus", "ACTIVE")
                            ->where("global_adverts.advertPosition",$i)
                            ->whereRaw('global_adverts.advertImpressionCount <= global_adverts.advertImpressionLimit')
                            ->where("global_clients.clientStatus", "<>", "DISABLED")
                            ->where("global_adverts.publisherKey", $request->publisherKey)
                            ->inRandomOrder()
                            ->limit(3)->get();*/

                            $tepmadverts    =   clone $adverts;
                            $tepmadverts    =   $tepmadverts->select("global_adverts.advertPosition","global_adverts.advertPosition","global_adverts.advertKey","global_adverts.advertUrl" )
                                ->where("global_adverts.advertPosition",$i)
                                ->where("global_clients.clientStatus", "<>", "DISABLED")
                                ->inRandomOrder()
                                ->limit(3)->get();
                            $globalDisplay[$i] = (count($tepmadverts)>0) ?  $tepmadverts : "no available advert";

                        // if(count($adverts)>0){
                        //     foreach($adverts as $item){
                        //         $globalDisplay[$i][] = array(
                        //             "advertPosition"=> (string)$item->advertPosition,
                        //             "advertKey"=>$item->advertKey,
                        //             "advertImagePath"=>$item->advertImagePath,
                        //             "advertUrl"=>$item->advertUrl,
                        //         );
                        //     }

                        // }else{
                        //     $globalDisplay[$i] ="no available advert";
                        // }
                    }else{
                        /*$adverts = GlobalAvert::select("global_adverts.*")
                            ->leftJoin("global_clients", "global_clients.clientKey", "=", "global_adverts.clientKey")
                            ->where("global_adverts.advertStatus", "ACTIVE")
                            ->where("global_adverts.advertPosition",$i)
                            ->whereRaw('global_adverts.advertImpressionCount <= global_adverts.advertImpressionLimit')
                            ->where("global_clients.clientStatus", "<>", "DISABLED")
                            ->where("global_adverts.publisherKey", $request->publisherKey)
                            ->inRandomOrder()
                            ->first();*/

                        $tepmadverts    =   clone $adverts;
                        $tepmadverts    =   $tepmadverts->select("global_adverts.*")
                                ->where("global_adverts.advertPosition",$i)
                                ->where("global_clients.clientStatus", "<>", "DISABLED")
                                ->inRandomOrder()
                                ->first();

                        if($tepmadverts){
                            $globalDisplay[$i] = array(
                                "advertPosition"=> (string)$tepmadverts->advertPosition,
                                "advertKey"=>$tepmadverts->advertKey,
                                "advertImagePath"=>$tepmadverts->advertImagePath,
                                "advertUrl"=>$tepmadverts->advertUrl,
                            );
                        }else{
                            $globalDisplay[$i] ="no available advert";
                        }
                    }

                }
                $data["status"] = "Success";
                $data["publisherKey"] = $request->publisherKey;
                $data["globalDisplay"] = $globalDisplay;
                $data["dateCreated"] = date("Y-m-d H:i:s");
            }else{
                $data["status"] = "Failed";
            }
        }

        return response()->json($data, 200);
    }


    public function global_test()
    {
        dd($this->getRandclickKey());
    }
}


