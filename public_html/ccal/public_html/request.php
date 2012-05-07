<?

/***********************************************************************
 * request.php
 *
 * Defines a class of an HttpRequest.
 **********************************************************************/

class HttpRequest
{
    // method of request (either POST, GET, etc.)
    public $method = NULL;
        
    // url for request
    public $url = NULL;
    
    // authorization header opening
    public $authorization = NULL;
    
    // total formatted authorization header
    public $auth_header = NULL; 
            
    // required parameters for authorization header
    public $auth = NULL;
    
    // data for request
    public $data = NULL;    
}

?>
