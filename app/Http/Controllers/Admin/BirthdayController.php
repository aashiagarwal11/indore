<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\Birthday;
use App\Models\City;

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
        // dd($request->all());
        $apiurl = env('APP_URL') . 'api/addbirthdayViaAdmin';
        // if ($files = $request->file('image')) {
        // $images = array();
        // foreach ($files as $file) {
        //     $imgname = md5(rand('1000', '10000'));
        //     $extension = strtolower($file->getClientOriginalExtension());
        //     $img_full_name = $imgname . '.' . $extension;
        //     $upload_path = 'public/birthday/';
        //     $img_url = $upload_path . $img_full_name;
        //     $file->move($upload_path, $img_full_name);



        // if ($request->hasFile('fileupload')) {
        //     $images = [];
        //     foreach ($request->file('fileupload') as $file) {
        //         if (file_exists($file)) {
        //             $name = $file->getClientOriginalName();
        //             // $images[] = $name;
        //         }
        //     }
        // }

        // $response = Http::attach(
        //     $images,
        //     $request->file('image')
        // )->post($apiurl, $request->all());

        // get Illuminate\Http\UploadedFile instance

        //   try {
        // $images = $request->file('image');
        //     $postData = [
        //         'title' => $request->input('title'),
        //         'description' => $request->input('description'),
        //         'city_id' => $request->input('city_id'),
        //         'video_url' => $request->input('video_url'),
        //         'role_id' => auth()->user()->role_id,
        //     ];

        //     /** @var PendingRequest $http */
        //     $http = Http::withHeaders([
        //             'Content-Type' => 'multipart/form-data',
        //         ])
        //         ->withOptions([
        //             'multipart' => [],
        //         ]);

        // foreach ($images as $image) {
        //     $http->attach('image[]', $image);
        // }

        //     $http->withBody(http_build_query($postData), 'multipart');

        //     $response = $http->post($apiurl);
        //     echo "<pre>";print_r($response);die;
        //     // Handle successful response
        // } catch (\Exception $e) {
        //     $error_message = $e->getMessage();
        //     echo "<pre>";print_r($error_message);die;
        //     // Handle error message
        // }


        
        
        
        



        // $newdata =  $response->json();
        // dd($newdata);
        // }
        // $response = Http::post($apiurl, [
        //     'role_id' => auth()->user()->role_id,
        //     'title' => $request->title,
        //     'description' => $request->description,
        //     'city_id' => $request->city_id,
        //     'image' => $request->image,
        //     'video_url' => $request->video_url,
        // ]);
        // $newdata =  $response->json();
        // dd($newdata);
        return redirect()->route('birthdayList');
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
