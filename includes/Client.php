<?php
include_once 'Repo.php';

class Client {
  private $repo;

  public function __construct()
  {
    $this->repo = new Repo();
  }

  /**
   * @param array $args keys:
   *   string client_name
   *   string alias
	 *   array  project
	 *   	array  number
	 *     string project_name
   *     string repo
   *     string deployment
   *     string staging-url
   *     string staging-host
   *     string staging-sub_host
   *     string staging-directory
   *     string staging-database
   *     string staging-database_table
   *     string production-url
   *     string production-host
   *     string production-sub_host
   *     string production-directory
   *     string production-database
   *     string production-database_table
   */
  public function create($args = array())
  {
    $this->repo->setSql($this->setClientSql($args));
    $client = $this->repo->getArraySingle($this->getClientIdSql($args['client_name']));

    foreach ($args['project'] as $input) {
      $input['client_id'] = $client['id'];
      $this->repo->setSql($this->setProjectSql($input));

      $project = $this->repo->getArraySingle($this->getProjectIdSql($input['project_name']));
      $input['project_id'] = intval($project['id']);
      $this->repo->setSql($this->setProjectEnvironmentSql($input));
    }

    return true;
  }

  private function getClientIdSql($name)
  {
    return <<<SQL
      SELECT client.id
        FROM client
       WHERE client.name = '{$name}'
SQL;
  }

  private function getProjectIdSql($name)
  {
    return <<<SQL
      SELECT project.id
        FROM project
       WHERE project.name = '{$name}'
SQL;
  }

  private function setClientSql($args)
  {
    return <<<SQL
      INSERT INTO client (name, alias)
           VALUES (
                     '{$args['client_name']}',
                     '{$args['alias']}'
                   )
SQL;
  }

  private function setProjectSql($args)
  {
    return <<<SQL
      INSERT INTO project (client_id, name, repo, deployment)
           VALUES (
                     '{$args['client_id']}',
                     '{$args['project_name']}',
                     '{$args['repo']}',
                     '{$args['deployment']}'
                   )
SQL;
  }

  private function setProjectEnvironmentSql($args)
  {
    return <<<SQL
      INSERT INTO project_environment (client_id, project_id, type, url, host, sub_host, directory, db, db_table)
           VALUES (
                     '{$args['client_id']}',
                     '{$args['project_id']}',
                     'staging',
                     '{$args['staging-url']}',
                     '{$args['staging-host']}',
                     '{$args['staging-sub_host']}',
                     '{$args['staging-directory']}',
                     '{$args['staging-database']}',
                     '{$args['staging-database_table']}'
                   ),
                  (
                     '{$args['client_id']}',
                     '{$args['project_id']}',
                     'production',
                     '{$args['production-url']}',
                     '{$args['production-host']}',
                     '{$args['production-sub_host']}',
                     '{$args['production-directory']}',
                     '{$args['production-database']}',
                     '{$args['production-database_table']}'
                   )
SQL;
  }

  public static function createInput($id, $placeholder, $options = array()) {
    $autofocus = isset($options['autofocus']) ? 'autofocus' : '';
    $project = isset($options['project']) ? "data-project=\"{$options['project']}\"" : '';

    return <<<HTML
      <input type="text" {$project} id="{$id}" placeholder="{$placeholder}" {$autofocus}>
HTML;
  }

  public static function getProjectInput($count)
  {
    $project_inputs = array(
      array(
        'id' => 'project_name',
        'placeholder' => 'Project Name',
      ),
      array(
        'id' => 'repo',
        'placeholder' => 'Repository URL',
      ),
      array(
        'id' => 'deployment',
        'placeholder' => 'Deployment (i.e., Wercker, Pantheon.io)',
      ),
    );

    $environment_inputs = array(
      array(
        'id' => 'url',
        'placeholder' => 'URL',
      ),
      array(
        'id' => 'host',
        'placeholder' => 'Host',
      ),
      array(
        'id' => 'sub_host',
        'placeholder' => 'Sub Host',
      ),
      array(
        'id' => 'directory',
        'placeholder' => 'Directory',
      ),
      array(
        'id' => 'database',
        'placeholder' => 'Database',
      ),
      array(
        'id' => 'database_table',
        'placeholder' => 'Database Table',
      ),
    );

    $close = $count > 1 ? '<i id="remove-project" class="fa fa-close"></i>' : '';

    $html = <<<HTML
    	<div class="project">
    		{$close}
	      <h4>Project {$count}</h4>
	      <p>Generic</p>
HTML;

    foreach ($project_inputs as $key => $input) {
      $html .= self::createInput($input['id'], $input['placeholder'], array('project' => $count));
    }

    $html .= <<<HTML
      <p>Staging Environment</p>
HTML;

    foreach ($environment_inputs as $key => $input) {
      $html .= self::createInput('staging-' . $input['id'], $input['placeholder'], array('project' => $count));
    }

    $html .= <<<HTML
      <p>Production Environment</p>
HTML;

    foreach ($environment_inputs as $key => $input) {
      $html .= self::createInput('production-' . $input['id'], $input['placeholder'], array('project' => $count));
    }

    $html .= "</div>";

    return $html;
  }

  public function renderClients($filter = array()) {
    $this->filter = $filter;
    $clients = $this->loadClients();

    $html = array();
    foreach ($clients as $client_id => $client) {
      $string = <<<HTML
        <div class="client hide-projects" data-client-name="{$client['client_name']}" data-alias="{$client['alias']}">
          <div class="title">{$client['client_name']} <span id="alias">({$client['alias']})</span></div>
HTML;
      if (!empty($client['projects'])) {
        $string .= <<<HTML
          <div class="projects hide">
HTML;
        foreach ($client['projects'] as $project_id => $project) {
          $string .= <<<HTML
            <div class="project" data-project-name="{$project['project_name']}">
              <div class="sub-title">{$project['project_name']}</div>
              <table class="details">
                {$this->renderTableRow('Repo', $project['repo'])}
                {$this->renderTableRow('Deployment', $project['deployment'])}
HTML;
          if (!empty($project['environments'])) {
            $string .= "<tr><td colspan=\"2\" class=\"environments\">";
            foreach ($project['environments'] as $type => $environment) {
              $type = ucfirst($type);
              $string .= <<<HTML
                <table class="environment">
                  {$this->renderTableRow($type, '', true)}
                  {$this->renderTableRow('URL', $environment['url'])}
                  {$this->renderTableRow('Primary Server Host', $environment['host'])}
                  {$this->renderTableRow('Secondary Server Host', $environment['sub_host'])}
                  {$this->renderTableRow('Directory', $environment['directory'])}
                  {$this->renderTableRow('Database Host', $environment['db_conn'])}
                  {$this->renderTableRow('Database Table', $environment['db'])}
                </table>
HTML;
            }
            $string .= "</td></tr>";
          }
          $string .= "</table></div>";
        }

        $string .= "</div>";
      }

      $string .= "</div>";
      $html[] = $string;
    }

    return implode($html);
  }

  private function loadClients()
  {
    $projects = $this->getProjects();

    $clients = array();
    foreach ($projects as $project) {
      if (!isset($clients[$project['client_id']])) {
        $clients[$project['client_id']] = array(
          'client_name' => $project['client_name'],
          'alias' => $project['alias'],
          'projects' => array()
        );
      }

      if (!isset($clients[$project['client_id']]['projects'][$project['project_id']])) {
        $clients[$project['client_id']]['projects'][$project['project_id']] = array(
          'project_name' => $project['project_name'],
          'repo' => $project['repo'],
          'deployment' => $project['deployment'],
          'environments' => array()
        );
      }

      $clients[$project['client_id']]['projects'][$project['project_id']]['environments'][$project['type']] = array(
        'url' => $project['url'],
        'host' => $project['host'],
        'sub_host' => $project['sub_host'],
        'directory' => $project['directory'],
        'db_conn' => $project['db'],
        'db' => $project['db_table']
      );
    }

    return $clients;
  }

  private function getProjects()
  {
    $where = '';

    if (isset($this->filter['where'])) {
    }

    $sql = <<<SQL
      SELECT client.id as client_id
           , client.name as client_name
           , client.alias
           , project.id as project_id
           , project.name as project_name
           , project.repo
           , project.deployment
           , project_environment.type
           , project_environment.url
           , project_environment.host
           , project_environment.sub_host
           , project_environment.directory
           , project_environment.db
           , project_environment.db_table
        FROM project_environment
        JOIN project
          ON project_environment.project_id = project.id
        JOIN client
          ON project.client_id = client.id
             {$where}
    ORDER BY client.id
           , project.id
SQL;
    return $this->repo->getArray($sql);
  }

  private function renderTableRow($key, $value, $header = false) {
    $tag = $header ? 'th' : 'td';
    $colspan = $header ? 'colspan="2"' : '';
    $second_item =  $header ? '' : "<td class=\"item\">{$value}</td>";

    return <<<HTML
        <tr>
          <{$tag} class="item primary" {$colspan}>{$key}</{$tag}>
          {$second_item}
        </tr>
HTML;
  }
}