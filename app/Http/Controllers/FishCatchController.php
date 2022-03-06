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
use Image;

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
            'species' => 'required|string|alpha_num',
            'length' => 'required|integer|gt:0',
            'weight' => 'required|integer|gt:0',
            'date' => 'required|date',
            'location' => 'required|string',
            'uploadImage' => 'required|image'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        // Save the image
        $uploadimage = $request->file("uploadImage");
        $filename = explode("/", $uploadimage)[2];
        $saveurl = public_path("/storage" . "/" . $filename . ".webp");
        $image = Image::make($uploadimage)->encode("webp", 90)
            ->resize(500, 500, function($constraint) {
                $constraint->aspectRatio();
            })->save($saveurl);

        $imageurl = "/storage" . "/" . $filename . ".webp";

        try {
            //Request is valid, create new fishcatch
            $fishCatch = $this->user->fishCatches()->create([
                'species' => strip_tags($request->species),
                'length' => strip_tags($request->length),
                'weight' => strip_tags($request->weight),
                'date' => strip_tags($request->date),
                'location' => strip_tags($request->location),
                'imageurl' => strip_tags($imageurl)
            ]);
        } catch (\Exception $err) {
            return response()->json([
                "error" => [
                    "Something went wrong, please check your data."
                ]
            ]);
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
        $data = $request->only('species', 'length', 'weight', 'date', 'location', 'uploadImage');

        $validator = Validator::make($data, [
            'species' => 'required|string|alpha_num',
            'length' => 'required|integer|gt:0',
            'weight' => 'required|integer|gt:0',
            'date' => 'required|date',
            'location' => 'required|string',
            'uploadImage' => 'image'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        $fishCatch = $this->user->fishCatches()->find($id);

        if (!$fishCatch) {
            return response()->json(['message' => "Sorry, item not found."], 400);
        }

        $updateData = [
            'species' => strip_tags($request->species),
            'length' => strip_tags($request->length),
            'weight' => strip_tags($request->weight),
            'date' => strip_tags($request->date),
            'location' => strip_tags($request->location)
        ];

        $uploadimage = $request->file("uploadImage");
        if ($uploadimage) {
            // Save the image
            $filename = explode("/", $uploadimage)[2];
            $saveurl = public_path("/storage" . "/" . $filename . ".webp");
            $image = Image::make($uploadimage)->encode("webp", 90)
                ->resize(500, 500, function($constraint) {
                    $constraint->aspectRatio();
                })->save($saveurl);

            $imageurl = "/storage" . "/" . $filename . ".webp";
            $updateData['imageurl'] = strip_tags($imageurl);
        }

        try {
            //Request is valid, update fishcatch
            $fishCatch->update($updateData);
        } catch (\Exception $err) {
            return response()->json([
                "errors" => [
                    "Something went wrong, please check your data."
                ]
            ]);
        }

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