<?php

namespace MF\Controller;

use MF\Model\Container;

abstract class Action {

	protected $view;

	public function __construct() {
		$this->view = new \stdClass();
	}

	public function validaUsuario() {

		$this->validaAutenticacao();

		if(!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 1) {

			header('Location: /');
			die();

		}

		$this->notificarVendas();

	}

	public function notificarVendas() {		

		if(isset($_SESSION['venda_pendente']) && !empty($_SESSION['venda_pendente'])) {

			$verificar_status = Container::getModel('Venda');

			foreach($_SESSION['venda_pendente'] as $key => $venda) {

				if($venda['exibido'] == false) {

					$this->view->notificacao = 'pendente';
					$_SESSION['venda_pendente'][$key]['exibido'] = true;
					break;

				} 
				if($venda['exibido'] == true) {

					$id_venda = $venda['id_venda'];

					$venda_aprovada = $verificar_status->verificarStatusVenda($id_venda);

					if($venda_aprovada['aprovado'] == 1) {

						$this->view->notificacao = 'aprovado';
						unset($_SESSION['venda_pendente'][$key]);

					}
					if($venda_aprovada['aprovado'] == 0) {

						$this->view->notificacao = 'recusado';
						unset($_SESSION['venda_pendente'][$key]);

					}

				}

			}

		}

	}

	public function validaVendedor() {

		$this->validaAutenticacao();

		if(!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 2) {

			header('Location: /');
			die();

		}

		$verificar_status = Container::getModel('Venda');

		$id_vendedor = $_SESSION['id'];
		$date = date('Y-m-d');

		$existe_pendente = $verificar_status->existeVendaPendente($id_vendedor, $date);

		if($existe_pendente) {

			$this->view->notificacao = 'nova_venda';

		}

	}

	public function horarioFuncionamento($id_vendedor) {

		$horario = Container::getModel('Vendedor');
		$horario_funcionamento = $horario->buscarHorarioFuncionamento($id_vendedor);

		if($horario_funcionamento != false) {

			$horarios = array(
				1 => 'segunda',
				2 => 'terca',
				3 => 'quarta',
				4 => 'quinta',
				5 => 'sexta',
				6 => 'sabado',
				7 => 'domingo'
			);

			$de = $horarios[date('N')].'_de';
			$ate = $horarios[date('N')].'_ate';

			$horario_inicio = $horario_funcionamento[$de]; 
			$horario_fim = $horario_funcionamento[$ate]; 

			if($horario_inicio == null || $horario_fim == null) {

				return false;

			} else {

				$hora_atual = date('H:i:s');

				if ($hora_atual >= $horario_inicio && $hora_atual <= $horario_fim) {

					$data1 = new \DateTime($hora_atual);
                    $data2 = new \DateTime($horario_fim);
					
                    $diferenca = $data1->diff($data2);
					
                    $proximo_fim = $diferenca->format('%H:%I:%S');

					$retorno = [ 'aberto' => true, 'proximo_fim' => false];

					if($proximo_fim <= '00:45:00') {
						$retorno['proximo_fim'] = true;
					}

					return $retorno;

				} else {

					return false;

				}

			}

		} else {

			return false;

		}

	}

	public function validaAutenticacao() {

        Action::ativarSessao();

        if(!isset($_SESSION['id']) || empty($_SESSION['id']) || !isset($_SESSION['nome']) || empty($_SESSION['nome'])) {

            header('Location: /');
			die();
        }

    }

	public static function ativarSessao() {

		session_set_cookie_params(2592000);
        session_start();
        setcookie(session_name(), session_id(), COOKIE_OPTIONS_INFINITY);

	}

	protected function render($view, $layout = 'layout') {
		$this->view->page = $view;

		if(file_exists(PATH_VIEW.$layout.".phtml")) {
			require_once PATH_VIEW.$layout.".phtml";
		} else {
			$this->content();
		}
	}

	protected function content() {
		$classAtual = get_class($this);

		$classAtual = str_replace('App\\Controllers\\', '', $classAtual);

		$classAtual = strtolower(str_replace('Controller', '', $classAtual));

		require_once PATH_VIEW.$classAtual."/".$this->view->page.".phtml";
	}
}

?>