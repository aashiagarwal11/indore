<?php

namespace App\Http\Controllers\Advertisment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisment;
use Illuminate\Support\Facades\Validator;


class AdvertismentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $advertisment =  Advertisment::orderBy('id', 'desc')->get()->toArray();
            if (!empty($advertisment)) {
                $newarr = [];
                foreach ($advertisment as $key => $new) {
                    $new['ads_image'] = str_replace("public", env('APP_URL') . "public", $new['ads_image']);
                    $new['ads_image'] = explode('|', $new['ads_image']);
                    $new['ads_image'] = ($new['ads_image'][0] != "") ? $new['ads_image'] : [];
                    array_push($newarr, $new);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'All Ads List',
                    'data' => $newarr,
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No Ads Available',
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
            'ads_image'             => ['required'],
            'ads_image.*'           => ['mimes:jpeg,png,jpg']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ]);
        }
        try {
            $chkads = Advertisment::where('ads_image', $request->ads_image)->first();
            if (empty($chkads)) {
                $images = array();
                if ($files = $request->file('ads_image')) {
                    foreach ($files as $file) {
                        $imgname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $img_full_name = $imgname . '.' . $extension;
                        $upload_path = 'public/adsImage/';
                        $img_url = $upload_path . $img_full_name;
                        $file->move($upload_path, $img_full_name);
                        array_push($images, $img_url);
                    }
                }

                $imp_image =  implode('|', $images);

                $media = Advertisment::create([
                    'ads_image' => $imp_image,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
                $imp_image = str_replace("public", env('APP_URL') . "public", $imp_image);
                $exp = explode('|', $imp_image);
                $media['ads_image'] = ($exp[0] != "") ? $exp : [];

                return response()->json([
                    'success' => true,
                    'message' => 'Advertisment Added Successfully',
                    'data' => $media,
                ]);
            } else {
                $explode = explode('|', $chkads->ads_image);
                if ($files = $request->file('ads_image')) {
                    foreach ($files as $file) {
                        $imgname = md5(rand('1000', '10000'));
                        $extension = strtolower($file->getClientOriginalExtension());
                        $img_full_name = $imgname . '.' . $extension;
                        $upload_path = 'public/adsImage/';
                        $img_url = $upload_path . $img_full_name;
                        $file->move($upload_path, $img_full_name);

                        array_push($explode, $img_url);
                    }
                }
                $imp_image =  implode('|', $explode);

                $verified['ads_image'] = $imp_image;
                $verified['updated_at']  = \Carbon\Carbon::now();
                $adsmedia = Advertisment::where('ads_image', $verified['ads_image'])->update($verified);
                return response()->json([
                    'success' => true,
                    'message' => 'Advertisment Updated Successfully',
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
            $advertisment = Advertisment::find($id);
            if (!empty($advertisment)) {
                $exdata = $advertisment->ads_image;
                $advertisment['ads_image'] = explode('|', $exdata);
                $advertisment['ads_image'] = str_replace("public", env('APP_URL') . "public", $advertisment['ads_image']);
                return response()->json([
                    'success' => true,
                    'message' => 'Advertisment',
                    'data' => $advertisment,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Record ' . $id . ' not exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator =  Validator::make($request->all(), [
            'ads_image'             => ['required'],
            'ads_image.*'           => ['mimes:jpeg,png,jpg'],
            'key'                   => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()]);
        }
        try {
            $user_ads = Advertisment::where('id', $id)->first();
            if (!empty($user_ads)) {
                $explode = explode('|', $user_ads->ads_image);
                if (isset($explode[$request->key])) {
                    $keyimg = $explode[$request->key];


                    if ($files = $request->file('ads_image')[0]) {
                        $imgname = md5(rand('1000', '10000'));
                        $extension = strtolower($files->getClientOriginalExtension());
                        $img_full_name = $imgname . '.' . $extension;
                        $upload_path = 'public/adsImage/';
                        $img_url = $upload_path . $img_full_name;
                        $files->move($upload_path, $img_full_name);

                        // if (!empty($explode[$request->key])) {
                        //     unlink($explode[$request->key]);
                        // }
                        $explode[$request->key] = $img_url;
                    }
                    $imp_image =  implode('|', $explode);

                    $verified['ads_image'] = $imp_image;
                    $media = Advertisment::where('id', $id)->update($verified);
                    $verified['exp_image'] =  explode('|', $imp_image);
                    $mediadata = Advertisment::where('id', $id)->first();

                    $mediadata['id'] = $mediadata->id;
                    $mediadata['ads_image'] = $explode;
                    $mediadata['created_at'] = $mediadata->created_at;
                    $mediadata['updated_at'] = $mediadata->updated_at;
                    return response()->json([
                        'success' => true,
                        'message' => 'Ads Updated Successfully',
                        'data' => $mediadata,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Key Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Record ' . $id . ' not exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $validator =  Validator::make($request->all(), [
            'key' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()]);
        }
        try {
            $user_media_delete = Advertisment::where('id', $id)->first();

            if (!empty($user_media_delete)) {
                $images = explode("|", $user_media_delete->ads_image);
                if (isset($images[$request->key])) {

                    $inarr =  in_array($images[$request->key], $images);

                    if ($inarr == true) {
                        unlink($images[$request->key]);
                        unset($images[$request->key]);
                    }

                    $arr = implode('|', $images);
                    $verified['ads_image'] = $arr;

                    $getadsimg = Advertisment::where('id', $id)->update($verified);
                    $deleted_media = Advertisment::where('id', $id)->first();

                    $deleted_media['ads_image'] = explode('|', $deleted_media->ads_image);

                    return response()->json([
                        'success' => true,
                        'message' => 'Ads Deleted Successfully',
                        'data' => $deleted_media,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Key Not Exist',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Record ' . $id . ' not exist',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
