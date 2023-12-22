<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {

    public function autenticar() {
        
        $login = Container::getModel('Usuario');

		$emailfiltro = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
		$email = filter_var($emailfiltro, FILTER_VALIDATE_EMAIL);
		$senha = htmlspecialchars($_POST['senha'], ENT_QUOTES, 'UTF-8');

        $login->__set('email', $email);
        $login->__set('senha', $senha);
        $login->login();

        if(!empty($login->__get('id')) && !empty($login->__get('nome')) && $login->__get('autenticado')) {
            
            if($login->__get('tipo_usuario') == 1) {

                Action::ativarSessao();

                $_SESSION['id'] = $login->__get('id');
                $_SESSION['nome'] = $login->__get('nome');
                $_SESSION['endereco'] = $login->__get('endereco');
                $_SESSION['bairro'] = $login->__get('bairro');
                $_SESSION['end_referencia'] = $login->__get('end_referencia');
                $_SESSION['telefone'] = $login->__get('telefone');
                $_SESSION['id_cidade'] = $login->__get('id_cidade');
                $_SESSION['tipo_usuario'] = $login->__get('tipo_usuario');
                header('Location: /');

            } else if($login->__get('tipo_usuario') == 2){

                if($login->__get('possui_entrega')) {

                    Action::ativarSessao();

                    $_SESSION['id'] = $login->__get('id');
                    $_SESSION['nome'] = $login->__get('nome');
                    $_SESSION['endereco'] = $login->__get('endereco');
                    $_SESSION['bairro'] = $login->__get('bairro');
                    $_SESSION['telefone'] = $login->__get('telefone');
                    $_SESSION['id_cidade'] = $login->__get('id_cidade');
                    $_SESSION['tipo_usuario'] = $login->__get('tipo_usuario');
                    $_SESSION['possui_entrega'] = $login->__get('possui_entrega');
                    $_SESSION['valor_entrega'] = $login->__get('valor_entrega');
                    $_SESSION['foto'] = $login->__get('foto_caminho').$login->__get('foto_perfil');
                    header('Location: /');

                } else {

                    Action::ativarSessao();
                    
                    $_SESSION['id'] = $login->__get('id');
                    $_SESSION['nome'] = $login->__get('nome');
                    $_SESSION['endereco'] = $login->__get('endereco');
                    $_SESSION['bairro'] = $login->__get('bairro');
                    $_SESSION['telefone'] = $login->__get('telefone');
                    $_SESSION['id_cidade'] = $login->__get('id_cidade');
                    $_SESSION['tipo_usuario'] = $login->__get('tipo_usuario');
                    $_SESSION['possui_entrega'] = $login->__get('possui_entrega');
                    $_SESSION['foto'] = $login->__get('foto_caminho').$login->__get('foto_perfil');
                    header('Location: /');

                }

            }

        } else {
            
            header('Location: /login?erro=login');
            
        }
        
    }

    public function sair() {
        session_start();

        session_destroy();

        header('Location: /');
    }

}





?>