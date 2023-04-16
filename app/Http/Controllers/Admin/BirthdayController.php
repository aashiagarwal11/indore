<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\Birthday;
use App\Models\City;
use Illuminate\Support\Facades\Validator;


class BirthdayController extends Controller
{
    public function birthdayList()
    {
        $apiurl = env('APP_URL') . 'api/birthdayListOfUser';
        $response = Http::get($apiurl, [
            'role_id' => auth()->user()->role_id,
        ]);
        $newdata =  $response->json($key = null, $default = null);
        $birthdayData = $newdata['data'];
        return view('admin.birthday.index', compact('birthdayData'));
    }


    public function birthdayImage($id)
    {
        $bdata = Birthday::where('id', $id)->first();
        $bdata->image = str_replace("public", env('APP_URL') . "public", $bdata->image);

        $exp = explode('|', $bdata->image);
        return view('admin.birthday.birthdayImage', compact('exp'));
    }

    public function getbirthdayForm()
    {
        $cityData = City::get();
        return view('admin.Birthday.birthdayForm', compact('cityData'));
    }

    public function addbirthday(Request $request)
    {
        if ($request->role_id == 1) {
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
                        $upload_path = 'public/birthday/';
                        $img_url = $upload_path . $img_full_name;
                        $file->move($upload_path, $img_full_name);
                        array_push($images, $img_url);
                    }
                }
                $imp_image =  implode('|', $images);

                $birthday = Birthday::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'image' => $imp_image,
                    'video_url' => $request->video_url ?? null,
                    'user_id' => 1,
                    'city_id' => $request->city_id,
                    'status' => 1,
                ]);

                return redirect()->route('birthdayList');
            }
        }
    }

    public function getbirthdayEditForm(Request $request, $id)
    {
        $bdata = Birthday::where('id', $id)->first();
        $cityData = City::get();

        return view('admin.birthday.birthdayEditForm', compact('bdata', 'cityData'));
    }

    public function updatebirthday(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/birthday/' . $request->id;
        $response = Http::put($apiurl, [
            'role_id' => auth()->user()->role_id,
            'title' => $request->title,
            'description' => $request->description,
            'city_id' => $request->city_id,
            'image' => $request->image,
            'video_url' => $request->video_url,
        ]);
        $newdata =  $response->json();
        return redirect()->route('birthdayList');
    }

    public function acceptBday(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/acceptBirthday';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,   ## 1
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function denyBday(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/denyBirthday';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,   ## 1
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }
}
