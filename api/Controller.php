<?php 
/**
 * DotBoost Technologies Inc.
 * DotKernel Application Framework
 *
 * @category   DotKernel
 * @package    Api
 * @copyright  Copyright (c) 2009-2014 DotBoost Technologies Inc. (http://www.dotboost.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version    $Id: Controller.php 808 2014-07-15 14:12:56Z costin $
 * @author     DotKernel Team <team@dotkernel.com>
 */
/// BOOTSTRAP
// if the api is disabled stop execution
if (!$registry->configuration->api->params->enable)
{
    header("HTTP/1.0 403 Forbidden");
    exit;
}
$data = array();
$key = isset($registry->arguments['key']) ? $registry->arguments['key'] : '';
$userType = isset($registry->arguments['user_type']) ? $registry->arguments['user_type'] : '' ;
$allowedUserTypes = array('student', 'parent', 'teacher');
if(! in_array($userType, $allowedUserTypes))
{
	$data['result'] = 'error';
	$data['response'] = 'User type missing or not allowed ';
	$jsonString = Zend_Json::encode($data);
	echo $jsonString;
	exit();
}


if(empty($key))
{
	$data['result'] = 'error';
	$data['response'] = 'API Key is missing';
	$jsonString = Zend_Json::encode($data);
	echo $jsonString;
	exit();
}

$eduModel = new EduSmart_Model();

if(!$eduModel->isKeyValid($key, $userType))
{
	$data['result'] = 'error';
	$data['response'] = 'API Key is wrong or expired';
	$jsonString = Zend_Json::encode($data);
	echo $jsonString;
	exit();
} 

#exit('ok');
if (isset($registry->action))
{
    # LOGIN TYPE
    # simple search
    
	switch ($registry->action)
	{
		case 'version':
			$data = array();
			$data[] = array('result' => 'ok');
			$data[] = array('response' => Dot_Kernel::VERSION);
			$jsonString = Zend_Json::encode($data);
			echo $jsonString;
		break;
		
		case 'opcache':
			if ($registry->configuration->api->params->key == $registry->arguments['key'])
			{
				$opCacheModel = new Api_Model_OpCache();
				echo $opCacheModel->opCacheStatus();
			}
			else
			{
				header("HTTP/1.0 401 Unauthorized");
				$data = array();
				$data[] = array('result' => 'error');
				$data[] = array('response' => "Invalid Key");
				$jsonString = Zend_Json::encode($data);
				echo $jsonString;
			}
		break;
	
		default:
			$data = array();
			$data[] = array('result' => 'error');
			$data[] = array('response' => "Action doesn't exist");
			$jsonString = Zend_Json::encode($data);
			echo $jsonString;
		break;
	}
}
else
{
	$data = array();
	$data[] = array('result' => 'error');
	$data[] = array('response' => "Action doesn't exist");
	$jsonString = Zend_Json::encode($data);
	echo $jsonString;
}