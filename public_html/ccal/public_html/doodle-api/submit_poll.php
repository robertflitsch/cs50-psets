<? 
  
/****************************************************************
 * submit_poll.php
 * 
 * Submits a poll to the doodle api.
 *
 ****************************************************************/
    
    // requirements
    require_once("poll.php");
    require_once("doodleClient.php");
        
    // escape user input
    $input['name'] = htmlspecialchars($_POST["name"]);
    
    // check that user put their name
    if($input['name'] == "Your Name")
        apologize("Sorry, a name is required.");
    
    // get url
    $url = htmlspecialchars($_GET["url"]);
    
    // get the required poll information
    $poll = acquire_poll($url);
    
    // counter for time entries
    $time = 0;
        
    // figure out # time slots
    foreach($poll['keys']['OPTION'] as $key => $value)
    {
        // update counters
        $time++;
          
        // break once out of dates
        if($poll['text'][$value]['level'] != TIME_LEVEL)
        {    
            // correct $day variable
            $time--;
             
            break;
        }
    }

    // make array containing checked values
    for($i = 1; $i <= $time; $i++)
    {
        // user checked box
        if(isset($_POST["option$i"]))
            $option[$i] = htmlspecialchars($_POST["option$i"]);
        
        // user didn't check box    
        else
            $option[$i] = "0";
    }

    // add options to array containing all user input
    $input['option'] = $option;
    $input['tokens'] = $poll['tokens'];
    $input['id'] = extract_id($url);
    
    // generate xml for post request
    $input['xml'] = update_xml($input);
    
    // send request using this input
    $response = submit_poll($input);
    
    // if unknown error...
    if($response == false)
        apologize("Sorry, an unknown error occurred. Please try again.");

    else
        redirect("submitted.php?url=$url");
?>    
