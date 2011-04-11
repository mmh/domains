<?php
use MiMViC as mvc;  

/**
 * Module for showing servers
 *
 * @packaged default
 * @author Henrik Farre <hf@bellcom.dk>
 **/
class servers
{
  private $views = array();

  /**
   * Setup views
   *
   * @return void
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function __construct()
  {
    $this->views = array(
      'table'   => array( 'tpl' => 'templates/servers_table.tpl.php'),
      'list'    => array( 'tpl' => '' ),
      );
  }

  /**
   * Checks if a view exists in the current module
   *
   * @param string $view THe name of the view
   * @return bool
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function hasView( $view )
  {
    if ( isset($this->views[$view] ))
    {
      return true;
    }
    return false;
  }

  /**
   * Renders the selected view 
   *
   * @param string $view The name of the view
   * @param array $data Data for the template from outside this module
   * @return bool
   * @author Henrik Farre <hf@bellcom.dk>
   **/
  public function executeView( $view, array $data )
  {
    if ( $this->hasView($view) )
    {
      switch ($view) 
      {
        case 'list':
        default:
          break;
      }

    }
    else
    {
      throw new InvalidArgumentException( 'The requested view "'.$view.'" does not exist in this module' );
    }
    return true;
  }


} // END class servers
