<?php

namespace App\Http\Controllers\Watermark;

use App\Http\Controllers\Controller;
use App\Models\Watermark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class WatermarkController extends Controller
{
    public function addWatermark(Request $request)
    {
        $id = auth()->user()->id;
        $role_id = auth()->user()->role_id;
        try {
            if (!empty($id)) {
                if ($role_id == 1) {
                    $getData = Watermark::first();
                    if (!empty($getData)) {
                        $validator = Validator::make($request->all(), [
                            'image'    => ['required'],
                        ]);

                        if ($validator->fails()) {
                            return response()->json(['status' => false, 'message' => $validator->errors()]);
                        }
                        if ($file = $request->file('image')) {
                            $imagename = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $image_full_name = $imagename . '.' . $extension;
                            $upload_path = 'public/watermark/';
                            $image_url = $upload_path . $image_full_name;
                            $file->move($upload_path, $image_full_name);
                        }
                        $getData['image'] = $image_url;
                        $updateImage = $getData->update();
                        $imp_image = str_replace("public", env('APP_URL') . "public", $image_url);

                        $getData['image'] = $imp_image;

                        return response()->json([
                            'success' => true,
                            'message' => 'Watermark Updated Successfully',
                            'data' => $getData,
                        ]);
                    } else {
                        $validator = Validator::make($request->all(), [
                            'image'   => ['required'],
                        ]);

                        if ($validator->fails()) {
                            return response()->json(['status' => false,'message' => $validator->errors()]);
                        }

                        if ($file = $request->file('image')) {
                            $imagename = md5(rand('1000', '10000'));
                            $extension = strtolower($file->getClientOriginalExtension());
                            $image_full_name = $imagename . '.' . $extension;
                            $upload_path = 'public/watermark/';
                            $image_url = $upload_path . $image_full_name;
                            $file->move($upload_path, $image_full_name);
                        }
                        $waterimage = Watermark::create([
                            'image' => $image_url,
                        ]);
                        $imp_image = str_replace("public", env('APP_URL') . "public", $image_url);

                        $waterimage['image'] = $imp_image;

                        return response()->json([
                            'success' => true,
                            'message' => 'Watermark Added Successfully',
                            'data' => $waterimage,
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Login as admin',
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function showWatermark()
    {
        try {
            $watermark = Watermark::get()->toArray();
            if (!empty($watermark)) {
                $watermark[0]['image'] = str_replace("public", env('APP_URL') . "public", $watermark[0]['image']);
                return response()->json([
                    'success' => true,
                    'message' => 'List of Resume',
                    'data' => $watermark,
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No data exist',
                    'data' => [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
