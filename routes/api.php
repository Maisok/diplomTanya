<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Branch;



Route::post('/check-address', function(Request $request) {
    $exists = Branch::where('address', $request->address)->exists();
    return response()->json(['unique' => !$exists]);
});


