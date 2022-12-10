<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PercentageExpenseRule implements Rule
{

    protected $data = [];

    protected $errorMessage = "";

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->checkPercentageUserExist($value) === true){
            if($this->checkCountOfUserInPercentageUserArray($value) === true){
                if($this->checkPercentageUptoTwodecimalInPercentageUserArray($value) === true){
                    return $this->checkPercentWithPercentageUserArray($value);
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }

    //check  if User of percent user array exit in user array 
    public function checkPercentageUserExist($value){
        $userArray = $this->data['user_array'];
        $getExactUserId = array_keys($value);
        foreach ($getExactUserId as $userId) {
            if(in_array($userId , $userArray)){
                continue;
            }else{
                $this->errorMessage = "In :attribute feild user id doesnot match user array";
                break;
            }
        }

        if(!empty($this->errorMessage)){
            return false;
        }else{
            return true;
        }
    }

    //check if  percent user arrary count should be same as user array 
    public function checkCountOfUserInPercentageUserArray($value){
        $userArray = count($this->data['user_array']);
        $getExactUserId = count(array_keys($value));
        $difference = $userArray - $getExactUserId;
        if($difference == 0){
            return true;
        }else{
            $this->errorMessage =  " In :attribute feild some user id are missing or have added extra";
            return false;
        }
    }

    //check if sum of percent user array matches with the 100
    public function checkPercentWithPercentageUserArray($value){
        $percent = (float)100;
        $getSumPercent = (float) array_sum(array_values($value));
        if($percent === $getSumPercent){
            return true;
        }else{
            $this->errorMessage =  " In :attribute feild sum of percentage is not 100";
            return false;
        }
    }

    public function checkPercentageUptoTwodecimalInPercentageUserArray($value){
          foreach ($value as $key => $percentage) {
            $percentage = (float) $percentage;
            if(floatval(number_format($percentage, 2)) === $percentage){
                continue;
            }else{
                $this->errorMessage =  " In :attribute feild percentage value should be upto 2 digit decimal places";
                break;
            }
        }

        if(!empty($this->errorMessage)){
            return false;
        }else{
            return true;
        }
    }
}
