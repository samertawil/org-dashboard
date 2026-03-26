<?php

namespace App\Services;

use App\Exceptions\CivilRegistryException;
use App\Models\Student;
use Illuminate\Http\Client\Response;

class CivilRegistryApiResponse
{
    public function validate(Response $response)
    {

        if (! $response->successful()) {

            $message = __($response->json()['message']);
            throw new CivilRegistryException($message);
        }

        if ($response->json()['data']['birth_date']  > Student::maxBirthDate() || $response->json()['data']['birth_date'] < Student::class::minBirthDate()) {

            $message = __('The students age does not match the standards, must be before or equal ' . Student::maxBirthDate() . ' and after or equal ' . Student::minBirthDate());
            throw new CivilRegistryException($message);
        }
       
        return $response->json();
    }
}
// now()->subYears(6)->format('Y-m-d')