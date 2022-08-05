<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContextualAdvert;
use App\Models\ContextualClick;
use App\Models\ContextualImpression;
use App\Models\ContextualKeyword;
use App\Models\GlobalPublisher;
use App\Models\GlobalSystemStatus;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
class ContextualController extends Controller
{
/**
 * @OA\Post(
 *  path="/hlola/contextual/isipho",
 *  summary="contextual isipho",
 *  description="Create a new contextual_clicks record",
 *  operationId="contextual_isipho",
 *  tags={"API Reference"},
 *  @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"publisherKey","userKey","keywordKey","advertKey","impressionKey","sessionKey"},
 *       @OA\Property(property="publisherKey", type="string",  example="HD3kd92JA"),
 *       @OA\Property(property="userKey", type="string",  example="Dhd38FDHD"),
 *       @OA\Property(property="keywordKey", type="string", example="dJVmVEG2T"),
 *       @OA\Property(property="advertKey", type="string", example="wCkbp9XHF"),
 *       @OA\Property(property="impressionKey", type="string", example="D2aEHp8bM"),
 *       @OA\Property(property="sessionKey", type="string", example="D2aEHp8bM"),
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
    public function contextual_isipho(Request $request){

        $global_status= Cache::remember('global_systemStatusId',24*60*60,function (){
            return GlobalSystemStatus::where("global_systemStatusId", 1)->first();
        });

        $publisher= Cache::remember('publisherKey_'.$request->publisherKey,24*60*60,function() use ($request){
            return $publisher = GlobalPublisher::where("publisherKey", $request->publisherKey)->first();
        });

        $validation = Validator::make($request->all(),[
            'publisherKey' => 'required',
            'userKey' => 'required',
            'impressionKey' => 'required',
            'keywordKey' => 'required',
            'advertKey' => 'required',
            'sessionKey' => 'required',
        ]);
        if($validation->fails()){
            $data["status"] = "Failed";
        }else{
            if(!empty($global_status) && !empty($publisher) && $global_status->global_systemStatusValue == "ACTIVE" &&  $publisher->publisherStatus == "ACTIVE"){
                $contextual = new ContextualClick();
                $contextual->clickKey = $this->getRandclickKey();
                $contextual->sessionKey = $request->sessionKey;
                $contextual->userKey = $request->userKey;
                $contextual->impressionKey = $request->impressionKey;
                $contextual->keywordKey = $request->keywordKey;
                $contextual->advertKey = $request->advertKey;

                if($contextual->save()){
                    Cache::put('getRandclickKey',$contextual->clickId);
                    $data["status"] = "Success";
                    $data["clickKey"] = $contextual->clickKey;
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
 *  path="/hlola/contextual/mamatheka",
 *  summary="contextual mamatheka",
 *  description="Create a new contextual_impressions record",
 *  operationId="contextual_mamatheka",
 *  tags={"API Reference"},
 *  @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"publisherKey","sessionKey","userKey","keywordKey","advertKey"},
 *       @OA\Property(property="publisherKey", type="string",  example="HD3kd92JA"),
 *       @OA\Property(property="sessionKey", type="string",  example="AJD18Sgh1"),
 *       @OA\Property(property="userKey", type="string", example="Dhd38FDHD"),
 *       @OA\Property(property="keywordKey", type="string", example="dJVmVEG2T"),
 *       @OA\Property(property="advertKey", type="string", example="wCkbp9XHF"),
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
    public function contextual_mamatheka(Request $request){
        $global_status= Cache::remember('global_systemStatusId',24*60*60,function (){
            return GlobalSystemStatus::where("global_systemStatusId", 1)->first();
        });

        $publisher= Cache::remember('publisherKey_'.$request->publisherKey,24*60*60,function() use ($request){
            return $publisher = GlobalPublisher::where("publisherKey", $request->publisherKey)->first();
        });
        $validation = Validator::make($request->all(),[
            'sessionKey' => 'required',
            'publisherKey' => 'required',
            'userKey' => 'required',
            'keywordKey' => 'required',
            'advertKey' => 'required',
        ]);
        if($validation->fails()){
            $data["status"] = "Failed";
        }else{
            if (!empty($global_status) && !empty($publisher) && $global_status->global_systemStatusValue == "ACTIVE" &&  $publisher->publisherStatus == "ACTIVE") {
                $impression = new ContextualImpression();
                $impression->impressionKey = $this->getRandImpressionKey();
                $impression->sessionKey = $request->sessionKey;
                $impression->userKey = $request->userKey;
                $impression->keywordKey = $request->keywordKey;
                $impression->advertKey = $request->advertKey;

                if($impression->save()){
                    Cache::put('getRandImpressionKey',$impression->impressionId);
                    $data["status"] = "Success";
                    $data["impressionKey"] = $impression->impressionKey;
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
 *  path="/hlola/contextual/funa",
 *  summary="contextual funa",
 *  description="Output data from contextual_adverts db table",
 *  operationId="contextual_funa",
 *  tags={"API Reference"},
 *  @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"publisherKey","keywordText"},
 *       @OA\Property(property="publisherKey", type="string",  example="HD3kd92JA"),
 *       @OA\Property(property="keywordText", type="string",  example="submissive,dominant,pet,prey,switch"),
 *    ),
 *  ),
 *
 *  @OA\Response(
 *    response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="Success"),
 *       @OA\Property(property="keywordKey", type="string", example="dJVmVEG2T"),
 *       @OA\Property(property="contextualDisplay", type="array",
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
    public function contextual_funa(Request $request){
        $global_status= Cache::remember('global_systemStatusId',24*60*60,function (){
            return GlobalSystemStatus::where("global_systemStatusId", 1)->first();
        });

        $publisher= Cache::remember('publisherKey_'.$request->publisherKey,24*60*60,function() use ($request){
            return $publisher = GlobalPublisher::where("publisherKey", $request->publisherKey)->first();
        });
        $validation = Validator::make($request->all(),[
            'publisherKey' => 'required',
            'keywordText' => 'required',
        ]);
        if($validation->fails()){
            $data["status"] = "Failed";
        }else{
            if (!empty($global_status) && !empty($publisher) && $global_status->global_systemStatusValue == "ACTIVE" &&  $publisher->publisherStatus == "ACTIVE") {
                $keywords = explode(",",$request->keywordText);
                $keys = ContextualKeyword::whereIn("keywordText", $keywords)->where("keywordStatus", "ACTIVE")->pluck("keywordKey")->toArray();
                foreach($keys as $key){
                    $check = ContextualAdvert::where("keywordKey", $key)->where("advertStatus", "ACTIVE")->whereBetween("advertPosition", [1, 5])->get();
                    if(count($check)>0){
                        $contextualDisplay= array();
                        for($i=1; $i<=5; $i++){
                            $adverts = ContextualAdvert::select("contextual_adverts.advertPosition","contextual_adverts.advertKey","contextual_adverts.advertImagePath","contextual_adverts.advertUrl")
                                                ->where("keywordKey", $key)
                                                ->where("advertStatus", "ACTIVE")
                                                ->where("advertPosition",$i)
                                                ->inRandomOrder()
                                                ->first();
                            $contextualDisplay[$i] = (!empty($adverts)) ? $adverts :"no available advert";
                            // if($adverts){
                            //     $contextualDisplay[$i] = array(
                            //         "advertPosition"=>(string)$adverts->advertPosition,
                            //         "advertKey"=>$adverts->advertKey,
                            //         "advertImagePath"=>$adverts->advertImagePath,
                            //         "advertUrl"=>$adverts->advertUrl,
                            //     );
                            // }else{
                            //     $contextualDisplay[$i] ="no available advert";
                            // }
                        }
                        $data["status"] = "Success";
                        $data["keywordKey"] = $key;
                        $data["contextualDisplay"] = $contextualDisplay;
                        $data["dateCreated"] = date("Y-m-d H:i:s");
                        return response()->json($data, 200);
                    }
                }
                $data["status"] = "Failed";
             }else{
                 $data["status"] = "Failed";
             }
        }

        return response()->json($data, 200);
    }
}


