<? 
  
/****************************************************************
 * display_poll.php
 * 
 * Defines function to print the html for the doodle 
 * poll web page.
 *
 ****************************************************************/
    
    // requirements
    require_once("doodle-api/poll.php");
    
    // escape (and check for) user input
    if(isset($_GET['url']))
        $url = htmlspecialchars($_GET["url"]);
    
    else
        apologize("Sorry, an error occurred. Please try again.");
    
    // get the required poll information
    $poll = acquire_poll($url);
    
    // check to make sure it's a day/time poll
    if(isset($poll['keys']['TYPE']))
    {    
        if($poll['text'][$poll['keys']['TYPE']['0']]['value'] != "DATE")
            apologize("Sorry, Crimson Calendar Doodle Tool only works with date/time polls");
    }
    
    else
        apologize("Sorry, an error occurred. Please try again.");        
    
    // remember the poll name
    $poll_name = $poll['text'][$poll['keys']['TITLE']['0']]['value'];
    
    /*
     * void
     * check_times($poll, $url)
     *
     * Sets up index.php to generate a pre-populated doodle
     * poll, and redirects back to the displayed pool,
     * with the form filled in accordingly
     */    
    
    function check_times($poll, $url)
    {
        // initialize counter
        $time = 0;
    
        // create time strings
        foreach($poll['keys']['OPTION'] as $key => $value)
        {
            // update counters
            $time++;
            
            // break once out of dates
            if($poll['text'][$value]['level'] != TIME_LEVEL)
            {
                // correct counter (valid event not found)
                $time--;
                break;
            }
            
            // set start and end values the same if single time
            if(isset($poll['text'][$value]['attributes']['DATETIME']))
            {
                $start_year = substr($poll['text'][$value]['attributes']['DATETIME'], 0, 4);
                $start_month = substr($poll['text'][$value]['attributes']['DATETIME'], 5, 2);
                $start_day = substr($poll['text'][$value]['attributes']['DATETIME'], 8, 2);
                $start_hours = substr($poll['text'][$value]['attributes']['DATETIME'], 11, -6);
                $start_minutes = substr($poll['text'][$value]['attributes']['DATETIME'], 14, -3);
            
                // reformat times to unix time
                $doodle['start'][$time] = $doodle['end'][$time] = mktime($start_hours, $start_minutes, 0, $start_month, $start_day, $start_year);
            }
            
            // set start AND end time if a range is set
            else if (isset($poll['text'][$value]['attributes']['STARTDATETIME']))
            {       
                // set start and end values
                $start_year = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 0, 4);
                $start_month = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 5, 2);
                $start_day = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 8, 2);
                $start_hours = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 11, -6);
                $start_minutes = substr($poll['text'][$value]['attributes']['STARTDATETIME'], 14, -3);
            
                $end_year = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 0, 4);
                $end_month = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 5, 2);
                $end_day = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 8, 2);
                $end_hours = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 11, -6);
                $end_minutes = substr($poll['text'][$value]['attributes']['ENDDATETIME'], 14, -3); 
            
                // reformat times to unix time
                $doodle['start'][$time] = mktime($start_hours, $start_minutes, 0, $start_month, $start_day, $start_year);
                $doodle['end'][$time] = mktime($end_hours, $end_minutes, 0, $end_month, $end_day, $end_year);
            }
            
            else
                apologize("Sorry, an error occurred. Please try again.");
        }
        
        // require index.php to get calendar information
        require_once("index.php");
    }
?>
<!DOCTYPE html>
  
  <html>
    <head>
      <link href="css/style.css" rel="stylesheet" type="text/css">
      <title>Crimson Calendar Doodle Tool: TEST</title>
    </head>

    <body>
      <div class="logo">
        <a href="index.php"><img id="logo" src="extras/harvard-logo.jpg" alt="Crimson Calendar"><h1 class="logo_text">Crimson Calendar</h1></a>
      </div>

      <div class="border_hor"></div>
      <div class="border_ver"></div>

      <div class="links">
        <h1><a class="link_box" href="index.php"> Home </a>
        <a class="link_box" href="events.html"> Events </a>
        <a class="link_box" href="calendars.html"> Calendars </a>
        <a class="active_link_box" href="doodle_tool.html"> Doodle Tool </a>
        </h1>
      </div>
      
      <div class="information">
        <div class="utility">
          <a class="utility_text" href="display_poll.php?url=<?= $url?>&amp;goog=true"><h1>Click here to complete Doodle Poll with your Google Calendar information.</h1></a>
        </div>
      </div>

      <div class="body">
        <div class="title">
          <h2>Doodle Tool</h2>
        </div>
      
        <div class="description">
          <h3>Poll Name: <?=$poll_name?></h3>
        </div>
      
        <?// display poll, depending on pre-filled or not
          if(isset($_GET['goog']))
              check_times($poll, $url);
        
          else
              display_poll($poll, $url);  
        ?>
      </div>
      <div class="utility2">
        <a class="utility_text" href="display_poll.php?url=<?= $url?>&amp;goog=true">Click here to complete Doodle Poll with your Google Calendar information.</a>
      </div>
    </body>
  </html>
