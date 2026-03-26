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
            'beneficiary' =>  config('services.civil_registry_api.beneficiary'),
            'appkey' =>  config('services.civil_registry_api.appkey'),
        ])->get('https://apisso.gov.ps/api/check-citzen/' . $identity_number);

        return $this->civilRegistryApiResponse->validate($response);
    }
}
