<? 
  
/****************************************************************
 * submit_poll.php
 * 
 * Submits a poll to the doodle api.
 *
 ****************************************************************/
    
    // requirements
    require_once("poll.php");
    
    // escape user input
    $input['name'] = htmlspecialchars($_POST["name"]);

    // make array containing checked values
    for($i = 1; $i <= OPTIONS; $i++)
    {
        // user checked box
        if(isset($_POST["option$i"]))
            $option[$i] = htmlspecialchars($_POST["option$i"]);
        
        // user didn't check box    
        else
            $option[$i] = "O";
    }

    // add options to array containing all user input
    $input['option'] = $option;
    dump($input);
    // send request using this input
    $response = submit_poll();
    
        
    
