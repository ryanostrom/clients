<?php

include_once 'includes/main.php';
include_once 'includes/Client.php';

echo_bootstrap(get_html(), array('js' => 'add_client'));

function get_html() {
  $client_inputs = array(
    array(
      'id' => 'client_name',
      'placeholder' => 'Client Name',
    ),
    array(
      'id' => 'alias',
      'placeholder' => 'Client Acronym',
    ),
  );

  $html = <<<HTML
    <div id="add-client">
    <h2>Add Client</h2>
    <h3>Client Details</h3>
HTML;

  foreach ($client_inputs as $key => $input) {
    $autofocus = $key == 0 ? true : false;
    $html .= Client::createInput($input['id'], $input['placeholder'], array('autofocus' => $autofocus));
  }

  $html .= <<<HTML
  	<h3>Project Details</h3>
HTML;

	$html .= Client::getProjectInput('1');

  $html .= <<<HTML
      <button class="add"><i class="fa fa-plus-circle"></i>Project</button>
      <button id="create" class="primary">Create</button>
    </div>
HTML;

	return $html;
}