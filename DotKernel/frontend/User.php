<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Frontend
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    $Id: User.php 797 2014-06-05 22:06:45Z julian $
 */

/**
 * User Model
 * Here are all the actions related to the user
 * @category   DotKernel
 * @package    Frontend
 * @author     DotKernel Team <team@dotkernel.com>
 */

class User extends Dot_Model_User
{
	
	/**
	 * Constructor
	 * @access public 
	 * @return User
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get user info
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getUserInfo($id, $type)
	{
		$select = $this->db->select()
					   ->from($type)
					   ->where('id = ?', $id);
		return $this->db->fetchRow($select);
	}

	/**
	 * Register logins data
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function registerLogin($data)
	{
	    $who = $data['who'];
	    unset($data['who']);
		$this->db->insert($who.'Login', $data);
	}

	/**
	 * Send a link to reset the  password to user's email
	 * @access public
	 * @param string $email
	 * @return void
	 */
	public function forgotPassword($email)
	{
		$session = Zend_Registry::get('session');
		$seo = Zend_Registry::get('seo');
		$value = $this->getUserBy('email', $email);

		if(!empty($value))
		{
			$dotEmail = new Dot_Email();
			$dotEmail->addTo($email);
			$subject = str_replace('%SITENAME%', $seo->siteName, $this->option->forgotPassword->subject);
			$dotEmail->setSubject($subject);
			
			$userToken = Dot_Auth::generateUserToken($value['password']);
			
			$msg = str_replace(array('%FIRSTNAME%', '%SITE_URL%', '%USERID%', '%TOKEN%'), 
													array($value['firstName'], $this->config->website->params->url, $value['id'], $userToken), 
													$this->option->forgotPassword->message);
			$dotEmail->setBodyText($msg);
			$succeed = $dotEmail->send();
			if($succeed)
			{
				$session->message['txt'] = $this->option->errorMessage->emailSent.$email;
				$session->message['type'] = 'info';
			}
			else
			{
				$session->message['txt'] = $this->option->errorMessage->emailNotSent.$email;
				$session->message['type'] = 'error';
			}
		}
		else
		{
			$session->message['txt'] = $email.$this->option->errorMessage->emailNotFound;
			$session->message['type'] = 'error';
		}
	}

	/**
	 * Authorize user login
	 * @access public
	 * @param array $validData
	 * @return void
	 */
	public function authorizeLogin($validData,$who)
	{
		$session = Zend_Registry::get('session');
		unset($session->user);
		// login info are VALID, we can see if is a valid user now 
		$dotAuth = Dot_Auth::getInstance();
		$validAuth = $dotAuth->process($who, $validData);
		if($validAuth)
		{
			//prepare data for register the login
			$dotGeoip = new Dot_Geoip();
			$userIp = Dot_Kernel::getUserIp();
			$userCountry = $dotGeoip->getCountryByIp($userIp);
			$dataLogin = array( 'ip' => $userIp, 
								'userId' => $session->user->id, 
								'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
								'userAgent' => $_SERVER["HTTP_USER_AGENT"],
								'country' => $userCountry[1],
			                     'who' => $who
								);
			$this->registerLogin($dataLogin);
			$link = isset($session->wantUrl) ? $session->wantUrl : $this->config->website->params->url.'/user/account';
			header('location: '.$link);
			exit;
		}
		else
		{
			$session->message['txt'] = $this->option->errorMessage->login;
			$session->message['type'] = 'error';
		}
	}
	
	public function getTutorStudent($id)
	{
	    $select = $this->db->select()
	                       ->from('studentParent')
	                       ->join('student','student.id=studentparent.parentId')
	                       ->where('parentId = ?', $id);
	    return $this->db->fetchRow($select);
	}
	
	public function confirmeAbsence($confirm)
	{
	    foreach($confirm as $k=>$v)
	    {
	        $data=array('seen'=>1);
	        $this->db->update('absence',$data,'id = '.$v);
	    }
	}
	
	public function confirmeGrade($confirm)
	{
	    foreach($confirm as $k=>$v)
	    {
	        $data=array('seen'=>1);
	        $this->db->update('grade',$data,'id = '.$v);
	    }
	}
	public function addAbsence($studentId,$teacherId,$subjectId)
	{
		$this->db->insert('absence', array('studentId'=>$studentId,'teacherId'=>$teacherId,'subjectId'=>$subjectId));
	}
}