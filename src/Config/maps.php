<?php

return [
	// Map QuickBooks actions to handle functions
	'actions'	=> [
		QUICKBOOKS_ADD_CUSTOMER	=> [
    		[ Sylvester\Quickbooks\Services\Customer\Customer::class, 'xmlRequest' ],
    		[ Sylvester\Quickbooks\Services\Customer\Customer::class, 'xmlResponse' ]
        ],
       QUICKBOOKS_ADD_INVENTORYITEM => [
    		[ Sylvester\Quickbooks\Services\Item\Item::class, 'xmlRequest' ],
    		[ Sylvester\Quickbooks\Services\Item\Item::class, 'xmlResponse' ]
       ],
       QUICKBOOKS_ADD_SALESORDER => [
        [ Sylvester\Quickbooks\Services\Sales\Sales::class, 'xmlRequest' ],
        [ Sylvester\Quickbooks\Services\Sales\Sales::class, 'xmlResponse' ]
	   ],
	   QUICKBOOKS_IMPORT_ITEM => [
		[ Sylvester\Quickbooks\Services\Importitem\Importitem::class, 'xmlRequest' ],
        [ Sylvester\Quickbooks\Services\Importitem\Importitem::class, 'xmlResponse' ]
       ]

	]

];