<?php

namespace App\Http\Controllers;

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
            // $id = auth()->user()->id;
            // $medias =  Media::where('user_id', $id)->first();
            $advertisment =  Advertisment::all()->toArray();
            if (!empty($advertisment)) {
                foreach ($advertisment as $ads) {
                    $data['id'] = $ads['id'];
                    $data['exe_img'] = explode('|', $ads['ads_image']);
                    $data['created_at'] = $ads['created_at'];
                    $data['updated_at'] = $ads['updated_at'];
                }

                return response()->json([
                    'message' => 'All Ads List',
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'message' => 'No Ads Available',
                ]);
            }
        } catch (\Exception $e) {
            response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $id = auth()->user()->id;
        // if (!empty($auth_user_id)) {
        $validator =  Validator::make($request->all(), [
            'ads_image' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $chkads = Advertisment::where('ads_image', $request->ads_image)->first();
            // $mediacount = explode('|', $query->media_image);
            // $total_count = count($mediacount);
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

                return response()->json([
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
                    'message' => 'Advertisment Updated Successfully',
                    'data' => $adsmedia,
                ]);
            }
            // } else {
            //     return ApiResponse::error('User Not Authanticated');
            // }
        } catch (\Exception $e) {
            response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
