<?php

namespace App\Traits;


trait SplitPayment {

  protected function splitPaymentAsperUser($case , $amount , $user_array , $option = []){

    $userAmountArray = [];
    switch ($case) {
      case 'EQUAL':

        //get equal amount
        $amount = (float) $amount;
        $userCount = count($user_array);
        $equalAmount = ($amount / $userCount);
   
        $exp = explode(".", $equalAmount);
        $decimalpart = isset($exp[1]) ? substr($exp[1],0,2) : "00";
        $equalAmount = floatval($exp[0].'.'. $decimalpart );
        //generate array with equal amount
        $userAmountArray = array_fill_keys($user_array , $equalAmount);

        //getDifference in Amount
        $getTotalSplitAmount = (float) $equalAmount * $userCount;
        $differenceWithAmount = (float) ($amount - $getTotalSplitAmount);
        //Add difference to first user 
        if($differenceWithAmount >= 0.0){
            $userAmountArray[$user_array[0]] = $userAmountArray[$user_array[0]] + $differenceWithAmount ;
        }
        
        break;
      
      case 'EXACT':
        # code...
        if(isset($option['exact_user_array'])){
          $userAmountArray = $option['exact_user_array'];
        }
        break;

      case 'PERCENT':

        if(isset($option['percentage_user_array'])){
          //calculate percentage amount
          foreach($option['percentage_user_array'] as $key => $value) { 
            $percentamount = floatval(((float) $value / 100 ) * $amount);
            $exp = explode(".", $percentamount);
            $decimalpart = isset($exp[1]) ? substr($exp[1],0,2) : "00";
            $equalAmount = floatval($exp[0].'.'. $decimalpart );
            $userAmountArray[$key] = $percentamount;
          }
        }
        # code...
        break;
    }
    return $userAmountArray;
   }
}


?>