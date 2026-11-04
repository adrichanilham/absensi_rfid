<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WaliMuridController extends Controller
{
    public function dashboard()
    {
        $wali = auth()->user();

        $anak = $wali->anak()
            ->with('kelas')
            ->firstOrFail();

        return view('wali-murid.dashboard', compact('anak'));
    }
}