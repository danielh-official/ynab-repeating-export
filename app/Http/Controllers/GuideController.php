<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @codeCoverageIgnore
 */
class GuideController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('guide');
    }
}
