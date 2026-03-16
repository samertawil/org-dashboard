<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CivilRegistryApiServices
{

    public function __construct(protected CivilRegistryApiResponse $civilRegistryApiResponse)
    {
        //
    }

    public  function getData($identity_number)
    {

        $response = Http::withHeaders([
            'beneficiary' => 'mersal',
            'appkey' => 'uyG6wfbOsxYJKGxGrQVgTvfZ6MFV8xBs0Ct',
        ])->get('https://apisso.gov.ps/api/check-citzen/' . $identity_number);

        return $this->civilRegistryApiResponse->validate($response);
    }
}
