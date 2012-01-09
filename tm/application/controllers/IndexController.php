<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once 'Zend/Session/Namespace.php';

class IndexController extends Zend_Controller_Action
{
  	/*public function indexAction()
  	{
    	$registry = Zend_Registry::getInstance();

		$title 		= $registry['title'];
		$credits 	= $registry['credits'];
		$strCredit = implode(", ",$credits);
		$this->view->assign('name', 'Wiwit');
		$this->view->assign('title', $title);
		$this->view->assign('credits', $strCredit);
  	}*/

	 public function indexAction()
	 { 
	   	$request = $this->getRequest();  
	   	$auth		= Zend_Auth::getInstance(); 
		if(!$auth->hasIdentity())
		{
	  		$this->_redirect('/user/loginform');
		}
		else
		{
	     	$this->_redirect('/user/userpage');
		}
	 }

 	public function authAction()
	{
	   	$request 	= $this->getRequest();
	   	$registry 	= Zend_Registry::getInstance();
		$auth		= Zend_Auth::getInstance(); 

		$DB = $registry['DB'];

	   $authAdapter = new Zend_Auth_Adapter_DbTable($DB);
	   $authAdapter->setTableName('users')
	               ->setIdentityColumn('username')
	               ->setCredentialColumn('password');    

		// Set the input credential values
		$uname = $request->getParam('username');
		$paswd = $request->getParam('password');
	   	$authAdapter->setIdentity($uname);
	   	$authAdapter->setCredential(md5($paswd));

	   	// Perform the authentication query, saving the result
	   	$result = $auth->authenticate($authAdapter);

	   	if($result->isValid())
		{
	  		$data = $authAdapter->getResultRowObject(null,'password');
	  		$auth->getStorage()->write($data);
	  		$this->_redirect('/user/userpage');
		}
		else
		{
	  		$this->_redirect('/user/loginform');
		}
	}
	
	public function userPageAction()
	{
	    $auth		= Zend_Auth::getInstance(); 

		if(!$auth->hasIdentity()){
		  $this->_redirect('/user/loginform');
		}

	    $request = $this->getRequest(); 
		$user		= $auth->getIdentity();
		$real_name	= $user->real_name;
		$username	= $user->username;
		$logoutUrl  = $request->getBaseURL().'/user/logout';

		$this->view->assign('username', $real_name);
		$this->view->assign('urllogout',$logoutUrl);
	}
	

 	public function registerAction()
 	{
   		$request = $this->getRequest();

		$this->view->assign('action',"process");
   		$this->view->assign('title','Member Registration');
		$this->view->assign('label_fname','First Name');
		$this->view->assign('label_lname','Last Name');	
		$this->view->assign('label_uname','User Name');	
		$this->view->assign('label_pass','Password');
		$this->view->assign('label_submit','Register');		
		$this->view->assign('description','Please enter this form completely:');		
 	}

	 public function processAction()
	 {
	   	$registry = Zend_Registry::getInstance();  
		$DB = $registry['DB'];

	   	$request = $this->getRequest();
		$data = array('first_name' => $request->getParam('first_name'),
	              'last_name' => $request->getParam('last_name'),
				  'user_name' => $request->getParam('user_name'),
				  'password' => md5($request->getParam('password'))
	              );
	   	$DB->insert('user', $data);

	   	$this->view->assign('title','Registration Process');
		$this->view->assign('description','Registration succes');  	
	 }
	
	 public function listAction()
	 {
	   	$registry = Zend_Registry::getInstance();  
		$DB = $registry['DB'];

		$sql = "SELECT * FROM `user` ORDER BY user_name ASC";
		$result = $DB->fetchAssoc($sql);

	   	$this->view->assign('title','Member List');
		$this->view->assign('description','Below, our members:');
		$this->view->assign('datas',$result);		

	 }
	
  	 public function editAction()
	 {
	   	$registry = Zend_Registry::getInstance();  
		$DB = $registry['DB'];

	   	$request = $this->getRequest();
		$id 	 = $request->getParam("id");

		$sql = "SELECT * FROM `user` WHERE id='".$id."'";
		$result = $DB->fetchRow($sql);

		$this->view->assign('data',$result);
		$this->view->assign('action', $request->getBaseURL()."/user/processedit");
	   	$this->view->assign('title','Member Editing');
		$this->view->assign('label_fname','First Name');
		$this->view->assign('label_lname','Last Name');	
		$this->view->assign('label_uname','User Name');	
		$this->view->assign('label_pass','Password');
		$this->view->assign('label_submit','Edit');		
		$this->view->assign('description','Please update this form completely:');		
	 }
	
	 public function processeditAction()
	 {
	   	$registry = Zend_Registry::getInstance();  
		$DB = $registry['DB'];
		
		$request = $this->getRequest();

		$data = array('first_name' => $request->getParam('first_name'),
	              'last_name' => $request->getParam('last_name'),
				  'user_name' => $request->getParam('user_name'),
				  'password' => md5($request->getParam('password'))
	              );
	   	$DB->update('user', $data,'id = '.$request->getParam('id'));	

	   	$this->view->assign('title','Editing Process');
		$this->view->assign('description','Editing succes');  	

	 }
	
	 public function delAction()
	 {
	   	$registry = Zend_Registry::getInstance();  
		$DB = $registry['DB'];
		
		$request = $this->getRequest();

	   	$DB->delete('user', 'id = '.$request->getParam('id'));	

	   	$this->view->assign('title','Delete Data');
		$this->view->assign('description','Deleting succes');  		  
	   	$this->view->assign('list',$request->getBaseURL()."/user/list");  

	 }
	
	 public function loginFormAction()
	 {
	   	$request = $this->getRequest();  

	   	$ns = new Zend_Session_Namespace('HelloWorld');

		if(!isset($ns->yourLoginRequest))
		{
	  		$ns->yourLoginRequest = 1;
		}
		else
		{
	  		$ns->yourLoginRequest++;
		}

		$this->view->assign('request', $ns->yourLoginRequest);
		$this->view->assign('action', $request->getBaseURL()."/user/auth");  
	   	$this->view->assign('title', 'Login Form');
	   	$this->view->assign('username', 'User Name');	
	   	$this->view->assign('password', 'Password');	
	 }
	
	 public function logoutAction()
	 {
	   	$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		$this->_redirect('/user');
	 }
	
}
?>
