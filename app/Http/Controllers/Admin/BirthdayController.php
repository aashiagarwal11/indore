<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $apiurl = env('APP_URL') . 'api/addbirthdayViaAdmin';
        $response = Http::post($apiurl, [
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
        // dd($newdata);
        return redirect()->route('birthdayList');
    }
}
