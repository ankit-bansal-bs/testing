<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: IndexController.php 9775 2012-08-31 13:25:57Z pamela $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Album_IndexController extends Core_Controller_Action_Standard
{
  public function browseAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ) return;
    
    // Moved to Albums/widgets/gutter-search/Controller.php
    // 
    // $search_form = $this->view->search_form = new Album_Form_Search();
    // if ($this->getRequest()->isPost() && $search_form->isValid($this->getRequest()->getPost())) {
    //   $this->_helper->redirector->gotoRouteAndExit(array(
    //     'page'   => 1,
    //     'sort'   => $this->getRequest()->getPost('sort'),
    //     'category_id' => $this->getRequest()->getPost('category_id'),
    //     'search' => $this->getRequest()->getPost('search'),
    //   ));
    // } else {
    //   $search_form->getElement('search')->setValue($this->_getParam('search'));
    //   $search_form->getElement('sort')->setValue($this->_getParam('sort'));
    //   if($search_form->getElement('category_id')) $search_form->getElement('category_id')->setValue($this->_getParam('category_id'));
    // }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    // moved to Albums/widgets/browse-menu/Controller.php
    // // Get navigation
    // $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
    //   ->getNavigation('album_main');

    // Get params
    switch($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }


    // Prepare data
    $table = Engine_Api::_()->getItemTable('album');
    if( !in_array($order, $table->info('cols')) ) {
      $order = 'modified_date';
    }
    
    $select = $table->select()
      ->where("search = 1")
      ->order($order . ' DESC');

    $user_id = $this->_getParam('user');
    if ($user_id) $select->where("owner_id = ?", $user_id);
    if ($this->_getParam('category_id')) $select->where("category_id = ?", $this->_getParam('category_id'));

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%'.$this->_getParam('search').'%');
    }
    
    // Create new array filtering out private albums
    $album_select = $select;
    $new_select = array();
    $i = 0;
    foreach($album_select->getTable()->fetchAll($album_select) as $album )  {
      if (Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')){
        $new_select[$i++] = $album;
      }
    }

    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    
    $paginator = $this->view->paginator = Zend_Paginator::factory($new_select);
    $paginator->setItemCountPerPage($settings->getSetting('album_page', 28));
    $paginator->setCurrentPageNumber( $this->_getParam('page') );
    
    $searchForm = new Album_Form_Search();
    $searchForm->getElement('sort')->setValue($this->_getParam('sort'));
    $searchForm->getElement('search')->setValue($this->_getParam('search'));
    $category_id = $searchForm->getElement('category_id');
    if ($category_id) {
      $category_id->setValue($this->_getParam('category_id'));
    }
    $this->view->searchParams = $searchForm->getValues();

    // Render
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
  }

  public function manageAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

    $search_form = $this->view->search_form = new Album_Form_Search();
    if ($this->getRequest()->isPost() && $search_form->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page'   => 1,
        'sort'   => $this->getRequest()->getPost('sort'),
        'search' => $this->getRequest()->getPost('search'),
        'category_id' => $this->getRequest()->getPost('category_id'),
      ));
    } else {
      $search_form->getElement('search')->setValue($this->_getParam('search'));
      $search_form->getElement('sort')->setValue($this->_getParam('sort'));
      if($search_form->getElement('category_id')) $search_form->getElement('category_id')->setValue($this->_getParam('category_id'));
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('album_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('album_quick');

    // Get params
    $this->view->page = $page = $this->_getParam('page');

    // Get params
    switch($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }
    
    // Prepare data
    $user = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable('album');
    
    if( !in_array($order, $table->info('cols')) ) {
      $order = 'modified_date';
    }

    $select = $table->select()
      ->where('owner_id = ?', $user->getIdentity())
      ->order($order . ' DESC');;

    if ($this->_getParam('category_id')) $select->where("category_id = ?", $this->_getParam('category_id'));

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%'.$this->_getParam('search').'%');
    }
    
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');
    
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);
  }



  public function uploadAction()
  {

    if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) return $this->_forward('upload-photo', null, null, array('format' => 'json'));

    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('album_main');

    // Get form
    $this->view->form = $form = new Album_Form_Album();

    if( !$this->getRequest()->isPost() )
    {
      if( null !== ($album_id = $this->_getParam('album_id')) )
      {
        $form->populate(array(
          'album' => $album_id
        ));
      }
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
     }

    $db = Engine_Api::_()->getItemTable('album')->getAdapter();
    $db->beginTransaction();

    try
    {
      $album = $form->saveValues();

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'album_id' => $album->album_id), 'album_specific', true);
  }

  public function uploadPhotoAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();

      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity()
      ));
      $photo->save();
      
      $photo->order = $photo->photo_id;
      $photo->setPhoto($_FILES['Filedata']);
      $photo->save();
      
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->photo_id;

      $db->commit();

    } catch( Album_Model_Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $this->view->translate($e->getMessage());
      throw $e;
      return;
      
    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }
}