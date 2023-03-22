<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class SearchController extends Controller
{
    public function searchWordFromWholeDatabase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $searchTerm = $request->search;
            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $selectedTblArr = array_diff($tables, array("advertisments", "cities", "failed_jobs", "migrations", "model_has_permissions", "model_has_roles", "password_reset_tokens", "permissions", "personal_access_tokens", "roles", "role_has_permissions", "sales", "sale_product_lists", "sale_sub_categories"));
            $tblarr = [];
            foreach ($selectedTblArr as $key => $a) {
                array_push($tblarr, $a);
            }
            $results = collect(); // Initialize an empty collection to store the search results
            foreach ($tblarr as $table) {
                $columns = DB::getSchemaBuilder()->getColumnListing($table);
                foreach ($columns as $column) {
                    $query = DB::table($table)
                        ->where($column, 'LIKE', '%' . $searchTerm . '%')
                        ->get();
                    $results = $results->merge($query); // Add the query results to the collection of search results

                }
            }
            if (!empty($results)) {
                return response()->json([
                    'message' => 'Result on the basis of search term',
                    'data' => $results,
                ]);
            } else {
                return response()->json([
                    'message' => 'Empty',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}