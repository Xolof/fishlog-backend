<?php
/**
 * No authentication required.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Display a list of all catches.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fishCatches = DB::table('fish_catches')
            ->leftJoin("users", "fish_catches.user_id", "=", "users.id")
            ->select("species", "length", "weight", "date", "location", "name as username")
            ->get();

        return $fishCatches;
    }
}