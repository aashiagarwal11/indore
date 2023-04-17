<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\ShokSuchna;
use App\Models\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class ShoksuchnaController extends Controller
{
    public function shoksuchnaList()
    {
        $apiurl = env('APP_URL') . 'api/showAllOnAdmin';
        $response = Http::get($apiurl, [
            'role_id' => auth()->user()->role_id,
        ]);
        $newdata =  $response->json($key = null, $default = null);
        $birthdayData = $newdata['data'];
        return view('admin.Shoksuchna.index', compact('birthdayData'));
    }

    public function shoksuchnaImage($id)
    {
        $bdata = ShokSuchna::where('id', $id)->first();
        $bdata->image = str_replace("public", env('APP_URL') . "public", $bdata->image);

        if ($bdata->image == "") {
            $exp = null;
        } else {
            $exp = explode('|', $bdata->image);
            $key = array_search("", $exp);
            unset($exp[$key]);
        }
        return view('admin.Shoksuchna.shoksuchnaImage', compact('exp', 'id'));
    }

    public function getshoksuchnaForm()
    {
        $cityData = City::get();
        return view('admin.Shoksuchna.shoksuchnaForm', compact('cityData'));
    }

    public function addshoksuchna(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => ['required', 'string'],
            'description' => ['required'],
            'city_id'     => ['required', 'numeric'],
            'image'       => ['nullable'],
            'image.*'     => ['mimes:jpeg,png,jpg'],
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
                    $upload_path = 'public/shoksuchna/';
                    $img_url = $upload_path . $img_full_name;
                    $file->move($upload_path, $img_full_name);
                    array_push($images, $img_url);
                }
            }
            $imp_image =  implode('|', $images);

            $birthday = ShokSuchna::create([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $imp_image ?? null,
                'user_id' => 1,
                'city_id' => $request->city_id,
                'status' => 1,
            ]);

            return redirect()->route('shoksuchnaList')->with('message', 'Added Successfully');
        }
    }

    public function getshoksuchnaEditForm(Request $request, $id)
    {
        $bdata = ShokSuchna::where('id', $id)->first();
        $cityData = City::get();

        return view('admin.Shoksuchna.shoksuchnaEditForm', compact('bdata', 'cityData'));
    }

    public function updateshoksuchna(Request $request)
    {
        $id = $request->id;
        $birthday = ShokSuchna::where('id', $id)->first();
        if (!empty($birthday)) {
            $validator = Validator::make($request->all(), [
                'title'       => ['required', 'string'],
                'description' => ['required'],
                'city_id'     => ['required', 'numeric'],
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()]);
            }

            $data['title'] = $request->title;
            $data['description'] = $request->description;
            $data['city_id'] = $request->city_id;
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
        return redirect()->route('shoksuchnaList')->with('message', 'Update Successfully');
    }

    public function acceptshoksuchna(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/acceptShokSuchna';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,   ## 1
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function denyshoksuchna(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/denyShokSuchna';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,   ## 1
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function addshoksuchnaImage(Request $request, $id)
    {
        $birthday = ShokSuchna::where('id', $id)->first();
        if (!empty($birthday)) {

            $exp = explode('|', $birthday->image);

            if ($files = $request->file('image')) {
                foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/shoksuchna/';
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