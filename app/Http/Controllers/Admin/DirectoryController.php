<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\Directory;
use App\Models\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class DirectoryController extends Controller
{
    public function directoryList()
    {
        $apiurl = env('APP_URL') . 'api/directoryListOfUser';
        $response = Http::get($apiurl, [
            'role_id' => auth()->user()->role_id,
        ]);
        $newdata =  $response->json($key = null, $default = null);
        $birthdayData = $newdata['data'];
        return view('admin.Directory.index', compact('birthdayData'));
    }


    public function directoryImage($id)
    {
        $bdata = Directory::where('id', $id)->first();
        $bdata->image = str_replace("public", env('APP_URL') . "public", $bdata->image);


        $exp = explode('|', $bdata->image);
        // $key = array_search("", $exp);
        // unset($exp[$key]);
        return view('admin.Directory.directoryImage', compact('exp', 'id'));
    }

    public function getdirectoryForm()
    {
        $cityData = City::get();
        return view('admin.Directory.directoryForm', compact('cityData'));
    }

    public function adddirectory(Request $request)
    {
        $validateImageData = $request->validate([
            'city_id'      => ['required'],
            'biz_name'     => ['required'],
            'contact_per1' => ['nullable'],
            'number1'      => ['nullable'],
            'category'     => ['nullable'],
            'city'         => ['nullable'],
            'state'        => ['nullable'],
            'contact_per2' => ['nullable'],
            'contact_per3' => ['nullable'],
            'number2'      => ['nullable'],
            'number3'      => ['nullable'],
            'address'      => ['nullable'],
            'detail'       => ['nullable'],
            // 'image'        => ['nullable'],
            'image.*'      => ['nullable', 'mimes:jpeg,png,jpg'],
        ]);

        $city = City::where('id', $request->city_id)->first();

        if (!empty($city)) {
            $images = array();
            if ($files = $request->file('image')) {
                foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/directory/';
                    $img_url = $upload_path . $img_full_name;
                    $file->move($upload_path, $img_full_name);
                    array_push($images, $img_url);
                }
            }
            $imp_image =  implode('|', $images);
            $birthday = Directory::create([
                'biz_name' => $request->biz_name,
                'contact_per1' => $request->contact_per1,
                'number1' => $request->number1,
                'category' => $request->category,
                'city' => $request->city,
                'state' => $request->state,
                'contact_per2' => $request->contact_per2,
                'contact_per3' => $request->contact_per3,
                'number2' => $request->number2,
                'number3' => $request->number3,
                'address' => $request->address,
                'detail' => $request->detail,
                'user_id' => 1,
                'image' => $imp_image ?? null,
                'city_id' => $request->city_id,
                'status' => 1,
            ]);



            return redirect()->route('directoryList')->with('message', 'Added Successfully');
        }
    }

    public function getdirectoryEditForm(Request $request, $id)
    {
        $bdata = Directory::where('id', $id)->first();
        $cityData = City::get();

        return view('admin.Directory.directoryEditForm', compact('bdata', 'cityData'));
    }

    public function updatedirectory(Request $request)
    {
        $id = $request->id;
        $birthday = Directory::where('id', $id)->first();
        if (!empty($birthday)) {
            $validateImageData = $request->validate([
                'city_id'      => ['required'],
                'biz_name'     => ['required'],
                'contact_per1' => ['nullable'],
                'number1'      => ['nullable'],
                'category'     => ['nullable'],
                'city'         => ['nullable'],
                'state'        => ['nullable'],
                'contact_per2' => ['nullable'],
                'contact_per3' => ['nullable'],
                'number2'      => ['nullable'],
                'number3'      => ['nullable'],
                'address'      => ['nullable'],
                'detail'       => ['nullable'],
                // 'image'        => ['nullable'],
                'image.*'      => ['nullable', 'mimes:jpeg,png,jpg'],
            ]);

            $data['biz_name'] = $request->biz_name;
            $data['contact_per1'] = $request->contact_per1;
            $data['number1'] = $request->number1;
            $data['category'] = $request->category;
            $data['city'] = $request->city;
            $data['state'] = $request->state;
            $data['contact_per2'] = $request->contact_per2;
            $data['contact_per3'] = $request->contact_per3;
            $data['number2'] = $request->number2;
            $data['number3'] = $request->number3;
            $data['address'] = $request->address;
            $data['detail'] = $request->detail;
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
        return redirect()->route('directoryList')->with('message', 'Update Successfully');
    }

    public function acceptdirectory(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/acceptDirectory';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function denydirectory(Request $request)
    {
        $apiurl = env('APP_URL') . 'api/denyDirectory';
        $response = Http::post($apiurl, [
            'role_id' => auth()->user()->role_id,
            'id' => $request->id,
        ]);
        $newdata =  $response->json();
        $message = $newdata['message'];
        return redirect()->back()->with('message', $message);
    }

    public function adddirectoryImage(Request $request, $id)
    {
        $validateImageData = $request->validate([
            'image.*'       => ['nullable', 'mimes:jpeg,png,jpg'],
        ]);
        $birthday = Directory::where('id', $id)->first();
        if (!empty($birthday)) {

            $exp = explode('|', $birthday->image);

            if ($files = $request->file('image')) {
                foreach ($files as $file) {
                    $imgname = md5(rand('1000', '10000'));
                    $extension = strtolower($file->getClientOriginalExtension());
                    $img_full_name = $imgname . '.' . $extension;
                    $upload_path = 'public/directory/';
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
