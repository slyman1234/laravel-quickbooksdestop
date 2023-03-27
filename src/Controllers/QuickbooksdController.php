<?php
namespace Sylvester\Quickbooks\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Sylvester\Quickbooks\Quickbooksd;
use Illuminate\Support\Facades\Log;

class QuickbooksdController extends Controller
{
    protected $QBD;

    public function __construct()
    {  
        $this->QBD  = new Quickbooksd;
    	
        $this->QBD->connect();
    	
    }

    public function initQBWC(Request $request){

        $response = $this->QBD->initServer(false, false); //$return,$debug

        $contentType = 'text/plain';

        if($request->isMethod('post'))
        {
            $contentType = 'text/xml';
        }
        elseif($request->input('wsdl') !== null or $request->input('WSDL') !== null)
        {
            $contentType = 'text/xml';
        }

        if($contentType == 'text/xml'){
            $tidy = new \tidy();
            $response = $tidy->repairString($response, ['input-xml'=> 1, 'indent' => 0, 'wrap' => 0]);
        }

        // Uncomment log if you want to see the request headers submitted from WebConnector
         //Log::info(print_r(getallheaders(), true));

        return response($response,200)->header('Content-Type', $contentType);

    }

  


}