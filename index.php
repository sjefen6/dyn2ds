<?php
// Example request:
// https://a:b@dyn2ds.gbt.cc/nic/update?hostname=example.com&myip=127.0.0.1

// Check if this is an DDNS client, or a user looking for information
if(isset($_GET["path"]) && $_GET["path"] == "nic/update"){
  // Set the source ip-address
  $ip = $_SERVER['REMOTE_ADDR'];
  // IF the client provided an ip, lets use that instead
  if(!empty($_GET["myip"])){
    $ip = $_GET["myip"];
  }

  // Target API server
  $server = "api.domeneshop.no";
  // Target path and get parameters
  $path = "/v0/dyndns/update?hostname=" . $_GET["hostname"] . "&myip=" . $ip;
  $remote_url = "https://" . $server . $path;

  // Set http parameters for the http request we will be sending to the target
  $opts = array(
    'http'=>array(
      'method' => "GET",
      'header' => "Authorization: " . $_SERVER['HTTP_AUTHORIZATION'] . "\r\n" . // Basic HTTP authentication
        "Host: " . $server . "\r\n" . // RFC 7230 - HTTP/1.1
        "User-Agent: DYN2DS.GBT.CC - dyn.com to Domeneshop API translator for DDClient. Contact me(at)gbt.cc" // User agent to be nice
    )
  );
  $context = stream_context_create($opts);

  // Send the request to the target
  $file = @file_get_contents($remote_url, false, $context);

  // some debuging output
  header("x-dyn2ds-remote_url: " . $remote_url);
  header("x-dyn2ds-auth: " . $_SERVER['HTTP_AUTHORIZATION']);

  // Translating some responses
  // https://help.dyn.com/remote-access-api/return-codes/
  if($http_response_header[0] == "HTTP/1.1 404 Not Found"){
    // domain not found
    echo "nohost";
  } else if($http_response_header[0] == "HTTP/1.1 401 Unauthorized"){
    // Unauthorized
    echo "badauth";
  } else if($http_response_header[0] == "HTTP/1.1 400 Bad Request"){
    // Unauthorized, most likely empty token and secret "Authorization: Basic Og==" (Og== is ":")
    echo "badauth";
  } else if($http_response_header[0] == "HTTP/1.1 204 No Content"){
    // Successful operation. We are not checking if the address actually changed.
    echo "good " . $ip;
  } else {
    // Something new happened
    echo "badagent";
    error_log($remote_url . json_encode($http_response_header));
  }
} else {
  // Explanation to users
  echo "<h1>DDNS translator for Domeneshop API</h1><br>";
  echo "<p>Compatible with clients that support <a target=\"_blank\" href=\"https://help.dyn.com/remote-access-api/\">Dyn.com DDNS API</a>.</p>";
  echo "<p>In DDClient:<br>";
  echo "Service: dyndns<br>";
  echo "Username: Domeneshop API token<br>";
  echo "Password: Domeneshop API secret<br>";
  echo "Server: dyn2ds.gbt.cc</p>";
  echo "<p>Use <a href=\"https://dyn2ds.gbt.cc\">https://dyn2ds.gbt.cc</a> at your own risk, log contains credentials and support might end abruptly. For self hosting, see <a target=\"_blank\" href=\"http://github.com/sjefen6/dyn2ds/\">http://github.com/sjefen6/dyn2ds/</a>.<br>";
  echo "For more information on Domeneshop API see <a target=\"_blank\" href=\"https://api.domeneshop.no/docs/\">https://api.domeneshop.no/docs/</a>. To get your token and secret, visit: <a target=\"_blank\" href=\"https://www.domeneshop.no/admin?view=api\">https://www.domeneshop.no/admin?view=api</a>.</p>";
}
?>
