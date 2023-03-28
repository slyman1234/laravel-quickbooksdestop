<?php

namespace Sylvester\Quickbooks\Services\Item;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Item
{
 
	/**
	 * Issue a request to QuickBooks to add a customer
	 */
	public static function xmlRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{
		// Do something here to load data using your model

        $products = DB::table('products')->where('id', $ID)->first();      
		// Build the qbXML request from $data
		$xml = '<?xml version="1.0" encoding="utf-8"?>
		<?qbxml version="'.$version.'"?>
		<QBXML>
			<QBXMLMsgsRq onError="stopOnError">
			<ItemInventoryAddRq>
			<ItemInventoryAdd> 
					<Name >'.implode(' ', array_slice(explode(' ',  $products->name  ), 0, 3)).'</Name> 
		        	<SalesDesc >'.strip_tags($products->brief_info).'</SalesDesc> 
					<SalesPrice >'.doubleval($products->discount_amount).'</SalesPrice> 
					<IncomeAccountRef>
					<FullName >Merchandise Sales</FullName>
			        </IncomeAccountRef>
					<COGSAccountRef>
					<FullName >Cost of Goods Sold</FullName>
				    </COGSAccountRef>
					<AssetAccountRef>
                        <FullName >Inventory Asset</FullName>
                    </AssetAccountRef>
					<QuantityOnHand >'.$products->quantity.'.00</QuantityOnHand>				
			</ItemInventoryAdd>
	</ItemInventoryAddRq>
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
		  
        DB::table('products')
        ->where('id', $ID)
        ->update(['listidentity' => $idents['ListID']]);

        return true; 
	}
}