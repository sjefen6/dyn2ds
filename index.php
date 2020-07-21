<?php
// Example request:
// https://nc2ds.gbt.cc/update?host=example.com&domain=a&password=b&ip=127.0.0.1

if(isset($_GET["path"]) && $_GET["path"] == "update"){
  $ip = $_SERVER['REMOTE_ADDR'];
  if(!empty($_GET["ip"])){
    $ip = $_GET["ip"];
  }

  $server = "api.domeneshop.no";
  $path = "/v0/dyndns/update?hostname=" . $_GET["host"] . "&myip=" . $ip;
  $remote_url = "https://" . $server . $path;

  $opts = array(
    'http'=>array(
      'method'=>"GET",
      'header' => "Authorization: Basic " . base64_encode($_GET["domain"] . ":" . $_GET["password"]) . "\r\n" .
      "User-Agent: NC2DS.GBT.CC - namecheap to Domeneshop API translator for DDClient. Contact me(at)gbt.cc\r\n"
    )
  );

  $context = stream_context_create($opts);

  $file = @file_get_contents($remote_url, false, $context);
  header($http_response_header[0], true);
  // error_log($http_response_header[0]);
  header("x-nc2ds-remote_url: " . $remote_url);
  header("x-nc2ds-token: " . $_GET["domain"]);
  header("x-nc2ds-secret: " . $_GET["password"]);
  header("x-nc2ds-secret: " . $_GET["password"]);

  print($file);

} else {
  echo "<h1>DDNS translator for Domeneshop API</h1><br>";
  echo "<p>Compatible with clients that support <a target=\"_blank\" href=\"https://www.namecheap.com/support/knowledgebase/article.aspx/29/11/how-do-i-use-a-browser-to-dynamically-update-the-hosts-ip\">Namecheaps DDNS API</a>.</p>";
  echo "<p>In DDClient:<br>";
  echo "Service: Namecheap<br>";
  echo "Username: Domeneshop API token<br>";
  echo "Password: Domeneshop API secret<br>";
  echo "Server: nc2ds.gbt.cc</p>";
  echo "<p>Use <a href=\"https://nc2ds.gbt.cc\">https://nc2ds.gbt.cc</a> at your own risk, log contains credentials and support might end abruptly. For self hosting, see <a target=\"_blank\" href=\"http://github.com/sjefen6/nc2ds/\">http://github.com/sjefen6/nc2ds/</a>.<br>";
  echo "For more information on Domeneshop API see <a target=\"_blank\" href=\"https://api.domeneshop.no/docs/\">https://api.domeneshop.no/docs/</a>. To get your token and secret, visit: <a target=\"_blank\" href=\"https://www.domeneshop.no/admin?view=api\">https://www.domeneshop.no/admin?view=api</a>.</p>";
}
?>
