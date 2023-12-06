<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\SiteData;

class SiteDataController extends Controller
{
    public function show()
    {
        return SiteData::first();
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'faq' => 'required',
            'terms_and_conditions' => 'required',
            'privacy_policy' => 'required',
            'contact_data' => 'required',
        ]);

        $siteData = SiteData::firstOrCreate([]);
        $siteData->update($data);

        return response(['site_data' => $siteData]);
    }
}
