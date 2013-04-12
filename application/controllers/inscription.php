<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inscription extends CI_Controller {
    
        public function __construct()
        {
                parent::__construct();
                $this->load->library('twig');
		$this->load->library('form_validation');
                $this->load->model('inscription_model');
                $this->twig->addFunction('validation_errors');  
                
                $this->load->helper('sessionnzo');
                $this->twig->addFunction('getsessionhelper');    
                
                
        }

	public function index()
	{                   
            $this->twig->render('inscription');
	}
        
        public function saveform()
        {

            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
            $this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');

            if ($this->form_validation->run() == FALSE)
            {
                    $this->twig->render('inscription');
            }
            else
            {        
                $this->inscription_model->insert_inscription();
                $link = base_url('inscription/confirmation').'/'. sha1($this->input->post('email') . 'XkI85BtF');              
                //send email
                $config = Array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'ssl://smtp.googlemail.com',
                    'smtp_port' => 465,
                    'smtp_user' => 'bourcedefret@gmail.com',
                    'smtp_pass' => 'markmark88',
                    'mailtype'  => 'html'
                    );
                    
                    $this->load->library('email', $config);
                    $this->email->set_newline("\r\n");

                    $this->email->from('bourcedefret@gmail.com', 'SmartShare Team');
                    $this->email->to($this->input->post('email'));

                    $this->email->subject(' Activer Votre compte ');
                    $this->email->message('Pour Activer Votre compte, cliquez sur ce '. anchor($link, 'lien'));
                        
                    if (!$this->email->send())
                      show_error($this->email->print_debugger());
                    else 
                      redirect('frontend/formsuccess');     
            }
        }
        
        public function confirmation($link)
        {
            $resultats = $this->inscription_model->get_all_desabled();
            
            foreach($resultats as $res)
            {
                if(sha1($res->email . 'XkI85BtF') == $link){
                        $this->inscription_model->update_activer($res->id);      
                        redirect('frontend');     
                        break;
                }
            }
                      
        }
        
        public function login() {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[4]|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]|xss_clean|callback_check_login');
            if ($this->form_validation->run() == FALSE)
            {
                    $this->twig->render('login');
            }
            else{
                $this->twig->render('home');
            }
        }
        
        public function check_login(){           
       
                $req = $this->inscription_model->login();
                if(!$req){
                    $this->form_validation->set_message('check_login', 'Invalid Email or password');
                    return false;
                }
                else{
                    foreach($req as $val){
                        if (!$val->activer) {
                            $this->form_validation->set_message('check_login', 'Utilisateur non Activer! ');
                            return false;
                        }
                    $login_in = array('id' => $val->id, 'email' => $val->email, 'nom' => $val->nom);
                    $this->session->set_userdata('login_in', $login_in);
                    }
                    return TRUE;
                }
             }
       
        
        function logout(){
            $this->session->unset_userdata('login_in');
            $this->session->sess_destroy();
            redirect('frontend');
        }

        function loginfb(){
            $this->twig->render('inscription_fb');
        }
        
        function registerfb(){
           function parse_signed_request($signed_request, $secret) {
            list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

            // decode the data
            $sig = base64_url_decode($encoded_sig);
            $data = json_decode(base64_url_decode($payload), true);

            if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
                error_log('Unknown algorithm. Expected HMAC-SHA256');
                return null;
            }

            // check sig
            $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
            if ($sig !== $expected_sig) {
                error_log('Bad Signed JSON signature!');
                return null;
            }

            return $data;
            }

            function base64_url_decode($input) {
                return base64_decode(strtr($input, '-_', '+/'));
            }

            if ($_REQUEST) {
            $response = parse_signed_request($_REQUEST['signed_request'], 
                                            'f46261dd081b144047d83d89b77fe1fc');
            
            $this->inscription_model->insert_inscription($response);
            redirect('frontend/formsuccess');  
            }
        }
}