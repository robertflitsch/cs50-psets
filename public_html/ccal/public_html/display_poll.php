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
    require_once("check_times.php");
       
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
    <title>Crimson Calendar Doodle Tool: <?= $poll_name?></title>
  </head>

  <body>
    <div id='title' style="text-align: center;">
      Crimson Calendar Doodle Tool
      <br>
      Poll Name: <?= $poll_name?>
      <br>
      <a href= "index.php">Home</a> -----
      <a href= "doodle_tool.html">Back</a>    
      <br><br>
    </div>
    <div>
      <?// display poll, depending on pre-filled or not
        if(isset ($_GET['goog']))
            check_times($poll, $url);
        
        else
            display_poll($poll, $url);  
      ?>
    </div>
    <div style="text-align: center;">
      <a href= "display_poll.php?url=<?=$url?>&goog=true">Complete Doodle Poll with your Google Calendar information.</a>
      <br><br>
      KEY
      <br>
      "X" indicates checked element, or "This time is good"</td>
      <br>
      "O" indicated unchecked element, or "I am busy"</td>
    </div>
  </body>
</html>
