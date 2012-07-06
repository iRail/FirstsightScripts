<?php

// RETURNS THE LAST TIME THE PHP FILE HAS BEEN MODIFIED
function fileLastMod() {
    return filemtime($_SERVER['SCRIPT_FILENAME']);
}

// SEND ALL THE HEADERS NEEDED TO VALIDATE CACHE
// !! SHOULD BE DONE BEFORE ANYTHING ELSE !!
function check_cache($timestamp,$cacheTime=604800) {
  
  // DISABLE CACHE IN DEV ENV
  if(isset($_SERVER['DEV']) && $_SERVER['DEV']) return;
  
  // Format the timestamp
  $timestamp = gmdate('D, d M Y H:i:s',$timestamp).' GMT';

  // Send the headers
  session_cache_limiter(FALSE);
  header("Pragma: public");
  header("Cache-Control: public");
  header("Last-Modified: $timestamp");
  header("Expires: ".gmdate('D, d M Y H:i:s',time()+604800).' GMT');

  if(!isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
      return; // IMS don't exist
  }

  if (stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) != $timestamp) {
      return; // IMS exists but doesn't match
  }

  // Nothing has changed since last request
  // Return a 304 and *DON'T RUN* the rest of page
  header('HTTP/1.1 304 Not Modified');
  exit;
}

// CAN BE DONE ANYTIME; THIS IS JUST AN INDICATION TO THE CLIENT
// IT CAN CACHE THE CONTENT. IT'S BETTER TO PROVIDE A LAST-MODIFIED
// USING "CHECK_CACHE_LAST_EDIT".
function allow_cache($cacheTime=604800) {

    // DISABLE CACHE IN DEV ENV
    if(isset($_SERVER['DEV']) && $_SERVER['DEV']) return;

    // ADD IT OTHERWHISE
    header("Pragma: public");
    header("Cache-Control: maxage=".$expires);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
}

?>
