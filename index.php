<?php

include_once 'includes/main.php';
include_once 'includes/Client.php';

echo_bootstrap(get_html(), array('js' => 'view_client'));

function get_html() {
  $add_client_button = "<button class=\"add new\"><i class=\"fa fa-plus-circle\"></i>Add Client</button>";
  $client = new Client();
  return  $add_client_button . $client->renderClients();
}