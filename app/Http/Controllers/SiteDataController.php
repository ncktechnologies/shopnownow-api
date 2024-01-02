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


    public function create(Request $request)
    {
        $data = $request->validate([
            'faq' => 'sometimes|required|array',
            'faq.*.question' => 'required|string',
            'faq.*.answer' => 'required|string',
            'terms_and_conditions' => 'sometimes|required',
            'privacy_policy' => 'sometimes|required',
            'contact_data' => 'sometimes|required',
        ]);

        $siteData = SiteData::create($data);

        return response(['site_data' => $siteData]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'faq' => 'sometimes|required|array',
            'faq.*.question' => 'sometimes|required|string',
            'faq.*.answer' => 'sometimes|required|string',
            'terms_and_conditions' => 'sometimes|required',
            'privacy_policy' => 'sometimes|required',
            'contact_data' => 'sometimes|required',
        ]);

        $siteData = SiteData::firstOrCreate([], [
            'faq' => [],
            'terms_and_conditions' => '',
            'privacy_policy' => '',
            'contact_data' => '',
        ]);
        $siteData->update($data);

        return response(['site_data' => $siteData]);
    }
}
