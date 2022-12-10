<?php
namespace App\Http\Service;

use App\Models\Txn;
use App\Models\TxnMeta;
use App\Models\User;
use App\Traits\Utils;

class TxnService
{
    use Utils;


    public function __construct()
    {
        
    }

    public function createTxn($txndata ){

        $referenceNumber = $this->generateRef('TRXN' , 12 );
        
        $txndetails = json_encode($txndata['userSplitAmountData']);
        Txn::create(['txn_reference' => $referenceNumber ,
                     'user_id' => $txndata['userid'],
                     'amount' => $txndata['amount'],
                     'txn_details' => $txndetails,
                     'expense' => $txndata['expense'],
        ]);

        foreach ($txndata['userSplitAmountData'] as $key => $value) {

        TxnMeta::create(['txn_reference' => $referenceNumber ,
                    'user_id' => $txndata['userid'],
                    'split_user_id' => $key,
                    'split_amount' => $value,                    
            ]);

        }

        return  true;
    }

    public function getUserExpense($user_id)
    {   
        $returnArray = [ 'user' => [] ,'transactions' => [] , "balances" => [] ];
        $txndata = User::with(['transactions' => function ($query) {
            $query->select('txn_reference','amount','txn_details','expense','created_at','user_id');
        }])->where('id' , $user_id)->get()->toArray();

        if(!empty($txndata)){

            $userData = ['name' => $txndata[0]['name'] , 'user_id' => $txndata[0]['id']];
            $transactionData = [];
            foreach($txndata[0]['transactions'] as $txn){
                $tempData = [];
                $tempData['txn_reference'] = $txn['txn_reference'];
                $tempData['amount'] = $txn['amount'];
                $tempData['expense'] = $txn['expense'];
                $tempData['txn_details'] = json_decode($txn['txn_details'],true);
                $tempData['created_at'] = $txn['created_at'];
                $transactionData[] = $tempData;

            }

            $returnArray['user'] = $userData ;
            $returnArray['transactions'] = $transactionData;
            $balanceFinalArr = $this->getUserBalances($user_id , $userData['name']);
            $returnArray['balances'] = $balanceFinalArr;

           return $returnArray;
        }
    }

    public function getUserBalances($user_id , $userName){

       // $user_id = 2;
        $collect = TxnMeta::collectFromUser($user_id);
        $owes = TxnMeta::owesToUser($user_id);
        // $collectUserIdArray = array_column($collect , 'split_user_id');
        // $owesUserIdArray = array_column($owes , 'user_id');
        // dump($collectUserIdArray , $owesUserIdArray);

        $balanceUserArray = [];

        if(!empty($collect)){
            foreach ($collect as $key => $value) {
                $temp = [];
                $temp['description'] = "{$value['name']}({$value['split_user_id']}) owes {$userName}({$user_id})" ; 
                $temp['amount'] = $value['split_amount'];
                $temp['user_id']  = $value['split_user_id'];  
                $balanceUserArray[]  = $temp;     
            }
        }

        $balanceUserIdArray = array_column($balanceUserArray , 'user_id');
        $flipBalanceUserIdArray = array_flip($balanceUserIdArray);
        //dump( $balanceUserArray , $balanceUserIdArray , $flipBalanceUserIdArray);

        if(!empty($owes)){
            foreach ($owes as $key => $value) {
                if(isset($flipBalanceUserIdArray[$value['user_id']])){
                    $node = [];
                    $node = $balanceUserArray[$flipBalanceUserIdArray[$value['user_id']]];
                    $newamount = $node['amount'] - $value['split_amount'];
                    if($newamount <= 0){
                        $node['description'] = "{$userName}({$user_id}) owes {$value['name']}({$value['user_id']})";
                        $node['amount'] = -1 * $newamount;
                    }else{
                        $node['amount'] =  $newamount;
                    }
                    $node['user_id'] = $user_id;

                    unset($balanceUserArray[$flipBalanceUserIdArray[$value['user_id']]]);
                    $balanceUserArray[] = $node;
                }else{
                    $node['description'] = "{$userName}({$user_id}) owes {$value['name']}({$value['user_id']})";
                    $node['amount'] = $value['split_amount'];
                    $node['user_id'] = $user_id;
                    $balanceUserArray[] = $node;
                }
            }
        }

        //dump( $balanceUserArray );
        $finalArray = [];
        if(!empty($balanceUserArray)){
            usort($balanceUserArray, function($a, $b) {
                return $a['user_id'] <=> $b['user_id'];
            });

            foreach($balanceUserArray as $arr){
                $finalArray[] = $arr['description'] . " : " . $arr['amount'];
            }
        }

        return $finalArray;

    }


    public function getEveryBalance()
    {
        $finalArray= [];
        $data = TxnMeta::getEveryRecordBalance();
        $balanceUserArray = [];
        if(!empty($data)){
           
            $skiparray = [];
            
            foreach($data as $okey => $piece){
                if(in_array($okey , $skiparray)){
                    continue;
                }
                $node = [];
                $userName = $piece['user_name'];
                $user_id = $piece['user_id'];
                $split_user_name = $piece['name'];
                $split_user_id = $piece['split_user_id'];
                
                $dataCollect = collect($data);
                $getOppNode = $dataCollect->where('user_id' , $split_user_id)->where('split_user_id' , $user_id)->all();
                if(empty($getOppNode)){
                    $node['description'] = "{$split_user_name}({$split_user_id}) owes {$userName}({$user_id})";
                    $node['amount'] = $piece['split_amount'];
                    $node['user_id'] = $user_id;
                    $node['split_user_id'] = $split_user_id;
                }else{ 
                    foreach($getOppNode as $key => $val){
                        $newamount = $piece['split_amount'] - $val['split_amount'];
                        if($newamount <= 0){
                            $node['description'] = "{$userName}({$user_id}) owes {$split_user_name}({$split_user_id})";
                            $node['amount'] = -1 * $newamount;
                        }else{
                            $node['description'] = "{$split_user_name}({$split_user_id}) owes {$userName}({$user_id})";
                            $node['amount'] =  $newamount;
                        }
                        $node['user_id'] = $user_id;
                        $node['split_user_id'] = $split_user_id;
                        array_push($skiparray , $key);
                    }
                }
                $balanceUserArray[] = $node;
            }

           $newBalUserArray = collect($balanceUserArray);
           $sorted = $newBalUserArray->sortBy([
            fn ($a, $b) => $a['user_id'] <=> $b['user_id'],
            fn ($a, $b) => $b['split_user_id'] <=> $a['split_user_id'],
            ]);
            $sorted->values()->toArray();
           
            foreach($balanceUserArray as $arr){
                $finalArray[] = $arr['description'] . " : " . $arr['amount'];
            }
            
            
        }

        return $finalArray;
    }

    public function test($user_id = null)
    {
       // $hard = Txn::with('user')->where('user_id' , 1)->get()->toArray();
       // $hard = Txn::with('txnMetaViaTxnRefno')->where('txn_reference' , 'TRXNd9c9f71a686a')->get()->toArray();
       // $hard = Txn::with('txnMetaViaUserId')->where('txn_reference' , 'TRXNd9c9f71a686a')->get()->toArray();
    
        // $user_id = 2;
        // $hard = TxnMeta::collectFromUser($user_id);
        // $hard2 = TxnMeta::owesToUser($user_id);

       // dd($hard , $hard2 );
    }

}