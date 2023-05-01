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
use App\Models\Watermark;
use Image;



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

        $exp = explode('|', $bdata->image);
        // $key = array_search("", $exp);
        // unset($exp[$key]);
        return view('admin.Shoksuchna.shoksuchnaImage', compact('exp', 'id'));
    }

    public function getshoksuchnaForm()
    {
        $cityData = City::get();
        return view('admin.Shoksuchna.shoksuchnaForm', compact('cityData'));
    }

    public function addshoksuchna(Request $request)
    {
        $validateImageData = $request->validate([
            'title'       => ['required', 'string'],
            'description' => ['required'],
            'city_id'     => ['required', 'numeric'],
            // 'image'       => ['nullable', 'mimes:jpeg,png,jpg'],
            'image.*'     => ['nullable', 'mimes:jpeg,png,jpg'],
        ]);

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

                    ## insert watermark
                    // $wimage = Watermark::first();
                    // // dd($wimage);

                    // $waterMarkUrl = $wimage->image;
                    // if (!empty($waterMarkUrl)) {
                    //     $imgFile = Image::make($file->getRealPath());
                    //     $imgFile->insert($waterMarkUrl, 'bottom-right', 5, 5, function ($font) {
                    //         $font->width(10);
                    //         $font->hright(2);
                    //     });
                    //     $imgFile->save($img_url);
                    // }



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
            $validateImageData = $request->validate([
                'title'       => ['required'],
                'description' => ['required'],
                'city_id'     => ['required'],
            ]);


            $data['title'] = $request->title;
            $data['description'] = $request->description;
            $data['city_id'] = $request->city_id;
            $updatedata = $birthday->update($data);
        }
        return redirect()->route('shoksuchnaList')->with('message', 'Update Successfully');
    }

    public function acceptshoksuchna(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/acceptShokSuchna';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
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
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function addshoksuchnaImage(Request $request, $id)
    {

        $validateImageData = $request->validate([
            'image.*'       => ['nullable', 'mimes:jpeg,png,jpg'],
        ]);
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

    public function deleteshoksuchnaImage(Request $request)
    {
        $get =  ShokSuchna::where('id', $request->id)->first();
        $exp = explode('|', $get->image);
        unset($exp[$request->key]);
        $imp = implode('|', $exp);
        $get->image = $imp;
        $data = ShokSuchna::where('id', $request->id)->update(['image' => $imp]);
        return response()->json(['data' => $data, 'message' => 'Deleted Successfully']);
    }
}
