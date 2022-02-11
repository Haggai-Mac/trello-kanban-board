<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spatie\DbDumper\Databases\MySql;

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
   MySql::create()
        ->setDbName(config('database.connections.'.config('database.default'))['database'])
        ->setUserName(config('database.connections.'.config('database.default'))['username'])
        ->setPassword(config('database.connections.'.config('database.default'))['password'])
        ->setHost(config('database.connections.'.config('database.default'))['host'])
        ->dumpToFile('storage/dump.sql');

    if (Storage::disk('public')->exists('dump.sql')) {
        return Storage::disk('public')->download('dump.sql');   
    } else {
        return redirect()->back();
    }
})->name('board.export.db');