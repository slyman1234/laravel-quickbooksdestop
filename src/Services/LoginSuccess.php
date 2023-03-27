<?php


namespace Sylvester\Quickbooks\Services;

use Illuminate\Support\Facades\Log;
use Sylvester\Quickbooks\Services\GetRun;

class LoginSuccess
{
	/**
	 * Catch and handle errors from QuickBooks
	 */		
   
    protected $dsn;

    protected $config;
    
    protected $map=[];
     
    public function __construct()
    {
        $this->config = config('quickbooks');
     


        
    }





    public static function successLogin($requestID, $user, $hook, &$err, $hook_data, $callback_config)
     {
         // For new users, we need to set up a few things
     
         // Fetch the queue instance
         $Queue = \QuickBooks_WebConnector_Queue_Singleton::getInstance();
         $date = '1983-01-02 12:01:01';
         
         // Set up the invoice imports
         // if (!_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_INVOICE))
         // {
         // 	// And write the initial sync time
         // 	_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_INVOICE, $date);
         // }
         
     
         // // ... and for sales orders
         // if (!_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_SALESORDER))
         // {
         // 	_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_SALESORDER, $date);
         // }
         $getrun = new GetRun();
       
         // ... and for items
         if (!$getrun->GetLastRun($user, QUICKBOOKS_IMPORT_ITEM))
         {
            $getrun->SetLastRun($user, QUICKBOOKS_IMPORT_ITEM, $date);
         }

         $config = config('quickbooks');
         
         // Make sure the requests get queued up
         //$Queue->enqueue(QUICKBOOKS_IMPORT_SALESORDER, 1, QB_PRIORITY_SALESORDER, null, $user);
         //$Queue->enqueue(QUICKBOOKS_IMPORT_INVOICE, 1, QB_PRIORITY_INVOICE, null, $user);
         //$Queue->enqueue(QUICKBOOKS_IMPORT_PURCHASEORDER, 1, QB_PRIORITY_PURCHASEORDER, null, $user);
         //$Queue->enqueue(QUICKBOOKS_IMPORT_CUSTOMER, 1, QB_PRIORITY_CUSTOMER, null, $user);
         $Queue->enqueue(QUICKBOOKS_IMPORT_ITEM, 1,  $config['QB_PRIORITY_ITEM'], null, $user);
         
     
         
     }






}