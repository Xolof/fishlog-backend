<?php

/**
 * Authentication required.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\JWTAuth;

class FishCatchController extends Controller
{
    protected $user;

    public function __construct(JWTAuth $jwtAuth)
    {
        $this->user = $jwtAuth->parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fishCatches = $this->user
            ->fishCatches()
            ->get();

        foreach ($fishCatches as $catch) {
            $catch["species"] = htmlspecialchars($catch["species"]);
        }

        return $fishCatches;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Image $image)
    {
        $data = $request->only('species', 'length', 'weight', 'date', 'location', 'uploadImage');

        $validator = Validator::make($data, [
            'species' => 'required|string|alpha_num',
            'length' => 'required|integer|gt:0',
            'weight' => 'required|integer|gt:0',
            'date' => 'required|date',
            'location' => 'required|string|regex:/^\d{2}.\d{10,15},\d{2}.\d{10,15}$/',
            'uploadImage' => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        $uploadimage = $request->file('uploadImage');
        $filename = explode('/', $uploadimage)[2];
        $saveurl = public_path('/storage'.'/'.$filename.'.webp');
        $image = $image::make($uploadimage)->encode('webp', 90)
            ->resize(500, 500, function ($constraint): void {
                $constraint->aspectRatio();
            })->save($saveurl);

        $imageurl = '/storage'.'/'.$filename.'.webp';

        try {
            $fishCatch = $this->user->fishCatches()->create([
                'species' => strip_tags($request->species),
                'length' => strip_tags($request->length),
                'weight' => strip_tags($request->weight),
                'date' => strip_tags($request->date),
                'location' => strip_tags($request->location),
                'imageurl' => strip_tags($imageurl),
            ]);
        } catch (\Exception $err) {
            return response()->json([
                'error' => ['Something went wrong, please check your data.'],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fishcatch created successfully',
            'data' => $fishCatch,
        ], 201);
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

        if (! $fishCatch) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found.',
            ], 404);
        }

        return $fishCatch;
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, Image $image)
    {
        $data = $request->only('species', 'length', 'weight', 'date', 'location', 'uploadImage');

        $validator = Validator::make($data, [
            'species' => 'required|string|alpha_num',
            'length' => 'required|integer|gt:0',
            'weight' => 'required|integer|gt:0',
            'date' => 'required|date',
            'location' => 'required|string|regex:/^\d{2}.\d{10,15},\d{2}.\d{10,15}$/',
            'uploadImage' => 'image',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }

        $fishCatch = $this->user->fishCatches()->find($id);

        if (! $fishCatch) {
            return response()->json(['message' => 'Item not found.'], 404);
        }

        $updateData = [
            'species' => strip_tags($request->species),
            'length' => strip_tags($request->length),
            'weight' => strip_tags($request->weight),
            'date' => strip_tags($request->date),
            'location' => strip_tags($request->location),
        ];

        $uploadimage = $request->file('uploadImage');
        if ($uploadimage) {
            $filename = explode('/', $uploadimage)[2];
            $saveurl = public_path('/storage'.'/'.$filename.'.webp');
            $image = $image::make($uploadimage)->encode('webp', 90)
                ->resize(500, 500, function ($constraint): void {
                    $constraint->aspectRatio();
                })->save($saveurl);

            $imageurl = '/storage'.'/'.$filename.'.webp';
            $updateData['imageurl'] = strip_tags($imageurl);
        }

        try {
            $fishCatch->update($updateData);
        } catch (\Exception $err) {
            return response()->json([
                'errors' => [
                    'Something went wrong, please check your data.',
                ],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fishcatch updated successfully',
            'data' => $fishCatch,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fishCatch = $this->user->fishCatches()->find($id);

        if (! $fishCatch) {
            return response()->json(['message' => 'Item not found.'], 404);
        }

        $fishCatch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fishcatch deleted successfully',
        ], 204);
    }
}
