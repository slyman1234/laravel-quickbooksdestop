<?php

namespace Sylvester\Quickbooks\Services\Sales;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Sales
{
 
	/**
	 * Issue a request to QuickBooks to add a customer
	 */
	public static function xmlRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
	{
		// Do something here to load data using your model

        $products = DB::table('orders')->where('id', $ID)->first();  
        


        $listid = "";

        if($products->customer_id == null){

            $sales = DB::table('orders')
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->select(
            'orders.product_id', 
            'orders.quantity',
            'orders.order_price',
            'products.listidentity',
            'products.brief_info'
            )->where('orders.id', $ID)
            ->first();

            $listid = "8000020F-1679338966";

        }else{
        

            $sales = DB::table('orders')
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
            'orders.customer_id',
            'orders.product_id', 
            'orders.quantity',
            'orders.order_price',
            'products.listidentity',
            'products.brief_info',
            'customers.ListID'
            )
            ->where('orders.id', $ID)
            ->first();

            $listid= $sales->ListID;

        }


        $xml = '<?xml version="1.0" encoding="utf-8"?>
		<?qbxml version="'.$version.'"?>
		<QBXML>
			<QBXMLMsgsRq onError="stopOnError">
			<SalesOrderAddRq>
			<SalesOrderAdd> 
			<CustomerRef> 
			<ListID >'.$listid.'</ListID> 		
			</CustomerRef>
	        <SalesOrderLineAdd>
			<ItemRef>
		    <ListID >'.$sales->listidentity.'</ListID> 
            </ItemRef>
			<Desc >'. strip_tags($sales->brief_info).'</Desc> 
			<Quantity >'.$sales->quantity.'</Quantity> 
			<Amount >'.$sales->order_price.'.00</Amount> 			
			</SalesOrderLineAdd>
			</SalesOrderAdd>
	        </SalesOrderAddRq>
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
		return true; 
	}
}