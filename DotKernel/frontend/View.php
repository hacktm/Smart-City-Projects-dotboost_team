<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    Frontend
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id: View.php 785 2014-03-10 22:06:35Z julian $
*/

/**
* View Model
* abstract over the Dot_Template class
* @category   DotKernel
* @package    Frontend
* @author     DotKernel Team <team@dotkernel.com>
*/

class View extends Dot_Template
{
	/**
	 * Singleton instance
	 * @access protected
	 * @static
	 * @var Dot_Template
	 */
	protected static $_instance = null;
	/**
	 * Returns an instance of Dot_View
	 * Singleton pattern implementation
	 * @access public
	 * @param string $root     Template root directory
	 * @param string $unknowns How to handle unknown variables
	 * @param array  $fallback Fallback paths
	 * @return Dot_Template
	 */
	public static function getInstance($root = '.', $unknowns = 'remove', $fallback='')
	{
		if (null === self::$_instance) 
		{
			self::$_instance = new self($root, $unknowns, $fallback);
		}
		return self::$_instance;
	}	
	/**
	 * Initalize some parameter
	 * @access public
	 * @return void
	 */	
	public function init()
	{
		$this->requestModule = Zend_Registry::get('requestModule');
		$this->requestController = Zend_Registry::get('requestController');
		$this->requestAction = Zend_Registry::get('requestAction');
		$this->config = Zend_Registry::get('configuration');
		$this->seo = Zend_Registry::get('seo');
	}
	/**
	 * Set the template file
	 * @access public 
	 * @return void
	 */
	public function setViewFile()
	{
		$this->setFile('tpl_index', 'index.tpl');
	}
	/**
	 * Set different paths url(site, templates, images)
	 * @access public
	 * @return void
	 */
	public function setViewPaths()
	{
		$this->setVar('TEMPLATES_URL', $this->config->website->params->url . TEMPLATES_DIR);
		$this->setVar('IMAGES_URL', $this->config->website->params->url . IMAGES_DIR . '/' .$this->requestModule);
		$this->setVar('SITE_URL', $this->config->website->params->url);
	}
	/**
	 * Set SEO values
	 * @access public
	 * @param string $pageTitle [optional]
	 * @return void
	 */
	public function setSeoValues($pageTitle = '')
	{
		$this->setVar('PAGE_KEYWORDS', $this->seo->defaultMetaKeywords);
		$this->setVar('PAGE_DESCRIPTION', $this->seo->defaultMetaDescription);
		$this->setVar('PAGE_TITLE', $this->seo->defaultPageTitle .  ' | ' . $pageTitle);
		$this->setVar('PAGE_CONTENT_TITLE', $pageTitle);
		$this->setVar('SITE_NAME', $this->seo->siteName);
		$this->setVar('CANONICAL_URL', $this->seo->canonicalUrl);
	}
	/**
	 * Display the menus
	 * @access public 
	 * @return void
	 */
	public function setMenu()
	{
	    $session = Zend_Registry::get('session');
		$dotAuth = Dot_Auth::getInstance();
		$registry = Zend_Registry::getInstance();
		
		// this template variable will be replaced with "selected"
		$selectedItem = "SEL_" . strtoupper($registry->requestController . "_" . $registry->requestAction);
		
		// top menu
		$this->setFile('tpl_menu_top', 'blocks/menu_top.tpl');
        $this->setBlock('tpl_menu_top', 'student', 'student_block');
        $this->setBlock('tpl_menu_top', 'tutor', 'tutor_block');
        $this->setBlock('tpl_menu_top', 'teacher', 'teacher_block');
        // add selected to the correct menu item
        $this->setVar($selectedItem, 'selected');
        
        if ($dotAuth->hasIdentity('user'))
        {   $this->setVar("USERNAME", $session->user->firstName);
            $this->setVar("TYPE", $session->user->type);
            if ($session->user && $session->user->type=="student")
            {   
                 $this->setVar("ACTION", "account");
                 $this->parse('student_block', 'student', true);
            }
            else 
            {
                 if ($session->user && $session->user->type=="tutor")
                 {
                     $this->setVar("ACTION", "grades");
                     $this->parse('tutor_block', 'tutor', true);
                 }
                 else 
                 {
                     if ($session->user && $session->user->type=="teacher")
                     {
                         $this->setVar("ACTION", "grades");
                         $this->parse('teacher_block', 'teacher', true);
                     }
                 }
                        
            }
           
            //$this->parse('student_block', '');
        }
        else
        {
            $this->setVar("LOGIN_FORM", 'login_form');
        }
		$this->parse('MENU_TOP', 'tpl_menu_top');
		
		// sidebar menu
		$this->setFile('tpl_menu_sidebar', 'blocks/menu_sidebar.tpl');
		$this->setBlock('tpl_menu_sidebar', 'sidebar_menu_logged', 'sidebar_menu_logged_block');
		$this->setBlock('tpl_menu_sidebar', 'sidebar_menu_not_logged', 'sidebar_menu_not_logged_block');

		// add selected to the correct menu item
		$this->setVar($selectedItem, 'selected');
		
		if ($dotAuth->hasIdentity('user'))
		{
			$this->parse('sidebar_menu_logged_block', 'sidebar_menu_logged', true);
			$this->parse('sidebar_menu_not_logged_block', '');		
		}
		else
		{
			$this->parse('sidebar_menu_not_logged_block', 'sidebar_menu_not_logged', true);
			$this->parse('sidebar_menu_logged_block', '');		
		}

		$this->parse('MENU_SIDEBAR', 'tpl_menu_sidebar');

		// footer menu
		$this->setFile('tpl_menu_footer', 'blocks/menu_footer.tpl');

		// add selected to the correct menu item
		$this->setVar($selectedItem, 'selected');
		
		$this->parse('MENU_FOOTER', 'tpl_menu_footer');
	}
	
	
	/**
	 * Create the pagination, based on how many data
	 * @access public
	 * @param array $page
	 * @return string
	 */
	protected function paginator($page)
	{
	    // get route again here, because ajax may have change it
	    //$route = Zend_Registry::get('route');
	    $request = Zend_Registry::get('request');
	    $this->setFile('page_file', 'paginator.tpl');
	    $this->setVar('TOTAL_RECORDS', $page->totalItemCount);
	    $this->setVar('TOTAL_PAGES', $page->pageCount );
	    $this->setBlock('page_file', 'first', 'first_row');
	    $this->setBlock('page_file', 'last', 'last_row');
	    $this->setBlock('page_file', 'current_page', 'current_row');
	    $this->setBlock('page_file', 'other_page', 'other_row');
	    $this->setBlock('page_file', 'pages', 'pages_row');
	
	    if(array_key_exists('page', $request))
	    {
	        unset($request['page']);
	    }
	
	    $link = Dot_Route::createCanonicalUrl() .'page/';
	
	    if ($page->current != 1)
	    {
	        $this->setVar('FIRST_LINK',$link."1");
	        $this->parse('first_row', 'first', TRUE);
	    }
	    else
	    {
	        $this->parse('first_row', '');
	    }
	    if ($page->current != $page->last && $page->last > $page->current)
	    {
	        $this->setVar('LAST_LINK',$link.$page->last);
	        $this->parse('last_row', 'last', TRUE);
	    }
	    else
	    {
	        $this->parse('last_row', '');
	    }
	    foreach ($page->pagesInRange as $val)
	    {
	        $this->setVar('PAGE_NUMBER', $val);
	        $this->parse('other_row','');
	        $this->parse('current_row','');
	        if($val == $page->current)
	        {
	            $this->parse('current_row','current_page', TRUE);
	        }
	        else
	        {
	            $this->setVar('PAGE_LINK', $link.$val);
	            $this->parse('other_row','other_page', TRUE);
	        }
	        $this->parse('pages_row', 'pages', TRUE);
	    }
	    $this->parse('PAGINATION', 'page_file');
	}
	
	
	/**
	 * Display message - error, warning, info
	 * @access public
	 * @return void
	 */
	public function displayMessage()
	{
		$session = Zend_Registry::get('session');
		if(isset($session->message))
		{
			$this->setFile('tpl_msg', 'blocks/message.tpl');
			$this->setBlock('tpl_msg', 'msg_array', 'msg_array_row');
			$this->setVar('MESSAGE_TYPE', $session->message['type']);
			if(is_array($session->message['txt']))
			{
				foreach ($session->message['txt'] as $k => $msg)
				{
					$this->setVar('MESSAGE_ARRAY', is_string($k) ? $msg = ucfirst($k) . ' - ' . $msg : $msg);
					$this->parse('msg_array_row', 'msg_array', true);
				}
			}
			else
			{
				$this->parse('msg_array_row', '');
				$this->setVar('MESSAGE_STRING', $session->message['txt']);
			}
			$this->parse('MESSAGE_BLOCK', 'tpl_msg');
			unset($session->message);
		}		
	}
	/**
	 * Add the user's token to the template
	 * @access public
	 * @return array
	 */
	public function addUserToken()
	{
		$dotAuth = Dot_Auth::getInstance();
		$user = $dotAuth->getIdentity('user');
		$this->setVar('USERTOKEN', Dot_Auth::generateUserToken($user->password));
	}	
	/**
	 * Get captcha display box using Zend_Service_ReCaptcha api
	 * @access public
	 * @return Zend_Service_ReCaptcha
	 */
	public function getRecaptcha()
	{
		$option = Zend_Registry::get('option');
		// add secure image using ReCaptcha
		$recaptcha = new Zend_Service_ReCaptcha($option->captchaOptions->recaptchaPublicKey, $option->captchaOptions->recaptchaPrivateKey);
		$recaptcha->setOptions($option->captchaOptions->toArray());
		return $recaptcha;
	}
}