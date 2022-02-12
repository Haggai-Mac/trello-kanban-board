<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Ifsnop\Mysqldump as IMysqldump;

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
    return view('board');
})->name('board');

Route::get('export', function () {
    $filename = 'dump.sql';
    try {
        $dump = new IMysqldump\Mysqldump('mysql:host=' . config('database.connections.'.config('database.default'))['host'] . ';dbname=' . config('database.connections.'.config('database.default'))['database'], config('database.connections.'.config('database.default'))['username'], config('database.connections.'.config('database.default'))['password']);
        $dump->start($filename);

        if (File::exists(public_path($filename))) {
            return response()->download(public_path($filename));
        } else {
            return redirect()->back();
        }
    } catch (\Exception $e) {
        Log::error($e);
        return redirect()->back();
    }
})->name('board.export.db');