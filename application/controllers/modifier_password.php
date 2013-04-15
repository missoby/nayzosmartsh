<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modifier_password extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('inscription_model', 'inscriManager');
        $this->twig->addFunction('getsessionhelper');
    }
        
    public function index()
    {
        //$this->form_validation->set_error_delimiters('<p class="form_erreur">', '</p>');
        
        $this->form_validation->set_rules('old', '\'Ancien mot de passe\'', 'trim|required|min_length[4]|max_length[255]|alpha_dash|encode_php_tags|xss_clean|callback_checkOldPass');
        $this->form_validation->set_rules('new', '\'Nouveau mot de passe\'', 'trim|required|min_length[4]|max_length[255]|alpha_dash|encode_php_tags|xss_clean');
        $this->form_validation->set_rules('renew', '\'Nouveau mot de passe\'', 'trim|required|min_length[4]|max_length[255]|alpha_dash|encode_php_tags|xss_clean|callback_checkPass');
        
        if($this->form_validation->run())
        {
            if(!$this->inscriManager->setPassword(getsessionhelper()['id']))
                echo 'Probleme ID. Fonction: models/inscription_model/setPassword';
            else
                redirect('/profil');
        }
        else
        {
            $this->twig->render('modifier_password_view');
        }
    }
    
    public function checkOldPass()
    {
        if($this->inscriManager->getPassword(getsessionhelper()['id']) === sha1($this->input->post('old')))
            return true;
        else
        {
            $this->form_validation->set_message('checkOldPass', 'Verifier votre ancien mot de passe');
            return false;
        }
    }

    public function checkPass()
    {
        if($this->input->post('new') === $this->input->post('renew'))
            return true;
        else
        {
            $this->form_validation->set_message('checkPass', 'Verifier votre mot de passe');
            return false;
        }
    }
    
}

?>


