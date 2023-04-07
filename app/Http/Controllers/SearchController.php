<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Schema;


class SearchController extends Controller
{
    public function searchWordFromWholeDatabase(Request $request)
    {
        dd('search');
        $validator = Validator::make($request->all(), [
            'search' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }
        try {
            $searchTerm = $request->search;

            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

            $selectedTblArr = array_diff($tables, array("advertisments", "cities", "failed_jobs", "migrations", "model_has_permissions", "model_has_roles", "password_reset_tokens", "permissions", "personal_access_tokens", "roles", "role_has_permissions", "sales", "sale_product_lists", "sale_sub_categories", "users", 'premium_ads', 'watermarks'));
            $tblarr = [];
            foreach ($selectedTblArr as $key => $a) {
                array_push($tblarr, $a);
            }

            $results = array();
            foreach ($tblarr as $table) {
                $columns = DB::getSchemaBuilder()->getColumnListing($table);
                foreach ($columns as $column) {
                    $query = DB::table($table)
                        ->where($column, 'LIKE', '%' . $searchTerm . '%')
                        ->get()->toArray();
                    $results = array_merge($results, $query);
                }
            }

            $statusArr = [];
            if (!empty($results)) {
                foreach ($results as $res) {
                    if ($res->status == 1 && $res->city_id != null) {
                        $res->image = str_replace("public", env('APP_URL') . "public",  $res->image);
                        $res->image = explode('|', $res->image);

                        array_push($statusArr, $res);
                    }
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Result on the basis of search term',
                    'data' => $statusArr,
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Empty Result',
                    'data' => [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
