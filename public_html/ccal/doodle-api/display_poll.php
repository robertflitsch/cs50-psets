<? 
  
/****************************************************************
 * display_poll.php
 * 
 * Defines function to print the html for the doodle 
 * poll web page.
 *
 ****************************************************************/
    
    // requirements
    require_once("poll.php");
    
    
    // escape user input
    $url = htmlspecialchars($_GET["url"]);
    
    // get the required poll information
    $poll = acquire_poll($url);
    
    // check to make sure it's a day/time poll
        if($poll['text'][$poll['keys']['TYPE']['0']]['value'] != "DATE")
            apologize("Sorry, Crimson Calendar Doodle Tool only works with date/time polls");
    
    // remember the poll name
    $poll_name = $poll['text'][$poll['keys']['TITLE']['0']]['value'];
    
?>

<html>
  <head>
    <title>Doodle Tool: <?= $poll_name?></title>
  </head>

  <body>
    <div id='title' style="text-align: center;">
      Doodle Tool: <?= $poll_name?><br>    
    </div>
    <div>
      <?display_poll(/*$_GET['gcal_info,']*/$poll)?>
    </div>
    <div style="text-align: center;">
      <a href= "display_poll.php?gcal_info=SOMETHING">Complete Doodle Poll with your Google Calendar information.</a><br><br>
      <a href= "doodle_tool.html">Back</a>
    </div>
  </body>
</html>
