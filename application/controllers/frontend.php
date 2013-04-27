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


