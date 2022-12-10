<?php

namespace App\Http\Controllers;

use App\Http\Service\TxnService;
use App\Rules\DecimalRule;
use App\Rules\ExactExpenseRule;
use App\Rules\UserRule;
use App\Rules\PercentageExpenseRule;

use App\Traits\HttpResponse;
use App\Traits\SplitPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class PaymentsController extends Controller
{
    use HttpResponse , SplitPayment;

    protected $txnservice;

    public function __construct(TxnService $txnservice)
    {
        $this->txnservice = $txnservice;
    }

    public function makePayment(Request $request)
    {
        $user_id = Auth::user()->currentAccessToken()->tokenable_id;

        Validator::make($request->all(), [
           'amount' => ['required', 'numeric' , new DecimalRule()],
          // 'percent' => 'required|numeric',
           'user_array' =>  ['required' , 'array' , new UserRule($user_id)] ,
           'expense' => 'required|string|in:EQUAL,EXACT,PERCENT',
           'exact_user_array' => ['required_if:expense,==,EXACT' , 'array' , new ExactExpenseRule($request->all())],
           'percentage_user_array' =>  ['required_if:expense,==,PERCENT' , 'array' , new PercentageExpenseRule($request->all())]
       ])->stopOnFirstFailure()->validate();

       $data = $request->all();
       $optionArray = [];
       if(isset($data['exact_user_array']) && $data['expense'] == 'EXACT') {
        $optionArray['exact_user_array'] = $data['exact_user_array'];
       } 

       if(isset($data['percentage_user_array']) && $data['expense'] == 'PERCENT') {
        $optionArray['percentage_user_array'] = $data['percentage_user_array'];
       } 
       
       $userSplitAmountData = $this->splitPaymentAsperUser($data['expense'] 
                                            , $data['amount'] 
                                            , $data['user_array'] 
                                            , $optionArray);

       
       $txnData = ['amount' => $data['amount'] , 
                   'expense' => $data['expense'] ,
                   'userid' => $user_id,
                   "userSplitAmountData" => $userSplitAmountData,          
        ];

       $this->txnservice->createTxn($txnData);

      return $this->success([] , 'Spilt Payment Register');
    }

    public function getUserBalance(Request $request)
    {
        $user_id = Auth::user()->currentAccessToken()->tokenable_id;   
        
        Validator::make($request->all() , [
            'user_id' => "nullable|numeric|gt:0"
            ])->stopOnFirstFailure()->validate();
        
        $data = $request->all();
        $user_id = empty($data['user_id']) ? $user_id : $data['user_id'];
    
        $arr = $this->txnservice->getUserExpense($user_id);

        return $this->success([$arr] , 'User Single Balance');

    }


    public function getEveryoneBalance()
    {
        $arr = $this->txnservice->getEveryBalance();

        return $this->success([$arr] , 'User Everyone Balance');

    }

}
