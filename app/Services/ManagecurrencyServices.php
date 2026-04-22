<?php

namespace App\Services;

use App\Models\CurrancyValue;

class ManagecurrencyServices
{
  public $nis;
  public $dollar;
 


    public  function convertCurrency($nis = null , $dollar = null)
    {
        
        if($nis && !$dollar) {
           
            $currencyValue = CurrancyValue::latest('exchange_date')->first('currency_value');
             
            return  round($nis / $currencyValue->currency_value,2) ;
        }

         
        if($dollar && !$nis) {
           
            $currencyValue = CurrancyValue::latest('exchange_date')->first('currency_value');
          
            return  round($dollar * $currencyValue->currency_value,2) ;
        }

        return null;
       
    }
}
 