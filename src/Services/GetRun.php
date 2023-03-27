<?php
namespace Sylvester\Quickbooks\Services;

use Illuminate\Support\Facades\Log;

class GetRun
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
     
         $this->dsn    = $this->config['qb_dsn'];
 
 
         
     }
 
     public function GetLastRun($user, $action)
{

    if(!$this->config['qb_dsn']){
        $dbconf 	= config('database');
        $db 	=  $dbconf['connections'][$dbconf['default']];
        if($db['driver'] == 'mysql'){
            $db['driver'] = 'mysqli';
        }
        $this->dsn = $db['driver'] . '://' . $db['username'] . ':' .$db['password'] . '@' . $db['host'] . ':' . $db['port'] .'/'. $db['database'];
    }

	$type = null;
	$opts = null;
	return \QuickBooks_Utilities::configRead($this->dsn, $user, md5(__FILE__), $this->config['QB_QUICKBOOKS_CONFIG_LAST'] . '-' . $action, $type, $opts);
}


public  function SetLastRun($user, $action, $force = null)
     {
     
         if(!$this->config['qb_dsn']){
             $dbconf 	= config('database');
             $db 	=  $dbconf['connections'][$dbconf['default']];
             if($db['driver'] == 'mysql'){
                 $db['driver'] = 'mysqli';
             }
             $this->dsn = $db['driver'] . '://' . $db['username'] . ':' .$db['password'] . '@' . $db['host'] . ':' . $db['port'] .'/'. $db['database'];
         }
     
         $value = date('Y-m-d') . 'T' . date('H:i:s');
         
         if ($force)
         {
             $value = date('Y-m-d', strtotime($force)) . 'T' . date('H:i:s', strtotime($force));
         }
         
         return \QuickBooks_Utilities::configWrite($this->dsn, $user, md5(__FILE__),$this->config['QB_QUICKBOOKS_CONFIG_LAST'] . '-' . $action, $value);
     }
     



     public  function GetCurrentRun($user, $action){

    if(!$this->config['qb_dsn']){
        $dbconf 	= config('database');
        $db 	=  $dbconf['connections'][$dbconf['default']];
        if($db['driver'] == 'mysql'){
            $db['driver'] = 'mysqli';
        }
        $this->dsn = $db['driver'] . '://' . $db['username'] . ':' .$db['password'] . '@' . $db['host'] . ':' . $db['port'] .'/'. $db['database'];
    }

	$type = null;
	$opts = null;
	return \QuickBooks_Utilities::configRead($this->dsn, $user, md5(__FILE__),$this->config['QB_QUICKBOOKS_CONFIG_CURR']. '-' . $action, $type, $opts);	
}




public function SetCurrentRun($user, $action, $force = null)
{

    if(!$this->config['qb_dsn']){
        $dbconf 	= config('database');
        $db 	=  $dbconf['connections'][$dbconf['default']];
        if($db['driver'] == 'mysql'){
            $db['driver'] = 'mysqli';
        }
        $this->dsn = $db['driver'] . '://' . $db['username'] . ':' .$db['password'] . '@' . $db['host'] . ':' . $db['port'] .'/'. $db['database'];
    }


	$value = date('Y-m-d') . 'T' . date('H:i:s');
	
	if ($force)
	{
		$value = date('Y-m-d', strtotime($force)) . 'T' . date('H:i:s', strtotime($force));
	}
	
	return \QuickBooks_Utilities::configWrite($this->dsn, $user, md5(__FILE__), $this->config['QB_QUICKBOOKS_CONFIG_CURR'] . '-' . $action, $value);	




}





}