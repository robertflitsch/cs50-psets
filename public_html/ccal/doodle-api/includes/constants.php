<?

/***********************************************************************
 * constants.php
 *
 * Global constants.
 *
 **********************************************************************/

// consumer key, secret, and request url (given by Doodle.com)
define("CKEY", "nn6lzq3agxbuldi84pjp1h28gx7615cf");

define("CSECRET", "wxjss3haigsw2poypxdgs74g7gpbgk3m");

define("URL", "https://doodle.com/api1/");

// constants for xml dom
define("TIME_LEVEL", 3);

define("PARTICIPANT_LEVEL", 5);

// disclaimer message for doodle api limitations
define("DISCLAIMER", "Disclaimer: Due to limitations of the Doodle Api, we can only display about 200 post results (for example, 10 participants with 3 options equates to 30 post results).
                      The Google Calendar-Doodle tool, should still prove very useful, and your post information will be sent to Doodle if you click submit, we just can't display it all here.
                      We are sorry for these limitations, and your poll may be completely viewed at ");

define("THANKS", "Thank you for using Crimson Calendar.");
            
// post limit due to api limitations (not a set number, is ~175-200 depending on length of names)
define("POST_LIM", 175);

?>
