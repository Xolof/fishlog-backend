<?php

/**
 * No authentication required.
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Display a list of all catches.
     *
     * @return array<\App\Models\FishCatch>
     */
    public function index(): array
    {
        $fishCatches = DB::table('fish_catches')
            ->leftJoin('users', 'fish_catches.user_id', '=', 'users.id')
            ->select('fish_catches.id', 'species', 'length', 'weight', 'date', 'location', 'imageurl', 'name as username')
            ->get()->toArray();

        foreach ($fishCatches as $arr) {
            foreach ($arr as &$val) {
                $val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
            }
        }

        return $fishCatches;
    }
}
