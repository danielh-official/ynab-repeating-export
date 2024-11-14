<?php

declare(strict_types=1);

namespace App\Actions;

use Illuminate\Http\Request;

class BuildFileName
{
    public function handle(Request $request)
    {
        $fileExtension = $request->input('file_extension', 'csv');

        if ($fileExtension === 'csv') {
            $fileStringExtension = 'csv';
        } elseif ($fileExtension === 'excel') {
            $fileStringExtension = 'xlsx';
        } else {
            $fileStringExtension = 'csv';
        }

        $today = now()->format('Y-m-d');

        return "$today-ynab-repeating-transactions.$fileStringExtension";
    }
}
