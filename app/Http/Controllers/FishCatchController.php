<?php
/**
 * Authentication required.
 */

namespace App\Http\Controllers;

use App\Models\FishCatch;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FishCatchController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->user
            ->fishCatches()
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $data = $request->only('species', 'length', 'weight', 'date', 'location', 'uploadImage');
        $validator = Validator::make($data, [
            'species' => 'required|string',
            'length' => 'required|integer',
            'weight' => 'required|integer',
            'date' => 'required|date',
            'location' => 'required|string',
            'uploadImage' => 'required|image'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Save the image
        $imageurl = $request->file("uploadImage")->store("public");
        $imageurl = Storage::url($imageurl);

        try {
            //Request is valid, create new fishcatch
            $fishCatch = $this->user->fishCatches()->create([
                'species' => $request->species,
                'length' => $request->length,
                'weight' => $request->weight,
                'date' => $request->date,
                'location' => $request->location,
                'imageurl' => $imageurl
            ]);
        } catch (\Exception $err) {
            return $err->getMessage();
        }

        //Fishcatch created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Fishcatch created successfully',
            'data' => $fishCatch
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FishCatch  $fishCatch
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fishCatch = $this->user->fishCatches()->find($id);
    
        if (!$fishCatch) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, item not found.'
            ], 400);
        }
    
        return $fishCatch;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FishCatch  $fishCatch
     * @return \Illuminate\Http\Response
     */
    public function edit(FishCatch $fishCatch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validate data
        $data = $request->only('species', 'length', 'weight', 'date', 'location');
        $validator = Validator::make($data, [
            'species' => 'required|string',
            'length' => 'required',
            'weight' => 'required',
            'date' => 'required',
            'location' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $fishCatch = $this->user->fishCatches()->find($id);

        if (!$fishCatch) {
            return response()->json(['message' => "Sorry, item not found."], 400);
        }

        //Request is valid, update fishcatch
        $fishCatch->update([
            'species' => $request->species,
            'length' => $request->length,
            'weight' => $request->weight,
            'date' => $request->date,
            'location' => $request->location
        ]);

        //Fishcatch updated, return success response
        return response()->json([
            'success' => true,
            'message' => 'Fishcatch updated successfully',
            'data' => $fishCatch
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        $fishCatch = $this->user->fishCatches()->find($id);

        if (!$fishCatch) {
            return response()->json(['message' => "Sorry, item not found."], 400);
        }

        $fishCatch->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Fishcatch deleted successfully'
        ], Response::HTTP_OK);
    }
}