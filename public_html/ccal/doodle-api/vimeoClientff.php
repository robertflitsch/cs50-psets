<?
/****************************************************************
* vimeoClient.php
* 
* Test to learn how to make http requests
*
* constants to remember
*   
*
****************************************************************/

    // requirements
    require_once("includes/request.php");
    require_once("includes/helpers.php");

// consumer key, secret, and request url (given by Doodle.com)
define("CKEY", "aebab86dd08b0eb9bc1900fe4e7c5cd0");
define("CSECRET", "ea1d7e0fcfbdc7d7");
define("URL", "http://vimeo.com/");
define("OFFSET", 7);

    /*
     * array
     * send($request)
     *
     * Sends HTTP request and returns response as an array
     * containing the response status, header, and content.
     */
     
    function send($request)
    {
        // Convert the data array into URL Parameters like a=b&foo=bar etc.
        if($request->data != NULL)
            $data = http_build_query($request->data);
            
        else
            $data = NULL;
     
        // parse the given URL
        $url = parse_url($request->url);
     
        // check for correct request scheme
        if ($url['scheme'] != 'http' && $url['scheme'] != 'https')
        { 
            die('Error: Only HTTP or HTTPS request are supported!');
        }
        
        // extract host and path:
        $host = $url['host'];
        $path = $url['path'];
        
        // open a socket connection on port 80 - timeout: 30 sec
        $fp = fsockopen($host, 80, $errno, $errstr, 30);
        
        if ($fp)
        { 
            // create the base string:
            fputs($fp, "GET $path HTTP/1.1\r\n");
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "Content-type: text/plain \r\n");
            fputs($fp, "Content-length: ".strlen($data)."\r\n");
            fputs($fp, "{$request->auth_header}\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
    
            $result = ''; 
            while(!feof($fp))
            {
                // receive the results of the request
                $result .= fgets($fp, 128);
            }
        }
        
        else
        { 
            return array(
                'status' => 'err', 
                'error' => "$errstr ($errno)"
            );
        }
    
        // close the socket connection:
        fclose($fp);
    
        // split the result status from the response
        $result = explode("\r\n", $result, 2);
        
        $status = isset($result[0]) ? $result[0] : NULL;
        $result = isset($result[1]) ? $result[1] : NULL;
        
        // split the result header from the remaining content
        $result = explode("\r\n\r\n", $result, 2);
    
        $header = isset($result[0]) ? $result[0] : NULL;
        $content = isset($result[1]) ? $result[1] : NULL;
    
        // return as structured array:
        return array(
            'status' => $status,
            'header' => $header,
            'content' => $content
        );
    }
    
    /*  
     * HttpRequest object
     * form_header($request)
     *
     * Formats the authorization header for the oauth request.
     * Returns the Http Request object including the auth
     * header.
     */
    
    function form_header($request)
    {
        // form request data
        $header = $request->authorization;
        
        foreach($request->auth as $key => $value)
            $header = $header.$value.",";
        
        // get rid of tail-ing comma
        $header = substr($header, 0, -1);
            
        // update request string
        $request->auth_header = $header;
        
        return $request;
    }
    
    /*
     * string
     * request_token(void)
     *
     * Forms and sends an HTTP request as defined in "Request
     * Token" at http://doodle.com/xsd1/AAforRESTfulDoodle.pdf
     * Returns a string containing the oauth_token, 
     * oauth_token_secret, and callback confirmation. 
     */
     
    function request_token()
    {
        // variables for HTTP requests
        $oauth_nonce = rand();
        $oauth_timestamp = time() + OFFSET;
        
        // make new HttpRequest Object
        $request = new HttpRequest;
        
        // set url for request
        $request->url = URL."api/rest/v2/";
        
        // set method for request
        $request->method = "GET";
        
        // set authorisation for request
        $request->authorization = "Authorization: OAuth ";
        
        // set required authorization parameters for request
        $request->auth['realm'] = "realm=\"\"";
        $request->auth['oauth_callback'] = "oauth_callback=\"oob\"";
        $request->auth['oauth_consumer_key'] = "oauth_consumer_key=\"".CKEY."\"";
        $request->auth['oauth_signature_method'] = "oauth_signature_method=\"HMAC-SHA1\"";
        $request->auth['oauth_timestamp'] = "oauth_timestamp=\"".$oauth_timestamp."\"";
        $request->auth['oauth_nonce'] = "oauth_nonce=\"".$oauth_nonce."\"";
        $request->auth['oauth_version'] = "oauth_version=\"1.0\"";
        
        // no tokens yet!
        $tokens = NULL;
        
        // format base string and key for oauth_signature
        $signature = form_signature($request, $tokens);
        
       
        // hash signature
        $oauth_signature = hash_hmac("SHA1", $signature['base_string'], $signature['key'], true);
        
        // base-64 encode the signature
        $oauth_signature = base64_encode($oauth_signature);
        
        // enclude the signature in the request
        $request->auth['oauth_signature'] = "oauth_signature=\"".$oauth_signature."\"";
        
        // format authorization header
        $request = form_header($request);
        
        // send request
        $response = send($request);
        
        dump($response);
        // respond to error messages
        //if($response['status'] == "HTTP/1.1 401 Unauthorized")
            //apologize("Sorry, you have not been authorized to access this information.");
            
        // return oauth_token etc        
        return $response['content'];
    }
    
    /*
     * string
     * access_token($token_string)
     *
     * Forms and sends an HTTP request as defined in "Access
     * Token" at http://doodle.com/xsd1/AAforRESTfulDoodle.pdf
     * Returns an array containing the auth_token and
     * oauth_token_secret.
     */
     
    function access_token($token_string)
    {
        // form oauth token and token secret from string
        $tokens = explode("&", $token_string, 3);
        
        // add values to tokens array
        $tokens['oauth_token'] = substr($tokens[0], 12);
        $tokens['oauth_token_secret'] = substr($tokens[1], 19);
        $tokens['oauth_callback_confirmed'] = substr($tokens[2], 25);
        
        // variables for HTTP requests
        $oauth_nonce = rand();
        $oauth_timestamp = time() + OFFSET;
        
        // make new HttpRequest Object
        $request = new HttpRequest;
        
        // set url for request
        $request->url = URL."oauth/accesstoken";
        
        // set method for request
        $request->method = "GET";
        
        // set authorization for request
        $request->authorization = "Authorization: OAuth ";
        
        // set required authorization parameters for request
        $request->auth['realm'] = "realm=\"\"";
        $request->auth['oauth_token'] = "oauth_token=\"".$tokens['oauth_token']."\"";
        $request->auth['oauth_consumer_key'] = "oauth_consumer_key=\"".CKEY."\"";
        $request->auth['oauth_signature_method'] = "oauth_signature_method=\"PLAINTEXT\"";
        $request->auth['oauth_timestamp'] = "oauth_timestamp=\"".$oauth_timestamp."\"";
        $request->auth['oauth_nonce'] = "oauth_nonce=\"".$oauth_nonce."\"";
        
        /* FORMATTING HMAC-SHA1 SIGNATURE
       
        // format base string and key for oauth_signature
        $signature = form_signature($request, $tokens);
        
        // hash signature

        $oauth_signature = hash_hmac("SHA1", $signature['base_string'], $signature['key'], true);
        
        $request->auth['oauth_signature'] = "oauth_signature=\"".$oauth_signature."\"";
        */
        
        $request->auth['oauth_signature'] = "oauth_signature=\"".CSECRET."&".$tokens['oauth_token_secret']."\"";
         
        // format authorization header
        $request = form_header($request);
        
        // send request
        $response = send($request);
        
        // respond to error messages
        if($response['status'] == "HTTP/1.1 401 Unauthorized")
            apologize("Sorry, you have not been authorized to access this information.");
        
        // return oauth_token etc
        return $response['content'];
    }
    
    /*
     * array
     * form_signature($request, $token)
     *
     * Formats the base string and key for the 
     * oauth_signature.
     */
    
    function form_signature($request, $tokens)
    {
        // construct base string array
        $method = $request->method;
        $url = urlencode($request->url);
        //$parameters['callback'] = $request->auth['oauth_callback'];
        $parameters['ckey'] = $request->auth['oauth_consumer_key'];
        $parameters['nonce'] = $request->auth['oauth_nonce'];
        $parameters['sig_method'] = $request->auth['oauth_signature_method'];
        $parameters['timestamp'] = $request->auth['oauth_timestamp'];
        
        /*if(isset($tokens))
        {
            if($tokens['auth_token'] == NULL)
                $parameters['token'] = $tokens['oauth_token']; 
        }*/
        
        $parameters['version'] = $request->auth['oauth_version'];
        
        // initialize parameter string
        $ps = NULL;
        
        // produce HTTP parameter string
        foreach($parameters as $key => $value)
                $ps = $ps."&".$value;
        
        // get rid of 1st "&"
        $ps = substr($ps, 1);
        
        // get rid of quotes
        $ps = str_replace('"', NULL, $ps);
            
        // percent encode the parameter string    
        $ps = urlencode($ps);    
        
        // produce base string
        $base_string = $method."&".$url."&".$ps;
                        
        // produce key
        if($tokens == NULL)
            $key = CSECRET."&";
            
        else
            $key = CSECRET."&".$tokens['oauth_token_secret'];
        
        // form associative array for formatting signature
        $signature['base_string'] = $base_string;
        $signature['key'] = $key;    
        
        return $signature;
    }
    
    /*
     * array
     * form_tokens($token_string, $token_string2)
     *
     * Formats an array containing the request token, access token
     * and token secret used for future get requests
     */
    
    function form_tokens($token_string, $token_string2)
    {
        // extract and insert oauth_token and oauth_token_secret
        $token_set = explode("&", $token_string, 3);
    
        $tokens['request_token'] = substr($token_set[0], 12);
        $tokens['token_secret'] = substr($token_set[1], 19);
        
        // extract and insert oauth(access)_token
        $token_set2 = explode("&", $token_string2, 2);    
        
        $tokens['access_token'] = substr($token_set2[0], 12);
        
        return $tokens;
    }
    
    /*
     * string
     * extract_id($url)
     *
     * Takes a url to a Doodle Poll and extracts and returns
     * the poll's 16-character identifier.
     */
     
    function extract_id($url)
    {
        $url = parse_url($url);
        $id = substr($url['path'], 1);
        
        return $id;
    }
    
    /*
     * XML object
     * access_poll($id, $tokens)
     *
     * Retrieves (via HTTP Request) and returns the XML 
     * encoded object including the poll information for
     * the poll identified by $id.
     */
    
    function access_poll($id, $tokens)
    {
        // variables for HTTP requests
        $oauth_nonce = rand();
        $oauth_timestamp = time() + OFFSET;
        
        // make new HttpRequest Object
        $request = new HttpRequest;
        
        // set url for request
        $request->url = URL."polls/$id";
        
        // set method for request
        $request->method = "GET";
        
        // set authorization for request
        $request->authorization = "Authorization: OAuth ";
        
        // set required authorization parameters for GET requesttoken
        $request->auth['realm'] = "realm=\"\"";
        $request->auth['oauth_token'] = "oauth_token=\"".$tokens['access_token']."\"";
        $request->auth['oauth_consumer_key'] = "oauth_consumer_key=\"".CKEY."\"";
        $request->auth['oauth_signature_method'] = "oauth_signature_method=\"PLAINTEXT\"";
        $request->auth['oauth_timestamp'] = "oauth_timestamp=\"".$oauth_timestamp."\"";
        $request->auth['oauth_nonce'] = "oauth_nonce=\"".$oauth_nonce."\"";
        
        /* FORMATTING HMAC-SHA1 SIGNATURE
       
        // format base string and key for oauth_signature
        $signature = form_signature($request, $tokens);
        
        // hash signature
        $oauth_signature = hash_hmac("sha1", $signature['base_string'], $signature['key'], true);
        
        $request->auth['oauth_signature'] = "oauth_signature=\"".$oauth_signature."\"";
        */
         
        $request->auth['oauth_signature'] = "oauth_signature=\"".CSECRET."&".$tokens['token_secret']."\"";
    
        // format authorization header
        $request = form_header($request);
        
        // send request
        $response = send($request);
        
        // respond to error messages
        if($response['status'] == "HTTP/1.1 404 Not Found")
            apologize("Sorry, this poll either doesn't exist, or is hidden. Please try again.");
        
        else if($response['status'] == "HTTP/1.1 410"/*DELETED POLL*/)
            apologize("Sorry, this poll has been deleted.");
        
        // return the poll as xml
        return $response['content'];
    }

?>
