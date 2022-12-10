<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExactExpenseRule implements Rule
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
     * Determine if the validation rule passes when 
     * checkExactUserExist is true 
     * checkCountOfUserInExactArray is true
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->checkExactUserExist($value) === true){
            if($this->checkCountOfUserInExactUserArray($value) === true){
                return $this->checkAmountWithExactUserArray($value);
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

    //check if User of exact user array exit in user array 
    public function checkExactUserExist($value){
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

    //check count of exact user id  should be less than 1 or zero from user array 
    public function checkCountOfUserInExactUserArray($value){
        $userArray = count($this->data['user_array']);
        $getExactUserId = count(array_keys($value));
        $difference = $userArray - $getExactUserId;
        if($difference == 1 || $difference == 0){
            return true;
        }else{
            $this->errorMessage =  " In :attribute feild some user id are missing or added have extra";
            return false;
        }
    }

    //check if exact user amount matches with the user amount
    public function checkAmountWithExactUserArray($value){
        $amount = $this->data['amount'];
        $getSumArray = array_sum(array_values($value));
        if($amount == $getSumArray){
            return true;
        }else{
            $this->errorMessage =  " In :attribute feild exact user sum amount is not matching with amount";
            return false;
        }
    }

}
