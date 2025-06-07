<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

define('API_BASE_URL', 'http://localhost:8000/api');

describe('Test the API.', function (): void {
    it('Tests home page.', function (): void {
        $response = $this->get('/');
        $response->assertStatus(200);
    });

    it('Tests registering a user.', function (): void {
        $response = $this->post(API_BASE_URL.'/register', [
            'name' => 'Kalle Anka',
            'email' => 'kalle@anka.se',
            'password' => 'Passw0rd',
        ]);

        $response->assertStatus(201);

        $json = $response->json();

        expect($json['success'])->toBe(true);
    });

    it('Tests registering an already existing user.', function (): void {
        $response = $this->post(API_BASE_URL.'/register', [
            'name' => 'Testuser',
            'email' => 'kalle@anka.se',
            'password' => 'Passw0rd',
        ]);

        $response->assertStatus(422);
    });

    it('Tests registering with invalid password.', function (): void {
        $response = $this->post(API_BASE_URL.'/register', [
            'name' => 'Testuser',
            'email' => 'kalle@anka.se',
            'password' => 'pass',
        ]);

        $response->assertStatus(422);
    });

    it('Tests the login endpoint.', function (): void {
        $response = $this->post(API_BASE_URL.'/login', [
            'email' => 'kalle@anka.se',
            'password' => 'Passw0rd',
        ]);

        $response->assertStatus(200);
    });

    it('Logs in and adds a catch', function (): void {
        $token = logIn($this, API_BASE_URL, 'kalle@anka.se', 'Passw0rd');

        $tokenHeader = "Bearer $token";

        foreach (range(1, 5) as $i) {
            $uniqId = uniqid();
            $fileName = 'mackerel.webp';
            $newFileName = explode('.', $fileName)[0];
            $stub = __DIR__.'/../files/'.$fileName;
            $path = sys_get_temp_dir().'/'.$uniqId.$newFileName;
            copy($stub, $path);

            $file = new UploadedFile($path, $newFileName, 'image/png', false, true);

            $response = $this->post(API_BASE_URL.'/create', [
                'species' => 'TestSpecies',
                'length' => 25,
                'weight' => 250,
                'location' => '57.76776343640755,11.710193958021183',
                'uploadImage' => $file,
                'date' => "2025-06-0$i",
            ], [
                'authorization' => $tokenHeader,
            ]);

            $response->assertStatus(201);
        }
    });

    it('Logs in and updates a catch', function (): void {
        $token = logIn($this, API_BASE_URL, 'kalle@anka.se', 'Passw0rd');
        $tokenHeader = "Bearer $token";

        $response = $this->get(API_BASE_URL.'/public_fishcatch');
        $response->assertStatus(200);
        $data = $response->json();
        $id = $data[0]['id'];
        $newDate = '2024-05-01';

        $uniqId = uniqid();
        $fileName = 'mackerel.webp';
        $newFileName = explode('.', $fileName)[0];
        $stub = __DIR__.'/../files/'.$fileName;
        $path = sys_get_temp_dir().'/'.$uniqId.$newFileName;
        copy($stub, $path);

        $file = new UploadedFile($path, $newFileName, 'image/png', false, true);

        $response = $this->post(API_BASE_URL."/update/$id", [
            'species' => 'TestSpecies',
            'length' => 25,
            'weight' => 250,
            'location' => '57.76776343640755,11.710193958021183',
            'uploadImage' => $file,
            'date' => $newDate,
        ], [
            'authorization' => $tokenHeader,
        ]);

        $response->assertStatus(200);

        $response = $this->get(API_BASE_URL.'/public_fishcatch');
        $response->assertStatus(200);
        $content = $response->getContent();
        expect($content)->toBeJson();
        $data = $response->json();
        expect($data[0]['date'])->toBe($newDate);
    });

    it('Tries to update a catch with invalid data.', function (): void {
        $token = logIn($this, API_BASE_URL, 'kalle@anka.se', 'Passw0rd');
        $tokenHeader = "Bearer $token";

        $response = $this->get(API_BASE_URL.'/public_fishcatch');
        $response->assertStatus(200);
        $data = $response->json();
        $id = $data[0]['id'];

        $response = $this->post(API_BASE_URL."/update/$id", [
            'species' => 12345,
            'length' => 'invalid',
            'weight' => 'invalid',
            'location' => 'invalid',
            'date' => 'invalid',
        ], [
            'authorization' => $tokenHeader,
        ]);

        $response->assertStatus(400);
    });

    it('Tries to update non existant fishCatch.', function (): void {
        $token = logIn($this, API_BASE_URL, 'kalle@anka.se', 'Passw0rd');
        $tokenHeader = "Bearer $token";

        $id = -1;

        $response = $this->post(API_BASE_URL."/update/$id", [
            'species' => 'Test',
            'length' => 12,
            'weight' => 34,
            'location' => '57.76776343640755,11.710193958021183',
            'date' => '2025-06-10',
        ], [
            'authorization' => $tokenHeader,
        ]);

        $response->assertStatus(404);
    });

    it('Tests public fishcatch', function (): void {
        $response = $this->get(API_BASE_URL.'/public_fishcatch');
        $response->assertStatus(200);
        $content = $response->getContent();
        expect($content)->toBeJson();
        $data = $response->json();
        expect($data[array_key_last($data)]['species'])->toBe('TestSpecies');
    });

    it('Gets data for a specific fishcatch', function (): void {
        $response = $this->get(API_BASE_URL.'/public_fishcatch');
        $data = $response->json();
        $lastId = $data[array_key_last($data)]['id'];

        $token = logIn($this, API_BASE_URL, 'kalle@anka.se', 'Passw0rd');
        $tokenHeader = "Bearer $token";

        $response = $this->get(API_BASE_URL."/fishcatch/$lastId", [
            'authorization' => $tokenHeader,
        ]);
        $data = $response->json();
        expect($data['species'])->toBe('TestSpecies');
    });

    it('Tries to get data for a non existant fishcatch', function (): void {
        $id = -2;

        $token = logIn($this, API_BASE_URL, 'kalle@anka.se', 'Passw0rd');
        $tokenHeader = "Bearer $token";

        $response = $this->get(API_BASE_URL."/fishcatch/$id", [
            'authorization' => $tokenHeader,
        ]);

        $response->assertStatus(404);
    });

    it('Deletes a fishcatch', function (): void {
        $response = $this->get(API_BASE_URL.'/public_fishcatch');
        $data = $response->json();
        $lastId = $data[array_key_last($data)]['id'];

        $token = logIn($this, API_BASE_URL, 'kalle@anka.se', 'Passw0rd');
        $tokenHeader = "Bearer $token";

        $response = $this->delete(
            API_BASE_URL."/delete/$lastId",
            [],
            ['authorization' => $tokenHeader]
        );

        $response->assertStatus(204);
    });

    it('Tries to delete a non existant fishCatch.', function (): void {
        $token = logIn($this, API_BASE_URL, 'kalle@anka.se', 'Passw0rd');
        $tokenHeader = "Bearer $token";

        $id = -1;

        $response = $this->delete(
            API_BASE_URL."/delete/$id",
            [],
            ['authorization' => $tokenHeader]
        );

        $response->assertStatus(404);
    });

    it('Logs in and logs out.', function (): void {
        $token = logIn($this, API_BASE_URL, 'kalle@anka.se', 'Passw0rd');
        $tokenHeader = "Bearer $token";

        $response = $this->post(
            API_BASE_URL.'/logout',
            ['token' => $token],
            ['authorization' => $tokenHeader]
        );

        $response->assertStatus(200);
    });

    it('Deletes test data.', function (): void {
        DB::table('fish_catches')->where('species', '=', 'TestSpecies')->delete();
        DB::table('users')->where('name', '=', 'Kalle Anka')->delete();

        $testCatches = DB::table('fish_catches')->where('species', '=', 'TestSpecies')->get();
        $testUsers = DB::table('users')->where('name', '=', 'Kalle Anka')->get();

        expect(count($testCatches))->toBe(0);
        expect(count($testUsers))->toBe(0);
    });
});
