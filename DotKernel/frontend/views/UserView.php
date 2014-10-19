<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Frontend 
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id: UserView.php 797 2014-06-05 22:06:45Z julian $
 */

/**
 * User View Class
 * class that prepare output related to User controller 
 * @category   DotKernel
 * @package    Frontend
 * @author     DotKernel Team <team@dotkernel.com>
 */

class User_View extends View
{

	/**
	 * Constructor
	 * @access public
	 * @param Dot_Template $tpl
	 */
	public function __construct($tpl)
	{
		$this->tpl = $tpl;
		$this->settings = Zend_Registry::get('settings');
	}

	/**
	 * Display the login form
	 * @access public
	 * @param string $templateFile
	 * @return void
	 */
	public function loginForm($templateFile)
	{
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');
		$session = Zend_Registry::get('session');
		if(isset($session->validData))
		{
			foreach ($session->validData as $k=>$v)
			{
				$this->tpl->setVar(strtoupper($k),$v);
			}
		}
		unset($session->validData);
	}

	/**
	 * Display the password reset form 
	 * @access public
	 * @param string $templateFile
	 * @param bool $disabled
	 * @param integer $userId
	 * @param string $userToken
	 * @return void
	 */
	public function resetPasswordForm($templateFile, $disabled = TRUE, $userId, $userToken)
	{
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');
		if(FALSE == $disabled)
		{
			$this->tpl->setVar('USERTOKEN', $userToken);
			$this->tpl->setVar('USERID', $userId);
			$this->tpl->setVar('DISABLED', 'submit');
		}
		else 
		{
			$this->tpl->setVar('DISABLED', 'hidden');
		}
	}

	/**
	 * Display user's signup form
	 * @access public
	 * @param string $templateFile
	 * @param array $data [optional]
	 * @return void
	 */
	public function details($templateFile, $data=array())
	{
		$this->tpl->setFile('tpl_main', 'user/' . $templateFile . '.tpl');
		if(!empty($data))
		foreach ($data as $k=>$v)
		{
			$this->tpl->setVar(strtoupper($k), $v);
		}
		if('add' == $templateFile)
		{
			$this->tpl->setVar('SECUREIMAGE',$this->getRecaptcha()->getHTML());
		}
		if('update' == $templateFile)
		{
			$this->tpl->addUserToken();
		}
		
		//empty because we don't want to show the password
		$this->tpl->setVar('PASSWORD', '');
	}
	
	public function grades($templateFile,$type,$page,$data)
	{
	    $this->tpl->setFile('tpl_main', 'user/'. $templateFile . '.tpl');
	    $this->tpl->paginator($data['pages']);
	    $this->tpl->setVar('PAGE', $page);
	    $this->tpl->setBlock('tpl_main', 'tutor_head', 'tutor_head_block');
	    $this->tpl->setBlock('tpl_main', 'tutor_seen', 'tutor_seen_block');
	    $this->tpl->setBlock('tpl_main', 'tutor_not_seen', 'tutor_not_seen_block');
	    $this->tpl->setBlock('tpl_main', 'grades', 'grades_block');
	    if($type == 'student')
	    {
	       
	        $this->tpl->parse('tutor_head_block', '');
	        $this->tpl->parse('tutor_seen_block', '');
	        $this->tpl->parse('tutor_not_seen_block', '');
	        foreach($data['data'] as $k=>$v)
	        {
	            $this->tpl->setVar('GRADE', $v['value']);
	            $this->tpl->setVar('SUBJECT', $v['subject']);
	            $this->tpl->setVar('DATE', $v['date']);
	            $this->tpl->parse('grades_block', 'grades', true);
	        }
	    }
	    if($type == 'tutor')
	    {
	        $this->tpl->parse('tutor_head_block', 'tutor_head', true);
	        
	        foreach($data['data'] as $k=>$v)
	        {
	            $this->tpl->setVar('GRADE', $v['value']);
	            $this->tpl->setVar('SUBJECT', $v['subject']);
	            $this->tpl->setVar('DATE', $v['date']);
	            if($v['seen'] == 1)
	            {
	                $this->tpl->parse('tutor_seen_block', 'tutor_seen', true);
	                $this->tpl->parse('tutor_not_seen_block', '');
	            }
	            else
	            {
	                $this->tpl->setVar('GRADE_ID', $v['id']);
	                $this->tpl->parse('tutor_seen_block', '');
	                $this->tpl->parse('tutor_not_seen_block', 'tutor_not_seen', true);
	            }
	            $this->tpl->parse('grades_block', 'grades', true);
	        }
	    }
	   
	}
	public function absence($templateFile,$type,$page,$data)
	{
	    $this->tpl->setFile('tpl_main', 'user/'. $templateFile . '.tpl');
	    $this->tpl->paginator($data['pages']);
	    $this->tpl->setVar('PAGE', $page);
	    $this->tpl->setBlock('tpl_main', 'tutor_head', 'tutor_head_block');
	    $this->tpl->setBlock('tpl_main', 'tutor_seen', 'tutor_seen_block');
	    $this->tpl->setBlock('tpl_main', 'tutor_not_seen', 'tutor_not_seen_block');
	    $this->tpl->setBlock('tpl_main', 'absence', 'absence_block');
	    if($type == 'student')
	    {
	        $this->tpl->parse('tutor_head_block', '');
	        $this->tpl->parse('tutor_seen_block', '');
	        $this->tpl->parse('tutor_not_seen_block', '');
	        foreach($data['data'] as $k=>$v)
	        {
	            $this->tpl->setVar('SUBJECT', $v['subject']);
	            $this->tpl->setVar('DATE', $v['date']);
	            $this->tpl->parse('absence_block', 'absence', true);
	        }
	    }
	    if($type == 'tutor')
	    {
	        $this->tpl->parse('tutor_head_block', 'tutor_head', true);
	        foreach($data['data'] as $k=>$v)
	        {
	            $this->tpl->setVar('SUBJECT', $v['subject']);
	            $this->tpl->setVar('DATE', $v['date']);
	            if($v['seen'] == 1)
	            {
	                $this->tpl->parse('tutor_seen_block', 'tutor_seen', true);
	                $this->tpl->parse('tutor_not_seen_block', '');
	            }
	            else
	            {
	                $this->tpl->setVar('GRADE_ID', $v['id']);
	                $this->tpl->parse('tutor_seen_block', '');
	                $this->tpl->parse('tutor_not_seen_block', 'tutor_not_seen', true);
	            }
	             $this->tpl->parse('absence_block', 'absence', true);
	        }
	    }
	}
	
	public function add($templateFile,$type)
	{
	    $this->tpl->setFile('tpl_main', 'user/'. $templateFile . '.tpl');
	    $this->tpl->setBlock('tpl_main', 'grades', 'grades_block');
	    $this->tpl->setBlock('tpl_main', 'absence', 'absence_block');
	    if($type == 'grades')
	    {
	        $this->tpl->parse('grades_block', 'grades', true);
	        $this->tpl->parse('absence_block', '');
	    }
	    if($type == 'absence')
	    {
	        $this->tpl->parse('grades_block', '');
	        $this->tpl->parse('absence_block', 'absence',true );
	    }
	}
	

	public function showStudentList($templateFile, $studentList, $page = 1)
	{
		$this->tpl->setFile('tpl_main','user/'.$templateFile);
		$this->tpl->setBlock('tpl_main', 'student_list', 'student_list_block');
		foreach($studentList['data'] as $student)
		{
			foreach($student as $key => $value)
			{
				$this->tpl->setVar(strtoupper($key), $value);
			}
			$this->tpl->parse('student_list_block', 'student_list',true );
		}
	}
	
	public function showClassList($filename, $list = array(), $page = 1, $data = array()) {
		$this->tpl->setFile ( 'tpl_main', 'user/' . $filename );
		$this->tpl->setBlock ( 'tpl_main', 'class_List', '_class_List' );
		$this->tpl->paginator ( $list ['pages'] );
		foreach ( $list ['data'] as $class ) {
			foreach ( $class as $key => $value ) {
				$this->tpl->setVar ( strtoupper ( $key ), $value );
			}
			$this->tpl->parse ( '_class_List', 'class_List', true );
		}
	}

	public function sendSms($fileName, $message, $number, $data = array())
	{
	    $this->tpl->setFile('tpl_main', 'user/'.$fileName );
	    $this->tpl->setVar('MESSAGE', $message);
	    $this->tpl->setVar('NUMBER', $number);
	}
	public function sentSms($fileName, $feedback)
	{
	    $this->tpl->setFile('tpl_main', 'user/'.$fileName );
	    $this->tpl->setVar('FEEDBACK', $feedback);
	}
}
