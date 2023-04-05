<?php

namespace Sylvester\Quickbooks\Services\Importitem;

use Sylvester\Quickbooks\Services\GetRun;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Importitem
{
 

    protected $config;
    protected $QBD;

    public function __construct()
    {  
        $this->config = config('quickbooks');


        $this->QBD  = new GetRun;
    	

    	
    }
	/**
	 * Issue a request to QuickBooks to add a customer
	 */
	public static function xmlRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{
		// Do something here to load data using your model
		$getrun = new GetRun();

		$config = config('quickbooks');

        $attr_iteratorID = '';
        $attr_iterator = ' iterator="Start" ';
        if (empty($extra['iteratorID']))
        {
            // This is the first request in a new batch

			
            $last = $getrun->GetLastRun($user, $action);
            $getrun->SetLastRun($user, $action);			// Update the last run time to NOW()
            
            // Set the current run to $last
            $getrun->SetCurrentRun($user, $action, $last);
        }
        else
        {
            // This is a continuation of a batch
            $attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
            $attr_iterator = ' iterator="Continue" ';
            
            $last = $getrun->GetCurrentRun($user, $action);
        }
        
        // Build the request
        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <?qbxml version="' . $version . '"?>
            <QBXML>
                <QBXMLMsgsRq onError="stopOnError">
    
                    <ItemQueryRq ' . $attr_iterator . ' ' . $attr_iteratorID . ' requestID="' . $requestID . '">
                        <MaxReturned>' . $config['QB_QUICKBOOKS_MAX_RETURNED'] . '</MaxReturned>
                        
                 
                        <OwnerID>0</OwnerID>
    
        
                    </ItemQueryRq>	
                </QBXMLMsgsRq>
            </QBXML>';
            
        return $xml;
	}

	/**
	 * Handle a response from QuickBooks indicating a new customer has been added
	 */	
	public static function xmlResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
	
		$config = config('quickbooks');

		if(!$config['qb_dsn']){
			$dbconf 	= config('database');
			$db 	=  $dbconf['connections'][$dbconf['default']];
			if($db['driver'] == 'mysql'){
				$db['driver'] = 'mysqli';
			}
			$dsn = $db['driver'] . '://' . $db['username'] . ':' .$db['password'] . '@' . $db['host'] . ':' . $db['port'] .'/'. $db['database'];
		}
	
	if (!empty($idents['iteratorRemainingCount']))
	{
		// Queue up another request
		
		$Queue = \QuickBooks_WebConnector_Queue_Singleton::getInstance();
		$Queue->enqueue(QUICKBOOKS_IMPORT_ITEM, null,0, array( 'iteratorID' => $idents['iteratorID'] ), $user);
	}
	
	// Import all of the records
	$errnum = 0;
	$errmsg = '';
	$Parser = new \QuickBooks_XML_Parser($xml);
	if ($Doc = $Parser->parse($errnum, $errmsg))
	{
		$Root = $Doc->getRoot();
		$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/ItemQueryRs');
		
		foreach ($List->children() as $Item)
		{
			$type = substr(substr($Item->name(), 0, -3), 4);
			$ret = $Item->name();
			
		
			$arr = array(
				'listidentity' => $Item->getChildDataAt($ret . ' ListID'),
				'created_at' => $Item->getChildDataAt($ret . ' TimeCreated'),
				'updated_at' => $Item->getChildDataAt($ret . ' TimeModified'),
				'name' => $Item->getChildDataAt($ret . ' Name'),
				'identifier' => mt_rand(),
				'specification' => $Item->getChildDataAt($ret . ' specification'),
				'quantity' =>  $Item->getChildDataAt($ret . ' QuantityOnHand')

				);
			
			$look_for = array(
				'discount_amount' => array( 'SalesOrPurchase Price', 'SalesAndPurchase SalesPrice', 'SalesPrice' ),
				'brief_info' => array( 'SalesOrPurchase Desc', 'SalesAndPurchase SalesDesc', 'SalesDesc' ),
				
			
			); 


			
			foreach ($look_for as $field => $look_here)
			{
				if (!empty($arr[$field]))
				{
					break;
				}
				
				foreach ($look_here as $look)
				{
					$arr[$field] = $Item->getChildDataAt($ret . ' ' . $look);
				}
			}
			
			\QuickBooks_Utilities::log($dsn, 'Importing ' . $type . ' Item ' . $arr['name'] . ': ' . print_r($arr, true));
			
			foreach ($arr as $key => $value) {
				$arr[$key] = $value;
			}
			

			$listidentity = $arr['listidentity'];
            $quantity = $arr['quantity'];
			$discountamount = $arr['discount_amount'];

			DB::table('products')->insertOrIgnore($arr);
			
			DB::table('products')
            ->where('listidentity', '=', $listidentity)
            ->update(
			['quantity' => $quantity, 'discount_amount' => $discountamount]);

			
		//	Log::info(print_r(array($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents), true));
		  
			//trigger_error(print_r(array_keys($arr), true));
			


			
	
	}
	

        return true; 
	}
}
}