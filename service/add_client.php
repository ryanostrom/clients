<?php

include_once '../includes/Client.php';

$client = new Client();

if ($_REQUEST['ax'] == 'add-client') {
  $missing_information = false;
  foreach ($_REQUEST['data'] as $field) {
    if (empty($field)) {
      $missing_information = true;
    }
  }

  if (!$missing_information) {
    $result = $client->create($_REQUEST['data']);
  }
  else {
    http_response_code(200);
    header("Content-Type: application/json");
    echo json_encode(array(
      'success' => false,
      'message' => 'Please fill out all fields.'
    ));
    exit;    
  }
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

