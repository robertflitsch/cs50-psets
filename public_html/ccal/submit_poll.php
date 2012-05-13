<? 
  
/****************************************************************
 * submit_poll.php
 * 
 * Submits a poll to the doodle api.
 *
 ****************************************************************/
    
    // requirements
    require_once("doodle-api/poll.php");
  
    if(isset($_POST['name']))
    {
        // escape user input
        $input['name'] = htmlspecialchars($_POST["name"]);
    
        // check that user put their name
        if($input['name'] == "Your Name" || !$input['name'])
            apologize("Sorry, a name is required.");
        
        // escape url
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
        
        /*if user reaches this point, submission successful*/
    }
    
    else
        // prevent user from jumping straight to this page
        apologize("Sorry, an error occurred. Please try again.");
?>

<!DOCTYPE html>

<html>
  <head>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <title>Crimson Calendar Doodle Tool: Submitted!</title>
  </head>
  <body>
    <div class="logo">
      <a href="index.php"><img id ="logo" src="extras/harvard-logo.jpg" alt="Crimson Calendar"><h1 class="logo_text">Crimson Calendar</h1></a>
    </div>

    <div class="border_hor"></div>
    <div class="border_ver"></div>

    <div class="links">
      <h1><a class="link_box" href= "index.php"> Home </a>
        <a class="link_box" href= "events.html"> Events </a>
        <a class="link_box" href= "calendars.html"> Calendars </a>
        <a class="active_link_box" href= "doodle_tool.html"> Doodle Tool </a>
      </h1>
    </div>

    <div class="extra_link">
      <?
        // link back to poll
        $url = htmlspecialchars($_GET['url']);
        echo("<a href=\"display_poll.php?url=$url\"><h2 class=\"link_text\">Return to poll</h2></a>");
      ?>
    </div>
    <div class="extra_link2">      
      <a href="doodle_tool.html"><h2 class="link_text">Complete another poll</h2></a>
    </div>

    <div class="body">
      <div class="submit_title">
        <h2>Doodle Tool: Poll Submitted</h2>
      </div>

      <div class="submit_message">
        <h1>Thank you for using Crimson Calendar.</h1>
        <h3>Your poll has been successfully submitted.</h3>
      </div>
    </div>
  </body>
</html>
