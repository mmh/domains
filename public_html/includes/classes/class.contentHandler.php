<?php
use MiMViC as mvc;  

class contentHandler implements mvc\ActionHandler
{
  public function exec($params)
  {
    $data = array();
    $designPath = 'design/'.mvc\retrieve('theme').'/';

    $module = isset($params['module']) ? $params['module'] : 'servers';
    $view   = isset($params['view']) ? $params['view'] : 'index';
                 
    $data['module']     = $module;
    $data['view']       = $view;
    $data['designPath'] = $designPath;
    $data['title']      = 'Page title'; // TODO: should be sat by module

    mvc\render($designPath.'templates/header.tpl.php', $data);
    mvc\render($designPath.'templates/top_menu.tpl.php', $data);

    if ( mvc\retrieve('debug') )
    {
      $data['values']['params'] = $params;
      mvc\render($designPath.'templates/debug.tpl.php',$data);
    }

    switch ($module) 
    {
      case 'domains':
        $accountToDomains = array();
        $owners = R::find('owner');

        foreach ( $owners as $owner ) 
        {
          $domains = R::related( $owner, 'domain' );
          $accountToDomains[ $owner->account_id ]['owner'] = $owner;
          $accountToDomains[ $owner->account_id ]['domains'] = $domains;
        }
        $data['accountToDomains'] = $accountToDomains;

        mvc\render($designPath.'templates/domains.tpl.php', $data);
        break;

      case 'accounts':
        $data['domains'] = getUnrelatedMainDomains();
        mvc\render($designPath.'templates/accounts.tpl.php', $data);
        break;

      case 'servers':
        $data['servers_grouped'] = getGroupedByType();
        mvc\render($designPath.'templates/servers_list.tpl.php', $data);
        break;

      case 'search':
        mvc\render($designPath.'templates/search.tpl.php', $data);
        break;

      case 'cleanup':
        mvc\render($designPath.'templates/cleanup.tpl.php', $data);
        break;

      default:
        $data['error'] = array(
          'code' => '404',
          'msg' => 'Page not found',
        );
        mvc\render($designPath.'templates/error.tpl.php', $data);
        break;
    }

    mvc\render($designPath.'templates/footer.tpl.php', $data);
  }
}
