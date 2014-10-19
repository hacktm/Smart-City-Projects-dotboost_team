<?php
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Frontend
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id: UserController.php 810 2014-07-17 09:37:20Z costin $
 */

/**
 * User Controller
 * @author     DotKernel Team <team@dotkernel.com>
 */

$session = Zend_Registry::get('session');
// instantiate classes related to User module: model & view
$userModel = new User(); 
$userView = new User_View($tpl);
$studentModel = new EduSmart_Model_Student();
$teacherModel = new EduSmart_Model_Teacher();
// all actions MUST set  the variable  $pageTitle
$pageTitle = $option->pageTitle->action->{$registry->requestAction};
switch ($registry->requestAction)
{
	default:
	case 'login':
		if(!isset($session->user))
		{
			// display Login form
			$userView->loginForm('login');
		}
		else
		{
			header('Location: '.$registry->configuration->website->params->url.'/user/account');
			exit;
		}
	break;
	case 'authorize':
		// authorize user login
		if (array_key_exists('username', $_POST) && array_key_exists('password', $_POST)&&array_key_exists('userType', $_POST))
		{
			// validate the authorization request parameters 
			$values = array('username' => array('username' => $_POST['username']), 
											'password' => array('password' => $_POST['password'])
											);
			$who=$_POST['userType'];
			$dotValidateUser = new Dot_Validate_User(array('who' => $who, 'action' => 'login', 'values' => $values));
			
			if($dotValidateUser->isValid())
			{
				$userModel->authorizeLogin($dotValidateUser->getData(),$who);
				
			}
			else
			{
				$error = $dotValidateUser->getError();
				// login info are NOT VALID
				$txt = array();
				$field = array('username', 'password');
				foreach ($field as $v)
				{
					if(array_key_exists($v, $error))
					{
						 $txt[] = $error[$v];
					}
				}
				$session->validData = $dotValidateUser->getData();
				$session->message['txt'] = $txt;
				$session->message['type'] = 'error';
			}
			
		}
		else
		{
			$session->message['txt'] = $option->warningMessage->userPermission;
			$session->message['type'] = 'warning';
		}
		
		header('Location: '.$registry->configuration->website->params->url. '/' . $registry->requestController. '/login');
		exit;
	break;
	case 'account':
		// display My Account page, if user is logged in 
		//Dot_Auth::checkIdentity();
		$data = array();
		$error = array();
		if($_SERVER['REQUEST_METHOD'] === "POST")
		{
			Dot_Auth::checkUserToken('user');
			// POST values that will be validated
			
			// Only if a new password is provided we will update the password field
			if($_POST['password'] != '' || $_POST['password2'] !='' )
			{
				$values['password'] = array('password' => $_POST['password'],
								 										'password2' =>  $_POST['password2']);
			}
			
			$dotValidateUser = new Dot_Validate_User(array(
																											'who' => $registry->session->user->role,
																											'action' => 'update',
																											'values' => $values,
																											'userId' => $registry->session->user->id)
																										);
			if($dotValidateUser->isValid())
			{
				// no error - then update user
				$data = $dotValidateUser->getData();
				$data['id'] = $registry->session->user->id;
				$type = $registry->session->user->type;
				$userModel->updateUser($data, $type);
				$session->message['txt'] = $option->infoMessage->update;
				$session->message['type'] = 'info';
			}
			else
			{
				$data = $dotValidateUser->getData();
				$session->message['txt'] = $dotValidateUser->getError();
				$session->message['type'] = 'error';
			}
		}
		$data = $userModel->getUserInfo($registry->session->user->id,$registry->session->user->type);
		$userView->details('update',$data);
	break;
	case 'list-grades':
		//list child grades
	    $userView->details('list_grades');
	break;
	case 'forgot-password':
		// send an emai with the forgotten password
		$data = array();
		$error = array();
		if($_SERVER['REQUEST_METHOD'] === "POST")
		{
			$values = array('email' => array('email' => (isset($_POST['email']) ? $_POST['email'] : '' )));
			$dotValidateUser = new Dot_Validate_User(array('who' => 'user', 'action' => 'forgot-password', 'values' => $values));
			if($dotValidateUser->isValid())
			{
				// no error - then send password
				$data = $dotValidateUser->getData();
				$userModel->forgotPassword($data['email']);
			}
			else
			{
				$session->message['txt'] = $dotValidateUser->getError();
				$session->message['type'] = 'error';
			}
		}
		$userView->details('forgot_password',$data);
	break;
	case 'reset-password':
		// start by considering there are no errors, and we enable the form 
		$disabled = FALSE;
		
		// not sure if the form was submitted or not yet , either from Request or from POST
		$userId = array_key_exists('id', $registry->request) ? $registry->request['id'] : ((isset($_POST['userId'])) ? $_POST['userId'] : '');
		$userToken = array_key_exists('token', $registry->request) ? $registry->request['token'] : ((isset($_POST['userToken'])) ? $_POST['userToken'] : '');
		
		// get user info based on ID , and see if is valid
		$userInfo = $userModel->getUserInfo($userId);
		if(FALSE == $userInfo)
		{
			$disabled = TRUE;
		}
		else
		{
			// Check if the user's password  match the token 
			$expectedToken = Dot_Auth::generateUserToken($userInfo['password']);
			if($expectedToken != $userToken)
			{
				$disabled = TRUE;
			}
		}
		// we have errors, display the message and disable the form
		if(TRUE == $disabled)
		{
			$session->message['txt'] = $registry->option->errorMessage->wrongResetPasswordUrl;
			$session->message['type'] = 'error';
		}
		// IF the form was submmited and there are NO errors 
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && FALSE == $disabled)
		{
			// POST values that will be validated
			$values['password'] =	array('password' => (isset($_POST['password']) ? $_POST['password'] : ''),
																	'password2' =>  (isset($_POST['password2']) ? $_POST['password2'] : ''));
			
			$dotValidateUser = new Dot_Validate_User(array('who' => 'user',
																										'action' => 'update',
																										'values' => $values,
																										'userId' => $userId));
			if($dotValidateUser->isValid())
			{
				$data['password'] = $_POST['password'];
				$data['id'] = $userId;
				$data['username'] = $userInfo['username'];
				$userModel->updateUser($data);
				$userModel->authorizeLogin($data);
			}
			else
			{
				$data = $dotValidateUser->getData();
				$session->message['txt'] = $dotValidateUser->getError();
				$session->message['type'] = 'error';
			}
		}
		// show the form, enabled or disabled 
		$userView->resetPasswordForm('reset_password', $disabled, $userId, $userToken);
	break;
	case 'logout':
		$dotAuth = Dot_Auth::getInstance();
		$dotAuth->clearIdentity('user');
		header('location: '.$registry->configuration->website->params->url);
		exit;
	break;

	case 'grades':
	    if(!isset($session->user))
	    {
	        // display Login form
	        $userView->loginForm('login');
	    }
	    else
	    {
	       if($session->user->type == 'student')
	       {
	           $type = 'student';
	           //get student grades
	           $page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
	           $data=$studentModel->getGradesForIdentity($session->user,$page);
	          
	       }
	       if($session->user->type == 'tutor')
	       {
	           if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gradeseen']))
	            
	           {
	               $userModel->confirmeGrade($_POST['gradeseen']);
	               unset($_POST);
	           }
	           $type = 'tutor';
	           //get student grades
	           $page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
	           $session->user->student = $userModel->getTutorStudent($session->user->id);
	           $session->user->student2->id=$session->user->student['id'];
	           $session->user->student2->type='student';
	           $data=$studentModel->getGradesForIdentity($session->user->student2,$page);
	       }
	       if($session->user->type == 'teacher')
	       {
	           $type = 'teacher';
	           //get student grades
	           $page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
	           $data=array();
	          
	       }
	       $userView->grades('grades',$type,$page,$data);
	    }
	break;
	case 'absence':
	    if(!isset($session->user))
	    {
	        // display Login form
	        $userView->loginForm('login');
	    }
	    else
	    {
	        if($session->user->type == 'student')
	        {
	            $type = 'student';
	            //get student absence
	            $page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
	            $data=$studentModel->getAbsencesForIdentity($session->user,$page);
	           
	        }
	        if($session->user->type == 'tutor')
	        {
	            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gradeseen']))
	      
	            {
	               $userModel->confirmeAbsence($_POST['gradeseen']);
	               unset($_POST);
	            }
	            $type = 'tutor';
	            //get student grades
	            $data=array();
	            $page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
	            $session->user->student = $userModel->getTutorStudent($session->user->id);
	            $session->user->student2->id=$session->user->student['id'];
	            $session->user->student2->type='student';
	            $data=$studentModel->getAbsencesForIdentity($session->user->student2,$page);
	        }
	        if($session->user->type == 'teacher')
	        {
	            $type = 'teacher';
	            //get student absence
	            $data=array();
	            $page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
	        
	        }
	        $userView->absence('absence',$type,$page,$data);
	    }
	    break;
	    case 'grades-add':
	        if(!isset($session->user))
	        {
	            // display Login form
	            $userView->loginForm('login');
	        }
	        else 
	        {
	            if(!$session->user->type == 'teacher')
	            {
	                $userView->loginForm('login');
	            }
	            else 
	            {
	                if($_SERVER['REQUEST_METHOD'] === 'POST')
	                {
	                    //baga tare in db nota noua
	                }
	                else 
	                {
	                    //formular
	                    $userView->add('add','grades');
	                }
	            }
	        }
	       
	    break;
	    case 'absence-add':
	        if(!isset($session->user))
	        {
	            // display Login form
	            $userView->loginForm('login');
	        }
	        else
	        {
	           $id=$registry->request['id'];
	           $identity = (array)$dotAuth->getIdentity();
	           $userModel->addAbsence($id,$identity['id'],1);
	           header('Location: '.$registry->configuration->website->params->url.'/user/student-menu/id/'.$id);
	           exit;
	        }
        break;
        case 'send-message' :
            $dotAuth = Dot_Auth::getInstance();
            $identity = (array)$dotAuth->getIdentity();
            if($identity['type'] != 'teacher')
            {
                $session->message['txt'] = 'Only teachers are allowed to send messages';
                $session->message['type'] = 'error';
                header('Location: '.$registry->configuration->website->params->url.'/user/');
                exit;
            }
            $smsModel = new EduSmart_Sms();
            $message = isset($_POST['message']) ? $_POST['message'] : '';
            $number = isset($registry->request['number']) ? $registry->request['number'] : '';
            $userView->sendSms( 'send.tpl', $message, $number );
            if(isset($_POST['number']) && isset($_POST['message'])
                && !empty($_POST['number']) && strlen($_POST['message']) > 2 )
            {
                $message = $_POST['message'];
                $number  = $_POST['number'];
                $length = strlen($message);
                if($length > 3 && $length < 155)
                    $result = $smsModel->sendSms($message, $number, 'xeprzfkleiab');
                $registry->session->message['txt'] = $result;
                $registry->session->message['type'] = ($result=='"success"') ? 'info' : 'error';
                	
            }
        
        
            break;
        
        
        
        // JSON from here below
        case 'json-generate-api-key':
        	$output['result'] = 'error';
        	$output['message'] = 'Couldn\'t contact server. Please try again later';
			$dotAuth = Dot_Auth::getInstance();
			$identity = (array) $dotAuth->getIdentity();
			$token = EduSmart_ApiUtilities::generateApiKeyToken($identity);
							
			$eduModel = new EduSmart_Model();
			$eduModel->removeOlderTokensForIdentity($identity);
			$eduModel->addToken($token);
							
			$output = $token;
			$output['result'] = 'ok';		 		
			// store the token
			exit(json_encode($output));
			break;
		case 'json-get-api-key':
			$key['result'] = 'error';
			$dotAuth = Dot_Auth::getInstance();
			$identity = (array) $dotAuth->getIdentity();	
			$eduModel = new EduSmart_Model();
			$key = $eduModel->getApiKeyForIdentity($identity);
			if(empty($key))
			{
				$output['key'] = 'The key is expired -- Press Generate New Api Key';
			}
			else
			{
				
				$output['key'] = $key;
				$output['result'] = 'ok';
			}
				exit(json_encode($output));
			break;
			case 'my-class':
				$dotAuth = Dot_Auth::getInstance();
				$identity = (array)$dotAuth->getIdentity();
				if(!isset($session->user))
				{
					// display Login form
					$userView->loginForm('login');
				}
				else
				{
					if(!$session->user->type == 'teacher')
					{
						$userView->loginForm('login');
					}
					else
					{
							$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
							$studentList=$teacherModel->getStudentsByTeacherIdentity($identity,$page);
							$userView->showStudentList('class.tpl',$studentList);
						
					}
				}
				break;
			case 'view-classes':
				$dotAuth = Dot_Auth::getInstance();
				$identity = (array)$dotAuth->getIdentity();
				if(!isset($session->user))
				{
					// display Login form
					$userView->loginForm('login');
				}
				else
				{
					if(!$session->user->type == 'teacher')
					{
						$userView->loginForm('login');
					}
					else
					{
						if (!isset($registry->request['id'])){
							$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
							$studentList=$teacherModel->getClassList($page);
							$userView->showClassList('classlist.tpl',$studentList);
						}
						else
						{
							$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
							$studentList=$teacherModel->getStudentListByClassId($registry->request['id'],$page);
							$userView->showStudentList('class.tpl',$studentList);
						}
				
					}
				}
				break;
				break;
				case 'view-class':
					$dotAuth = Dot_Auth::getInstance();
					$identity = (array)$dotAuth->getIdentity();
					if(!isset($session->user))
					{
						// display Login form
						$userView->loginForm('login');
					}
					else
					{
						if(!$session->user->type == 'teacher')
						{
							$userView->loginForm('login');
						}
						else
						{
							if (!isset($registry->request['id'])){
							$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
							$studentList=$teacherModel->getClassList($page);
							$userView->showClassList('classlist.tpl',$studentList);
						}
							else 
							{
							$page = (isset($registry->request['page']) && $registry->request['page'] > 0) ? $registry->request['page'] : 1;
							$studentList=$teacherModel->getStudentListByClassId($registry->request['id'],$page);
							$userView->showStudentList('class.tpl',$studentList);
							}
				
						}
					}
					break;
		case 'student-menu':
			$studentId=$registry->request['id'];
			$studentData=$userModel->getUserInfo($studentId,'student');
			$userView->details('student_menu',$studentData);
			break;
}	