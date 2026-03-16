<?php

namespace App\Services;

use App\Exceptions\CivilRegistryException;
use Illuminate\Http\Client\Response;

class CivilRegistryApiResponse
{
     public function validate(Response $response) {

        if (! $response->successful()) {

            $message = __($response->json()['message']);
            throw new CivilRegistryException($message);
        }
      
        
        return $response->json();
     }
}
