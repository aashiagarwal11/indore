<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\Birthday;
use App\Models\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


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
        return view('admin.Birthday.index', compact('birthdayData'));
    }


    public function birthdayImage($id)
    {
        $bdata = Birthday::where('id', $id)->first();
        $bdata->image = str_replace("public", env('APP_URL') . "public", $bdata->image);


        $exp = explode('|', $bdata->image);
        // $key = array_search("", $exp);
        // unset($exp[$key]);
        return view('admin.Birthday.birthdayImage', compact('exp', 'id'));
    }

    public function getbirthdayForm()
    {
        $cityData = City::get();
        return view('admin.Birthday.birthdayForm', compact('cityData'));
    }

    public function addbirthday(Request $request)
    {
        $validateImageData = $request->validate([
            'title'       => ['required', 'string'],
            'description' => ['required'],
            'city_id'     => ['required', 'numeric'],
            'image'       => ['nullable', 'mimes:jpeg,png,jpg'],
            // 'image.*'     => ['mimes:jpeg,png,jpg'],
            'video_url'   => ['nullable'],
        ]);

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
                'image' => $imp_image ?? null,
                'video_url' => $request->video_url ?? null,
                'user_id' => 1,
                'city_id' => $request->city_id,
                'status' => 1,
            ]);



            return redirect()->route('birthdayList')->with('message', 'Added Successfully');
        }
    }

    public function getbirthdayEditForm(Request $request, $id)
    {
        $bdata = Birthday::where('id', $id)->first();
        $cityData = City::get();

        return view('admin.Birthday.birthdayEditForm', compact('bdata', 'cityData'));
    }

    public function updatebirthday(Request $request)
    {
        $id = $request->id;
        $birthday = Birthday::where('id', $id)->first();
        if (!empty($birthday)) {
            $validateImageData = $request->validate([
                'title'       => ['required'],
                'description' => ['required'],
                'city_id'     => ['required'],
                'video_url'   => ['nullable'],
            ]);

            $data['title'] = $request->title;
            $data['description'] = $request->description;
            $data['city_id'] = $request->city_id;
            $data['video_url'] = $request->video_url ?? null;
            $updatedata = $birthday->update($data);
        }

        // $apiurl = env('APP_URL') . 'api/birthday/' . $request->id;
        // $response = Http::put($apiurl, [
        //     'role_id' => auth()->user()->role_id,
        //     'title' => $request->title,
        //     'description' => $request->description,
        //     'city_id' => $request->city_id,
        //     'image' => $request->image,
        //     'video_url' => $request->video_url,
        // ]);
        // $newdata =  $response->json();
        return redirect()->route('birthdayList')->with('message', 'Update Successfully');
    }

    public function acceptBday(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/acceptBirthday';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
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
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function addbirthdayImage(Request $request, $id)
    {
        $validateImageData = $request->validate([
            'image'       => ['nullable', 'mimes:jpeg,png,jpg'],
        ]);
        $birthday = Birthday::where('id', $id)->first();
        if (!empty($birthday)) {

            $exp = explode('|', $birthday->image);

            if ($files = $request->file('image')) {
                foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/birthday/';
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
