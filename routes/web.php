<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});


Route::post('/uploadimage', function (Request $request) {
    $image = $request->file('image');
    
    if (!getimagesize($image)) {
        return 'invalid image file';
    }

    $img = Image::make($image);
    $img->resize(200, null, function ($constraint) {
        $constraint->aspectRatio();
    });
    $img->save(public_path('uploads/'.$image->getClientOriginalName()));
    
    $client = new Client();
    $response = $client->request('POST', 'https://codelime.in/api/remind-app-token', [
        'multipart' => [
            [
                'name' => 'image',
                'contents' => fopen(public_path('uploads/'.$image->getClientOriginalName()), 'r')
            ]
        ]
    ]);

    return 'image uploaded successfully';
});



