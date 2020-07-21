<?php
// Example request:
// https://dyn2ds.gbt.cc/nic/update?hostname=example.com&myip=127.0.0.1

if(isset($_GET["path"]) && $_GET["path"] == "nic/update"){
  $ip = $_SERVER['REMOTE_ADDR'];
  if(!empty($_GET["myip"])){
    $ip = $_GET["myip"];
  }

  $server = "api.domeneshop.no";
  $path = "/v0/dyndns/update?hostname=" . $_GET["hostname"] . "&myip=" . $ip;
  $remote_url = "https://" . $server . $path;

  $opts = array(
    'http'=>array(
      'method' => "GET",
      'header' => "Authorization: " . $_SERVER['HTTP_AUTHORIZATION'] . "\r\n" .
        "Host: " . $server . "\r\n" .
        "User-Agent: DYN2DS.GBT.CC - dyn.com to Domeneshop API translator for DDClient. Contact me(at)gbt.cc"
    )
  );

  $context = stream_context_create($opts);

  $file = @file_get_contents($remote_url, false, $context);
  header("x-dyn2ds-remote_url: " . $remote_url);
  header("x-dyn2ds-auth: " . $_SERVER['HTTP_AUTHORIZATION']);

  // https://help.dyn.com/remote-access-api/return-codes/
  if($http_response_header[0] == "HTTP/1.1 404 Not Found"){
    echo "nohost";
  } else if($http_response_header[0] == "HTTP/1.1 401 Unauthorized"){
    echo "badauth";
  } else if($http_response_header[0] == "HTTP/1.1 204 No Content"){
    echo "good " . $ip;
  } else {
    echo "badagent";
    error_log($remote_url . json_encode($http_response_header));
  }
} else {
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
