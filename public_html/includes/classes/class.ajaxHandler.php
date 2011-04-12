<?php
use MiMViC as mvc;  

class ajaxHandler implements mvc\ActionHandler
{
  public function exec($params)
  {
    switch ($params['action']) 
    {
      case 'getAccount':
        require 'class.sugarCrmConnector.php';
        try
        {
          $sugar = sugarCrmConnector::getInstance(); 
          $sugar->connect(mvc\retrieve( 'config' )->sugarLogin, mvc\retrieve( 'config' )->sugarPassword);

          $input = '"'. $_GET['term'] .'%"';
          $accounts = array();

          $results = $sugar->getEntryList( 'Accounts', "accounts.account_type = 'Customer' AND accounts.name LIKE $input", 'name', 0, array('name') );
          foreach ($results->entry_list as $result) 
          {
            $accounts[] = (object) array( 'id' => $result->id, 'label' => $result->name_value_list[0]->value, 'value' => $result->name_value_list[0]->value );
          }
        }
        catch(Exception $e)
        {
          // TODO
        }
        die( json_encode( $accounts ) );
        break;
      case 'accountsToDomains':

        $owner = false;
        $owner = R::findOne('owner', 'account_id=?',array( $_GET['account_id'] ));
        if ( $owner === false )
        {
          $owner = R::dispense("owner");
          $owner->name = $_GET['account_name'];
          $owner->account_id = $_GET['account_id'];
          $id = R::store($owner);
        }

        foreach ($_GET['domains'] as $id ) 
        {
          $domain = R::load( 'domain', $id );
          R::associate( $owner, $domain );

          $otherDomainsDefinedInVhost = R::find("domain","vhost_group_key = ?",array($domain->vhost_group_key));
          foreach ($otherDomainsDefinedInVhost as $otherDomain) 
          {
            if ( $otherDomain->type === 'name' )
            {
              continue;
            }
            R::associate( $owner, $otherDomain );
          }
        }

        $domains = getUnrelatedMainDomains();
        $html = '';
        foreach ($domains as $domain) 
        {
          $html .= '<option value="'.$domain->id.'">'.$domain->name.'</option>';
        }

        $msg = array(
          'msg'      => $owner->name .' set as owner of domains',
          'msg_type' => 'ok',
          'error'    => false,
          'content'  => $html,
        );
        break;
      case 'getDomains':
        $server = R::load("server",$_GET['serverID']);
        $domains = R::related($server,'domain');

        if ( !empty($domains) )
        {
          $html = '<div class="domains list">';
          foreach ($domains as $domain) 
          {
            $html .= '<div class="domain">
  <div class="status">'. (!empty($domain->dns_info) ? '<img src="/design/desktop/images/error.png" title="'.$domain->dns_info.'" class="icon"/>' : '').'</div>
  <div class="name'. ($domain->is_active ? '' : ' inactive')  .'"><a href="http://'.$domain->name.'">'. $domain->name .'</a></div>
<br class="cls"/>
</div>';
          }
          $html .= '</div>';

          $msg = array(
            'msg'      => '',
            'msg_type' => 'ok',
            'error'    => false,
            'content'  => $html,
          );
        }
        else
        {
          $msg = array(
            'msg'      => 'No domains found on server',
            'msg_type' => 'warning',
            'error'    => true,
            'content'  => '',
          );
        }

        break;
      case 'search':
        if( isset($params['segments'][0][0]) && isset($params['segments'][0][1]))
        {
          $type  = $params['segments'][0][0];
          $query = $params['segments'][0][1];
        }

        $query = $query .'%';
        if ( isset($params['segments'][0][2]) && $params['segments'][0][2] == 'both')
        {
          $query = '%'.$query;
        }

        $results = R::find($type, 'name LIKE ?',array($query));

        $json = array();

        foreach ($results as $result )
        {
          switch ($type) 
          {
            case 'server':
              $desc = '<td></td><td>'.$result->name .'</td><td>type: '. $result->type  .' ip: '. $result->ip .'</td>';
              break;
            case 'domain':
              $servers = R::related( $result, 'server' );
              $owners  = R::related( $result, 'owner' );
              $serverDesc = array();
              foreach ($servers as $server) 
              {
                $serverDesc[] = $server->name;
              }

              // there should only be one owner
              $owner = '';
              if ( !empty($owners) )
              {
                $owner = ' owned by '. array_shift($owners)->name .' ';
              }

              $desc = '<td>'. 
                (!empty($result->dns_info) ? '<img src="/design/desktop/images/error.png" title="'.$result->dns_info.'" class="icon"/> ' : '') .'</td><td><a'.($result->is_active ? '' : ' class="inactive"').' href="http://'.$result->name.'">'. $result->name .'</a></td><td>'. $owner .'exist on server'. ( (count($serverDesc)>0) ? 's' : '') .': '. implode(',',$serverDesc).'</td>';
              break;
          }
          $json[] = array( 'id' => $result->id, 'label' => $result->name, 'value' => 'snaps', 'desc' => '<tr class="line '.$type.'">'.$desc.'</tr>' );
        }

        die (json_encode($json));
        break;
      default:
          $msg = array(
            'msg'      => 'Unknown action',
            'msg_type' => 'error',
            'error'    => true,
            'content'  => '',
          );
        break;
    };  
    die( json_encode( $msg ) );
  }
}
