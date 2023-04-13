<?php

namespace App\Http\Controllers\Premium;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PremiumAds;
use Illuminate\Support\Facades\Validator;


class PremiumAdsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $premiumAds =  PremiumAds::orderBy('id', 'desc')->get()->toArray();
            if (!empty($premiumAds)) {
                $newarr = [];
                foreach ($premiumAds as $key => $new) {
                    $new['premium_ads_image'] = str_replace("public", env('APP_URL') . "public", $new['premium_ads_image']);
                    // $new['premium_ads_image'] = explode('|', $new['premium_ads_image']);
                    array_push($newarr, $new);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'All Premium Ads List',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No Premium Ads Available',
                    'data' => [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'premium_ads_image'             => ['required'],
            'premium_ads_image.*'           => ['mimes:jpeg,png,jpg']
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }
        try {
            $chkads = PremiumAds::where('premium_ads_image', $request->premium_ads_image)->first();
            if (empty($chkads)) {
                $images = array();
                if ($file = $request->file('premium_ads_image')) {
                    // foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/PremiumAdsImage/';
                    $img_url = $upload_path . $img_full_name;
                    $file->move($upload_path, $img_full_name);
                    // array_push($images, $img_url);
                    // }
                }

                // $imp_image =  implode('|', $images);
                $media = PremiumAds::create([
                    'premium_ads_image' => $img_url,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
                $imp_image = str_replace("public", env('APP_URL') . "public", $img_url);
                $media['premium_ads_image'] = $imp_image;

                return response()->json([
                    'status' => true,
                    'message' => 'Premium Advertisment Added Successfully',
                    'data' => $media,
                ]);
            } else {
                $explode = explode('|', $chkads->premium_ads_image);
                if ($files = $request->file('premium_ads_image')) {
                    foreach ($files as $file) {
                        $imgname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $img_full_name = $imgname . '.' . $extension;
                        $upload_path = 'public/PremiumAdsImage/';
                        $img_url = $upload_path . $img_full_name;
                        $file->move($upload_path, $img_full_name);

                        array_push($explode, $img_url);
                    }
                }
                $imp_image =  implode('|', $explode);

                $verified['premium_ads_image'] = $imp_image;
                $verified['updated_at']  = \Carbon\Carbon::now();
                $adsmedia = premiumAds::where('premium_ads_image', $verified['premium_ads_image'])->update($verified);
                return response()->json([
                    'status' => true,
                    'message' => 'Premium Advertisment Updated Successfully',
                    'data' => $adsmedia,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $premiumAds = PremiumAds::find($id);
            if (!empty($premiumAds)) {
                $exdata = $premiumAds->premium_ads_image;

                // dd($exdata);
                // $premiumAds['premium_ads_image'] = explode('|', $exdata);
                $premiumAds['premium_ads_image'] = str_replace("public", env('APP_URL') . "public", $exdata);
                return response()->json([
                    'status' => true,
                    'message' => 'Premium Ads',
                    'data' => $premiumAds,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Record ' . $id . ' not exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, string $id)
    {
        // $validator =  Validator::make($request->all(), [
        //     'status' => ['required', 'numeric'],
        // ]);

        // $status = $request->status;

        // if ($validator->fails()) {
        //     return response()->json(['status' => false, 'message' => $validator->errors()]);
        // }
        // try {
        //     $user_ads = PremiumAds::where('id', $id)->first();
        //     if (!empty($user_ads)) {
        //         $verified['status'] = $status;

        //         $media = PremiumAds::where('id', $id)->update($verified);
        //         if ($status == 1) {
        //             return response()->json([
        //                 'status' => true,
        //                 'message' => 'Record Active',
        //             ]);
        //         } else {
        //             return response()->json([
        //                 'status' => true,
        //                 'message' => 'Record Deactive',
        //             ]);
        //         }
        //     } else {
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'Record ' . $id . ' not exist',
        //         ]);
        //     }
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'message' => $e->getMessage(),
        //     ]);
        // }
    }


    public function activedeactivepremiumAds(Request $request)
    {
        $id = $request->id;
        $validator =  Validator::make($request->all(), [
            'status' => ['required', 'numeric'],
            'id'     => ['required', 'numeric'],
        ]);

        $status = $request->status;

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }
        try {
            $user_ads = PremiumAds::where('id', $id)->first();
            if (!empty($user_ads)) {
                $verified['status'] = $status;

                $media = PremiumAds::where('id', $id)->update($verified);
                if ($status == 1) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Record Active',
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'message' => 'Record Deactive',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Record ' . $id . ' not exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }



    // public function update(Request $request, string $id)
    // {
    //     //single image updation
    //     $validator =  Validator::make($request->all(), [
    //         'premium_ads_image'             => ['required'],
    //         'premium_ads_image.*'           => ['mimes:jpeg,png,jpg'],
    //         // 'key'                           => ['required', 'numeric'],
    //         // 'key'                           => ['required', 'numeric'],
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'message' => $validator->errors()]);
    //     }
    //     try {
    //         $user_ads = PremiumAds::where('id', $id)->first();
    //         if (!empty($user_ads)) {
    //             // $explode = explode('|', $user_ads->premium_ads_image);
    //             // if (isset($explode[$request->key])) {
    //                 // $keyimg = $explode[$request->key];


    //                 if ($files = $request->file('premium_ads_image')) {
    //                     // dd($files);
    //                     $imgname = md5(rand('1000', '10000'));
    //                     $extension = strtolower($files->getClientOriginalExtension());
    //                     $img_full_name = $imgname . '.' . $extension;
    //                     $upload_path = 'public/PremiumAdsImage/';
    //                     $img_url = $upload_path . $img_full_name;
    //                     $files->move($upload_path, $img_full_name);

    //                     // $explode[$request->key] = $img_url;
    //                 }
    //                 // $imp_image =  implode('|', $explode);

    //                 $verified['premium_ads_image'] = $img_url;
    //                 $media = PremiumAds::where('id', $id)->update($verified);
    //                 // $verified['exp_image'] =  explode('|', $imp_image);
    //                 $mediadata = PremiumAds::where('id', $id)->first();
    //                 $imp_image = str_replace("public", env('APP_URL') . "public", $img_url);

    //                 $mediadata['id'] = $mediadata->id;
    //                 $mediadata['premium_ads_image'] = $imp_image;
    //                 $mediadata['created_at'] = $mediadata->created_at;
    //                 $mediadata['updated_at'] = $mediadata->updated_at;
    //                 return response()->json([
    //                     'status' => true,
    //                     'message' => 'Premium Ads Updated Successfully',
    //                     'data' => $mediadata,
    //                 ]);
    //             // } else {
    //             //     return response()->json([
    //             //         'status' => false,
    //             //         'message' => 'Key Not Exist',
    //             //     ]);
    //             // }
    //         } else {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Record ' . $id . ' not exist',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }
    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     $validator =  Validator::make($request->all(), [
    //         'premium_ads_image'             => ['required'],
    //         'premium_ads_image.*'           => ['mimes:jpeg,png,jpg'],
    //         'key'                           => ['required', 'numeric'],
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'message' => $validator->errors()]);
    //     }
    //     try {
    //         $user_ads = PremiumAds::where('id', $id)->first();
    //         if (!empty($user_ads)) {
    //             $explode = explode('|', $user_ads->premium_ads_image);
    //             if (isset($explode[$request->key])) {
    //                 $keyimg = $explode[$request->key];


    //                 if ($files = $request->file('premium_ads_image')[0]) {
    //                     $imgname = md5(rand('1000', '10000'));
    //                     $extension = strtolower($files->getClientOriginalExtension());
    //                     $img_full_name = $imgname . '.' . $extension;
    //                     $upload_path = 'public/PremiumAdsImage/';
    //                     $img_url = $upload_path . $img_full_name;
    //                     $files->move($upload_path, $img_full_name);

    //                     $explode[$request->key] = $img_url;
    //                 }
    //                 $imp_image =  implode('|', $explode);

    //                 $verified['premium_ads_image'] = $imp_image;
    //                 $media = PremiumAds::where('id', $id)->update($verified);
    //                 $verified['exp_image'] =  explode('|', $imp_image);
    //                 $mediadata = PremiumAds::where('id', $id)->first();

    //                 $mediadata['id'] = $mediadata->id;
    //                 $mediadata['premium_ads_image'] = $explode;
    //                 $mediadata['created_at'] = $mediadata->created_at;
    //                 $mediadata['updated_at'] = $mediadata->updated_at;
    //                 return response()->json([
    //                     'status' => true,
    //                     'message' => 'Premium Ads Updated Successfully',
    //                     'data' => $mediadata,
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Key Not Exist',
    //                 ]);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Record ' . $id . ' not exist',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Request $request, string $id)
    {
        try {
            $delete = PremiumAds::where('id', $id)->first();
            if (!empty($delete)) {
                $getdeleterec = $delete->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Record Deleted Successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Record Not Exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function premiumads()
    {
        ## premium ads api
        try {
            $ads = PremiumAds::where('status', 1)->get()->random(1)->toArray();
            if (!empty($ads)) {
                $ads[0]['premium_ads_image'] = str_replace("public", env('APP_URL') . "public", $ads[0]['premium_ads_image']);
                return response()->json([
                    'status' => true,
                    'message' => 'Premium Ad',
                    'data' => $ads,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No Premium Ads Exist',
                    'data' => [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }





    // public function index()
    // {
    //     try {
    //         $premiumAds =  PremiumAds::orderBy('id', 'desc')->get()->toArray();
    //         if (!empty($premiumAds)) {
    //             $newarr = [];
    //             foreach ($premiumAds as $key => $new) {
    //                 $new['premium_ads_image'] = str_replace("public", env('APP_URL') . "public", $new['premium_ads_image']);
    //                 $new['premium_ads_image'] = explode('|', $new['premium_ads_image']);
    //                 array_push($newarr, $new);
    //             }
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'All Premium Ads List',
    //                 'data' => $newarr,
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'No Premium Ads Available',
    //                 'data' => [],
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }







    // public function destroy(Request $request, string $id)
    // {
    //     $validator =  Validator::make($request->all(), [
    //         'key' => ['required', 'numeric'],
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'message' => $validator->errors()]);
    //     }
    //     try {
    //         $user_media_delete = PremiumAds::where('id', $id)->first();

    //         if (!empty($user_media_delete)) {
    //             $images = explode("|", $user_media_delete->premium_ads_image);
    //             if (isset($images[$request->key])) {

    //                 $inarr =  in_array($images[$request->key], $images);

    //                 if ($inarr == true) {
    //                     unlink($images[$request->key]);
    //                     unset($images[$request->key]);
    //                 }

    //                 $arr = implode('|', $images);
    //                 $verified['premium_ads_image'] = $arr;

    //                 $getadsimg = PremiumAds::where('id', $id)->update($verified);
    //                 $deleted_media = PremiumAds::where('id', $id)->first();

    //                 $deleted_media['premium_ads_image'] = explode('|', $deleted_media->premium_ads_image);

    //                 return response()->json([
    //                     'status' => true,
    //                     'message' => 'Premium Ads Deleted Successfully',
    //                     'data' => $deleted_media,
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Key Not Exist',
    //                 ]);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Record ' . $id . ' not exist',
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }

    // public function premiumads()
    // {
    //     ## random ads api
    //     try {
    //         $ads = PremiumAds::all()->random(1);
    //         if (!empty($ads)) {
    //             $image = explode('|', $ads[0]->premium_ads_image);
    //             shuffle($image);

    //             $ads[0]->premium_ads_image = $image[0];
    //             $ads[0]->premium_ads_image = str_replace("public", env('APP_URL') . "public", $ads[0]->premium_ads_image);
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Premium Ad',
    //                 'data' => $ads,
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'No Premium Ads Exist',
    //                 'data' => [],
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }
}
