<?php

namespace Sylvester\Quickbooks;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Quickbooksd {

    protected $dsn;

protected $config;

protected $map=[];
 


public function __construct()
{
    $this->config = config('quickbooks');

    $this->dsn    = $this->config['qb_dsn'];
}

public function connect(){

    date_default_timezone_set($this->config['qb_timezone']);
    // error_reporting(E_ALL | E_STRICT);

    if(!$this->config['qb_dsn']){
        $dbconf 	= config('database');
        $db 	=  $dbconf['connections'][$dbconf['default']];
        if($db['driver'] == 'mysql'){
            $db['driver'] = 'mysqli';
        }
        $this->dsn = $db['driver'] . '://' . $db['username'] . ':' .$db['password'] . '@' . $db['host'] . ':' . $db['port'] .'/'. $db['database'];
    }


    // Check to make sure our database is set up 
    if (!\QuickBooks_Utilities::initialized($this->dsn))
    {
        // Initialize creates the neccessary database schema for queueing up requests and logging
        \QuickBooks_Utilities::initialize($this->dsn);
        
        // This creates a username and password which is used by the Web Connector to authenticate
        \QuickBooks_Utilities::createUser($this->dsn, $this->config['qb_username'], $this->config['qb_password']);
    }
    
    // Set up our queue singleton
    \QuickBooks_WebConnector_Queue_Singleton::initialize($this->dsn);
    // 
    return \QuickBooks_Utilities::initialized($this->dsn);

  
}

public function initServer($return = true, $debug = false)
{

    // Create a new server and tell it to handle the requests
    $Server = new \QuickBooks_WebConnector_Server(
            $this->dsn, 
            $this->config['actions'], 
            $this->config['error_map'], 
            $this->config['hooks'], 
            $this->config['qb_log_level'], 
            $this->config['soap']['server'], 
            QUICKBOOKS_WSDL, 
            $this->config['soap']['options'], 
            $this->config['handler_options'], 
            $this->config['driver_options'], 
            $this->config['callback_options']
    );

    return $Server->handle($return, $debug); 

  
    // if $return is turned-on, there is an error "Response is not well-formed XML". Probably because of whitespaces somewhere. I haven't figured out this yet
}



// Queue up a request for the Web Connector to process
public function enqueue($action, $ident, $priority = 0, $extra = null, $user = null)
{
    $Queue = new \QuickBooks_WebConnector_Queue($this->dsn);
    Log::alert(print_r(array($action, $ident, $priority, $extra, $user), true));
    return $Queue->enqueue($action, $ident, $priority, $extra, $user);
}
}



