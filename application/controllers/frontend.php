<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Frontend extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');
    }
    
    public function index()
    {
        require_once 'assets/facebook_sdk_src/facebook.php';
        
        $param = array();
        $param['appId'] = '444753728948897';
        $param['secret'] = '5ab7c77a75fd646619cc98bde08e37e3';
        $param['fileUpload'] = true; // pour envoyer des photos
        //$param['cookie'] = true;
        $fb = new Facebook($param);
        
        $uid = $fb->getUser();
        if(empty($uid))
        {
            $ppp = array();
            $ppp['scope'] = 'email';//, read_stream, friends_likes';//read_stream = fil d'actualité; friends_likes: mentions j'aime de vos amis
            //$ppp['display'] = 'popup';
            //$ppp['locale'] = 'fr_FR';
            redirect($fb->getLoginUrl($ppp));
        }
        else
        {
            print_r($uid);
            echo '<br /><br /><br />';
            print_r($_SESSION);
            echo '<br /><br /><br />';
            $me = $fb->api('/me');
            //print_r($me);
            echo $me['email'];
        }
        
        $this->load->view('home');
    }
        
    public function indexOrigin()
    {
        $this->twig->render('home');
    }
        
    public function formsuccess()
    {
        $this->twig->render('formsuccess');          
    }
        
    public function uploadimg()
    { 
        $this->twig->render('uploadimg');          
    }
        
    public function do_upload()
    {
        $config['upload_path'] = './uploads/';
	$config['allowed_types'] = 'gif|jpg|png';
	$config['max_size']	= '100';
	$config['max_width']  = '1024';
	$config['max_height']  = '768';
        $config['encrypt_name']  = TRUE;

	$this->load->library('upload', $config);

	if ( ! $this->upload->do_upload())
	{
		$error = array('error' => $this->upload->display_errors());
		$this->twig->render('uploadimg', $error);
	}
	else
	{
		$data = array('upload_data' => $this->upload->data());
		$this->twig->render('successupload', $data);
	}
    }
}


