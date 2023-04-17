<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\KrishiMandiBhav;
use App\Models\City;
use Illuminate\Support\Facades\Validator;


class KrishiMandiBhavController extends Controller
{
    public function krishiList()
    {
        $apiurl = env('APP_URL') . 'api/krishiMandiBhav';
        $response = Http::get($apiurl, [
            'role_id' => auth()->user()->role_id,
        ]);
        $newdata =  $response->json($key = null, $default = null);
        $birthdayData = $newdata['data'];
        return view('admin.KrishiMandiBhav.index', compact('birthdayData'));
    }


    public function krishiImage($id)
    {
        $bdata = KrishiMandiBhav::where('id', $id)->first();
        $bdata->image = str_replace("public", env('APP_URL') . "public", $bdata->image);

        if ($bdata->image == "") {
            $exp = null;
        } else {
            $exp = explode('|', $bdata->image);
            $key = array_search("", $exp);
            unset($exp[$key]);
        }
        return view('admin.KrishiMandiBhav.krishiImage', compact('exp', 'id'));
    }

    public function getkrishiForm()
    {
        $cityData = City::get();
        return view('admin.KrishiMandiBhav.krishiForm', compact('cityData'));
    }

    public function addkrishi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => ['required', 'string'],
            'description' => ['required'],
            'city_id'     => ['required', 'numeric'],
            'image'       => ['nullable'],
            'image.*'     => ['mimes:jpeg,png,jpg'],
            'video_url'   => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()]);
        }

        $city = City::where('id', $request->city_id)->first();
        if (!empty($city)) {
            $images = array();
            if ($files = $request->file('image')) {
                foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/krishiMandiImage/';
                    $img_url = $upload_path . $img_full_name;
                    $file->move($upload_path, $img_full_name);
                    array_push($images, $img_url);
                }
            }
            $imp_image =  implode('|', $images);

            $krishiMandiBhav = KrishiMandiBhav::create([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $imp_image ?? null,
                'video_url' => $request->video_url ?? null,
                'user_id' => 1,
                'city_id' => $request->city_id,
            ]);

            return redirect()->route('krishiList')->with('message', 'Added Successfully');
        }
    }

    public function getkrishiEditForm(Request $request, $id)
    {
        $bdata = KrishiMandiBhav::where('id', $id)->first();
        $cityData = City::get();

        return view('admin.KrishiMandiBhav.krishiEditForm', compact('bdata', 'cityData'));
    }

    public function updatekrishi(Request $request)
    {
        $id = $request->id;
        $KrishiMandiBhav = KrishiMandiBhav::where('id', $id)->first();
        if (!empty($KrishiMandiBhav)) {
            $validator = Validator::make($request->all(), [
                'title'       => ['required', 'string'],
                'description' => ['required'],
                'city_id'     => ['required', 'numeric'],
                'video_url'   => ['nullable'],
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()]);
            }

            $data['title'] = $request->title;
            $data['description'] = $request->description;
            $data['city_id'] = $request->city_id;
            $data['video_url'] = $request->video_url ?? null;
            $updatedata = $KrishiMandiBhav->update($data);
        }
        // $apiurl = env('APP_URL') . 'api/krishiMandiBhav/' . $request->id;
        // $response = Http::put($apiurl, [
        //     'role_id' => auth()->user()->role_id,
        //     'title' => $request->title,
        //     'description' => $request->description,
        //     'city_id' => $request->city_id,
        //     'image' => $request->image,
        //     'video_url' => $request->video_url,
        // ]);
        // $newdata =  $response->json();
        return redirect()->route('krishiList')->with('message', 'Update Successfully');
    }

    public function addkrishiImage(Request $request, $id)
    {
        $birthday = KrishiMandiBhav::where('id', $id)->first();
        if (!empty($birthday)) {

            $exp = explode('|', $birthday->image);

            if ($files = $request->file('image')) {
                foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/krishiMandiImage/';
                    $img_url = $upload_path . $img_full_name;
                    $file->move($upload_path, $img_full_name);
                    array_push($exp, $img_url);
                }
            }

            $imp_image =  implode('|', $exp);

            $data['image'] = $imp_image;

            $updatedata = $birthday->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Image added successfully',
            ]);
        }
    }
}
