<?php
use MiMViC as mvc;  

class ajaxHandler implements mvc\ActionHandler
{
  public function exec($params)
  {
    $msg = array(
      'msg'      => 'No data returned',
      'msg_type' => 'error',
      'error'    => true,
      'content'  => '',
    );
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

        if ( empty($_GET['account_id']) || empty($_GET['domains']))
        {
          $msg = array(
            'msg'      => 'Some fields are missing',
            'msg_type' => 'error',
            'error'    => true,
            'content'  => '',
          );
        }
        else
        {
          $owner = false;
          $owner = R::findOne('owner', 'account_id=?',array( $_GET['account_id'] ));
          if ( !( $owner instanceof RedBean_OODBBean ) )
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

            $otherDomainsDefinedInVhost = R::find("domain","apache_vhost_id = ?",array($domain->apache_vhost_id));
            foreach ($otherDomainsDefinedInVhost as $otherDomain) 
            {
              if ( in_array( $otherDomain->id, $_GET['domains'] ) )
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
        }
        break;
      case 'getDomains':
        $serverID = (int) $_GET['serverID'];
        $server = R::load("server",$serverID);

        $linker = mvc\retrieve('beanLinker');
        $vhostIDs = $linker->getKeys($server,'apache_vhost');

        $domains = array();
        foreach ($vhostIDs as $ID )
        {
          $domains = array_merge( $domains, R::find('domain', 'apache_vhost_id=?',array($ID)) );
        }

        if ( !empty($domains) )
        {
          $html = '<h1>Domains in vhosts on server (excluding www aliases)</h1><div class="domains list">';
          foreach ($domains as $domain) 
          {
            // ignore www aliases
            if ( $domain->sub == 'www' && $domain->type == 'alias')
            {
              continue;
            }
            // TODO: dns_info is missing
            $html .= '<div class="domain">
              <div class="status">'. (!empty($domain->dns_info) ? '<img src="/design/desktop/images/error.png" title="'.$domain->dns_info.'" class="icon"/>' : '').'</div>
              <div class="name'. ($domain->is_active ? '' : ' inactive')  .'">'. (($domain->type == 'alias') ? '- ' : '') .'<a href="http://'.$domain->getFQDN().'">'. $domain->getFQDN() .'</a></div>
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
              $owners  = R::related( $result, 'owner' );
              $servers = R::related( $result, 'server' );
              $serverDesc = array();
              foreach ($servers as $server) 
              {
                $serverDesc[] = $server->name;
              }

              // there should only be one owner
              $owner = '';
              if ( !empty($owners) )
              {
                $o = array_shift($owners);
                $owner = ' owned by <a href="'. sprintf( mvc\retrieve('config')->sugarAccountUrl,  $o->account_id ) .'">'. $o->name .'</a> ';
              }

              $desc = '<td>'. 
                (!empty($result->dns_info) ? '<img src="/design/desktop/images/error.png" title="'.$result->dns_info.'" class="icon"/> ' : '') .'</td><td><a'.($result->is_active ? '' : ' class="inactive"').' href="http://'.$result->name.'">'. $result->name .'</a></td><td>'. $owner .'exist on server'. ( (count($serverDesc)>0) ? 's' : '') .': '. implode(',',$serverDesc).'</td>';
              break;
          }
          $json[] = array( 'id' => $result->id, 'label' => $result->name, 'value' => 'snaps', 'desc' => '<tr class="line '.$type.'">'.$desc.'</tr>' );
        }

        die (json_encode($json));
        break;
      case 'missingDomRelation':

        $servers = R::find( "server", "type = ?", array('xenu') );
        $content = array();

        foreach ( $servers as $server )
        {
          $dom0 = false;
          $dom0 = R::getParent( $server );
          if ( !($dom0 instanceof RedBean_OODBBean ) || empty( $dom0->name ) )
          {
            $content[] = $server->name;
          }
        }

        if ( empty($content) )
        {
          $msg = array(
            'msg'      => 'No servers found',
            'msg_type' => 'ok',
            'error'    => true,
            'content'  => '',
          );
        }
        else
        {
          $msg = array(
            'msg'      => 'ok',
            'msg_type' => 'ok',
            'error'    => false,
            'content'  => implode('<br/>',$content),
          );
        }
        break;
      case 'missingFieldsOnServer':

        $servers = R::find( "server");
        $content = array();
        $allowedMissing = array( 'comment' );

        foreach ( $servers as $server ) 
        {
          $missingFields = array();
          foreach ( $server->getIterator() as $key => $value )
          {
            if ( in_array($key,$allowedMissing))
            {
              continue;
            }

            if ( empty( $value ) || is_null($value) )
            {
              $missingFields[] = $key;
            }
          }
          if ( !empty($missingFields) )
          {
            $content[] = $server->id.' is missing: '. implode(', ',$missingFields);
          }
        }

        $msg = array(
          'msg'      => 'ok',
          'msg_type' => 'ok',
          'error'    => false,
          'content'  => implode('<br/>',$content),
        );
        break;

      case 'inactiveDomains':

        $domains = R::find( "domain", "is_active=?",array(false));
        $content = array();

        foreach ( $domains as $domain )
        {
          $server    = R::load( "server", $domain->server_id );
          $content[] = $domain->name .' on '. $server->name .' last updated '. date( 'd-m-Y H:i:s', $domain->updated );
        }

        if ( empty($content) )
        {
          $msg = array(
            'msg'      => 'No inactive domains found',
            'msg_type' => 'ok',
            'error'    => true,
            'content'  => '',
          );
        }
        else
        {
          $msg = array(
            'msg'      => 'ok',
            'msg_type' => 'ok',
            'error'    => false,
            'content'  => implode('<br/>',$content),
          );
        }
        break;
      case 'notUpdatedRecently':

        $content = array();
        $type = $params['segments'][0][0];
        $ts   = mktime();

        switch ($type) 
        {
          case 'domains':
            $results = R::find( "domain", "updated < ?", array( $ts ) );
            foreach ( $results as $domain )
            {
              $content[] = $domain->getFQDN();
            }
            break;
          case 'servers':
            $results = R::find( "server", "updated < ?", array( $ts ) );
            foreach ( $results as $result )
            {
              $content[] = $result->name;
            }
            break;
        }


        if ( empty($content) )
        {
          $msg = array(
            'msg'      => 'No '. $type .' not updated the last 3 days',
            'msg_type' => 'ok',
            'error'    => true,
            'content'  => '',
          );
        }
        else
        {
          $msg = array(
            'msg'      => 'ok',
            'msg_type' => 'ok',
            'error'    => false,
            'content'  => implode('<br/>',$content),
          );
        }
        break;

      case 'editServerComment':

        $serverID = (int) $_GET['serverID'];
        $server = R::load( "server", $serverID );

        if ( $server instanceof RedBean_OODBBean )
        {
          $content = '<form action="/service/ajax/saveServerComment/json/?serverID='.$serverID.'" method="post" id="serverCommentForm">
            <p>
            <textarea name="comment" rows="10" cols="50">'. $server->comment .'</textarea><br/>
            <input type="submit" name="serverCommentSaveAction" value="Save" />
            </p>
            </form';

          $msg = array(
            'msg'      => 'ok',
            'msg_type' => 'ok',
            'error'    => false,
            'content'  => $content,
          );
        }
        else
        {
          $msg = array(
            'msg'      => 'Unknown server',
            'msg_type' => 'error',
            'error'    => true,
            'content'  => '',
          );
        }
        break;
      case 'saveServerComment':

        $comment = $_GET['comment'];
        $serverID = (int) $_GET['serverID'];
        $server = R::load( "server", $serverID );
        $server->comment = $comment;
        R::store($server);

        $msg = array(
          'msg'      => 'Comment stored',
          'msg_type' => 'ok',
          'error'    => false,
          'content'  => '',
        );

        break;
      case 'setEnabledFields':

        $type = $_GET['type'];
        $availableFields = getAvaliableFields($type);
        $enabledFields = array();

        foreach ( $_GET['field'] as $key )
        {
          if ( isset($availableFields[$key]) )
          {
            $enabledFields[$key] = $availableFields[$key];
          }
        }

        setcookie('enabledFields', serialize( array( $type => $enabledFields) ), time()+36000, '/' );
        $msg = array(
          'msg'      => 'ok',
          'msg_type' => 'ok',
          'error'    => false,
          'content'  => '',
        );
        break;
      case 'getServerList':
        $data['hasFieldSelector'] = true;
        $data['avaliableFields']  = getAvaliableFields('servers');
        $data['enabledFields']    = getEnabledFields('servers');
        $data['serversGrouped']   = getGroupedByType();
        $data['template'] = 'design/desktop/templates/servers_list.tpl.php';
        ob_start();
        mvc\render($data['template'], $data);
        $content = ob_get_clean();

        $msg = array(
          'msg'      => 'ok',
          'msg_type' => 'ok',
          'error'    => false,
          'content'  => $content,
        );
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
