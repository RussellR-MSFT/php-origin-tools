<?php
function getCpuLoad() {
  // Try to use sys_getloadavg if available (Unix systems)
  if (function_exists('sys_getloadavg')) {
    $load = sys_getloadavg();
    if ($load && isset($load[0])) {
      return $load[0];
    }
  }
  // Fallback for Windows or if sys_getloadavg is unavailable
  if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
    // Use WMIC to get CPU load percentage
    @exec('wmic cpu get loadpercentage /value', $output);
    foreach ($output as $line) {
      if (preg_match('/LoadPercentage=(\d+)/', $line, $matches)) {
        return $matches[1];
      }
    }
  }
  return 'N/A';
}

header('x-as-current-load: ' . getCpuLoad());
?>

<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$isCurl = stripos($userAgent, 'curl') !== false;
?>

<?php if ($isCurl): ?>
PHP Origin Tools (Basic Output)

Host name: <?php echo gethostname(); ?>

Raw query string: <?php echo $_SERVER['QUERY_STRING']; ?>

Request Headers:
  <?php
  $requestHeaders = function_exists('apache_request_headers') ? apache_request_headers() : [];
  foreach ($requestHeaders as $header => $value) {
      echo "$header: $value\n";
  }
  ?>

POST Values:
<?php print_r($_POST); ?>
<?php else: ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <!--
    Modified from the Debian original for Ubuntu
    Last updated: 2016-11-16
    See: https://launchpad.net/bugs/1288690
  -->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>PHP Origin Tools</title>
    <link rel="stylesheet" href="styles.css">
  </head>    <body>
      <div class="main_page">
        <div class="page_header floating_element">
         <img src="/tools.png" alt="tools image" class="floating_element"/> 
          <span class="floating_element">
            PHP Origin Tools</br>
            <?php echo date(DATE_RFC2822);?>
          </span>
        </div>
        <div class="content_section floating_element">
          <div class="section_header section_header_red">
            <div id="about"></div>
            Host name: <?php echo gethostname(); ?>
          </div>
          <div class="content_section_text">
            <p>
              This page provides some helpful tools to reflect headers and other data that is sent via GET requests.  This helps in
              troubleshooting application layer issues by providing visibility into the headers seen on the backend or origin behind various
              networking load balancers such as Azure Application Gateways, Content Delivery Network endpoints, or Azure Front Doors.
            </p>
          </div>
          <div class="section_header">
              <div id="querystrings"></div>
              Query strings
          </div>
          <div class="content_section_text">
              <?php
              $queryString = $_SERVER['QUERY_STRING'];
              echo "Raw query string: $queryString";
              ?>
          </div>
          <div class="section_header">
              <div id="requestheaders"></div>
              Request Headers
          </div>
          <div class="content_section_text">
            <?php
             $requestHeaders = function_exists('apache_request_headers') ? apache_request_headers() : [];
             foreach ($requestHeaders as $header => $value) {
              echo "$header: $value <br />\n";
             }
            ?>
          </div>
          <div class="section_header">
              <div id="postvalues"></div>
              POST Values
              </div>
          <div class="content_section_text">
            <?php 
             echo "<pre>"; print_r($_POST) ;  echo "</pre>";  
            ?>
          </div>
          <div class="section_header">
             <div id="websocketsdemolink"></div>
             Websockets Demo
          </div>
          <div class="content_section_text">
             <a href="websocketsdemo.php">Websockets Demo Chat Room</a>
             <p>
                Provides a quick easy way to see websockets in action.  Requires a separate websockets server.
             </p>
          </div>
        </div>
      </div>
      <div class="validator">
      </div>
    </body>
    </html>     
  <?php endif; ?>    
