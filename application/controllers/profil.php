<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profil extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->twig->addFunction('getsessionhelper');    
    }
        
    public function index()
    {
        $this->twig->render('profil_view');
    }
    
}

?>


