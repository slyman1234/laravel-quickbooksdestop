<?php

namespace Sylvester\Quickbooks;

use Illuminate\Support\Facades\Route;

use Sylvester\Quickbooks\Controllers\QuickbooksdController;




Route::group(['prefix' => 'qbd-webconnector', 'namespace' => 'Sylvester\Quickbooks'], function(){

	Route::any('/qbwc', [QuickbooksdController::class, 'initQBWC']);
	
	

});

