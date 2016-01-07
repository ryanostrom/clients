<?php

include_once '../includes/Client.php';

$client = new Client();

if ($_REQUEST['ax'] == 'add-client') {
  $result = $client->create($_REQUEST['data']);
}
elseif ($_REQUEST['ax'] == 'add-option') {
  $result = $client::getProjectInput($_REQUEST['data']['count']);
}

if ($result) {
  http_response_code(200);
  header("Content-Type: application/json");
  echo json_encode(array(
    'success' => true,
    'result' => $result
  ));
  exit;
}

