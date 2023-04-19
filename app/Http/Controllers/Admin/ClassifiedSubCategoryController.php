<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use App\Models\KrishiMandiBhav;
use App\Models\Sale;
use App\Models\SaleSubCategory;
use App\Models\City;
use Illuminate\Support\Facades\Validator;


class ClassifiedSubCategoryController extends Controller
{
    public function classifiedSubCategoryList()
    {
        $apiurl = env('APP_URL') . 'api/sellsubcategory';
        $response = Http::get($apiurl, [
            'role_id' => auth()->user()->role_id,
        ]);
        $newdata =  $response->json($key = null, $default = null);
        $birthdayData = $newdata['data'];
        return view('admin.SubCategory.index', compact('birthdayData'));
    }

    public function getclassifiedSubCategoryForm()
    {
        $cityData = Sale::get();
        return view('admin.SubCategory.subcategoryForm', compact('cityData'));
    }

    public function addclassifiedSubCategory(Request $request)
    {
        $validateImageData = $request->validate([
            'sale_id'     => ['required'],
            'sub_type'    => ['required']
        ]);

        $chksale = Sale::where('id', $request->sale_id)->first();
        if (!empty($chksale)) {
            $chk_sale_id = SaleSubCategory::where('sub_type', $request->sub_type)->first();
            if (empty($chk_sale_id)) {
                $sale_sub_type = SaleSubCategory::create([
                    'sale_id' => $request->sale_id,
                    'sub_type' => $request->sub_type,
                ]);
                return redirect()->route('classifiedSubCategoryList')->with('message', 'Added Successfully');
            } else {
                return redirect()->back()->with('message', 'Sub Category Already Exist');
            }
        }
    }

    public function getclassifiedSubCategoryEditForm(Request $request, $id)
    {
        $bdata = SaleSubCategory::where('id', $id)->first();
        $cityData = Sale::get();

        return view('admin.SubCategory.subcategoryEditForm', compact('bdata', 'cityData'));
    }

    public function updateclassifiedSubCategory(Request $request)
    {
        $validateImageData = $request->validate([
            'sale_id' => ['required'],
            'sub_type' => ['required'],
        ]);

        $id = $request->id;
        $chk_id = SaleSubCategory::where('id', $id)->first();
        if (!empty($chk_id)) {
            $chk_sale_id = SaleSubCategory::where('sub_type', $request->sub_type)->where('id', '!=', $id)->first();
            if (empty($chk_sale_id)) {
                $updatesale = $chk_id->update([
                    'sale_id' => $request->sale_id,
                    'sub_type' => $request->sub_type,
                ]);
                return redirect()->route('classifiedSubCategoryList')->with('message', 'Updated Successfully');
            } else {
                return redirect()->back()->with('message', 'Sub Category Already Exist');
            }
        }
    }

    public function deleteclassifiedSubCategory($id)
    {
        $del = SaleSubCategory::where('id', $id)->first();
        $del->delete();
        return redirect()->route('classifiedSubCategoryList')->with('message', 'Deleted Successfully');
    }
}
