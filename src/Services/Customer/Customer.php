<?php
namespace Sylvester\Quickbooks\Services\Customer;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Customer
{



    
	/**
	 * Issue a request to QuickBooks to add a customer
	 */
	public static function xmlRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{
		// Do something here to load data using your model

        $customer = DB::table('customers')->where('id', $ID)->first();      
		// Build the qbXML request from $data
		$xml = '<?xml version="1.0" encoding="utf-8"?>
		<?qbxml version="'.$version.'"?>
		<QBXML>
			<QBXMLMsgsRq onError="stopOnError">
				<CustomerAddRq requestID="' . $requestID . '">
					<CustomerAdd>
						<Name>'.$customer->name.'</Name>
						<CompanyName>'.$customer->name.'</CompanyName>
                      	<Phone>'.$customer->phone.'</Phone>
						<Email>'.$customer->email.'</Email>
					
					</CustomerAdd>
				</CustomerAddRq>
			</QBXMLMsgsRq>
		</QBXML>';
	
		return $xml;
	}

	/**
	 * Handle a response from QuickBooks indicating a new customer has been added
	 */	
	public static function xmlResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
	{
		// Do something here to record that the data was added to QuickBooks successfully 
		Log::info(print_r(array($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents), true));
		
      
            DB::table('customers')
            ->where('id', $ID)
            ->update(['ListID' => $idents['ListID']]);
        
        
        return true; 
	}
}