<!DOCTYPE html>

<html>
  <head>
    <title>Crimson Calendar Doodle Tool: Submitted!</title>
      <div style= "text-align: center">
      Crimson Calendar Doodle Tool: Submitted<br><br><br>
      </div>
  </head>

  <body>
    <div style= "text-align: center">
      Thank you for using Crimson Calendar.
      <br>
      Your poll has been successfully submitted.
      <br><br> 
      <a href="index.php">Home</a>-----
      <?
        // link back to poll
        $url = htmlspecialchars($_GET['url']);
        echo("<a href=\"display_poll.php?url=$url\">Return to poll</a>-----");
      ?>
      <a href="doodle_tool.html">Complete another poll</a>
    </div>
  </body>
</html>
