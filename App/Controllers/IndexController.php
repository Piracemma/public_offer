<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {

		Action::ativarSessao();

		if(!isset($_SESSION['id']) || empty($_SESSION['id']) || !isset($_SESSION['nome']) || empty($_SESSION['nome'])) {

			$this->render('index','layout2');

		} else if($_SESSION['tipo_usuario'] == 1) {			

			$this->notificarVendas();

			$this->view->pagina = 'inicio';

			$produtos = Container::getModel('Produto');
			$this->view->novidades = $produtos->novidades();
			$this->view->destaques = $produtos->destaques();

			$this->render('app');

		} else if($_SESSION['tipo_usuario'] == 2) {

			header("Location: /vendedor");

		}
		
	}

	public function login() {

		$this->render('login','layout2');
		
	}

	public function cadastreSe() {

		$this->render('cadastre_se','layout2');

	}
	
	public function registrarVendedor() {

		if(isset($_POST) && isset($_POST['aceita-termos']) && $_POST['aceita-termos'] == 'sim') {
		
			$padrao = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#\/])[A-Za-z\d@$!%*?&#\/]{8,20}$/';
			$senha = htmlspecialchars($_POST['senha'], ENT_QUOTES, 'UTF-8');
			$validasenha = htmlspecialchars($_POST['validasenha'],ENT_QUOTES, 'UTF-8');

			if (preg_match($padrao, $senha) && $senha == $validasenha && strlen($senha) >= 8) {
				
				if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {

					$temporario = $_FILES['imagem']['tmp_name'];
					
					$nomeArquivoArray = explode('.',$_FILES['imagem']['name']);

					if(isset($nomeArquivoArray[1])){
						if(preg_match('/^(jpeg|png|jpg)$/', end($nomeArquivoArray))) {
							
							$valida_tipo = getimagesize($temporario);

							$width = (float) $valida_tipo[0];

							$height = (float) $valida_tipo[1];
							
							$proporcao = $width / $height;

							if($proporcao > 1.03 || $proporcao < 0.97) {
								header("Location: /cadastre_se?erro=erro_imagem");
								die();
							}
				
							if($valida_tipo != false && isset($valida_tipo['mime'])){
				
								if(preg_match('/^image\/(jpeg|png)$/', $valida_tipo['mime'])){


									$senhacod = password_hash($senha,PASSWORD_DEFAULT);

									//caso senha valida adiciona os registros
									$nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
									$emailfiltro = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
									$email = filter_var($emailfiltro, FILTER_VALIDATE_EMAIL);
									$documento = htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8');
									$cidade = htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8');
									$endereco = htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8');
									$bairro = htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8');
									$telefone = htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8');
									$segmento = htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8');
									$sobrenos = htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8');


									//Horarios de Funcionamento
									$segunda_nao = isset($_POST['segunda-nao']) && $_POST['segunda-nao'] == 'segunda-nao' ? true : false; 
									$segunda_de = null;
									$segunda_ate = null;
									if($segunda_nao) {
										$segunda_de = null;
										$segunda_ate = null;
									} else {
										$segunda_de = htmlspecialchars($_POST['segunda-de'], ENT_QUOTES, 'UTF-8');
										$segunda_ate = htmlspecialchars($_POST['segunda-ate'], ENT_QUOTES, 'UTF-8');
									}

									$terca_nao = isset($_POST['terca-nao']) && $_POST['terca-nao'] == 'terca-nao' ? true : false; 
									$terca_de = null;
									$terca_ate = null;
									if($terca_nao) {
										$terca_de = null;
										$terca_ate = null;
									} else {
										$terca_de = htmlspecialchars($_POST['terca-de'], ENT_QUOTES, 'UTF-8');
										$terca_ate = htmlspecialchars($_POST['terca-ate'], ENT_QUOTES, 'UTF-8');
									}

									$quarta_nao = isset($_POST['quarta-nao']) && $_POST['quarta-nao'] == 'quarta-nao' ? true : false; 
									$quarta_de = null;
									$quarta_ate = null;
									if($quarta_nao) {
										$quarta_de = null;
										$quarta_ate = null;
									} else {
										$quarta_de = htmlspecialchars($_POST['quarta-de'], ENT_QUOTES, 'UTF-8');
										$quarta_ate = htmlspecialchars($_POST['quarta-ate'], ENT_QUOTES, 'UTF-8');
									}

									$quinta_nao = isset($_POST['quinta-nao']) && $_POST['quinta-nao'] == 'quinta-nao' ? true : false; 
									$quinta_de = null;
									$quinta_ate = null;
									if($quinta_nao) {
										$quinta_de = null;
										$quinta_ate = null;
									} else {
										$quinta_de = htmlspecialchars($_POST['quinta-de'], ENT_QUOTES, 'UTF-8');
										$quinta_ate = htmlspecialchars($_POST['quinta-ate'], ENT_QUOTES, 'UTF-8');
									}

									$sexta_nao = isset($_POST['sexta-nao']) && $_POST['sexta-nao'] == 'sexta-nao' ? true : false; 
									$sexta_de = null;
									$sexta_ate = null;
									if($sexta_nao) {
										$sexta_de = null;
										$sexta_ate = null;
									} else {
										$sexta_de = htmlspecialchars($_POST['sexta-de'], ENT_QUOTES, 'UTF-8');
										$sexta_ate = htmlspecialchars($_POST['sexta-ate'], ENT_QUOTES, 'UTF-8');
									}

									$sabado_nao = isset($_POST['sabado-nao']) && $_POST['sabado-nao'] == 'sabado-nao' ? true : false; 
									$sabado_de = null;
									$sabado_ate = null;
									if($sabado_nao) {
										$sabado_de = null;
										$sabado_ate = null;
									} else {
										$sabado_de = htmlspecialchars($_POST['sabado-de'], ENT_QUOTES, 'UTF-8');
										$sabado_ate = htmlspecialchars($_POST['sabado-ate'], ENT_QUOTES, 'UTF-8');
									}

									$domingo_nao = isset($_POST['domingo-nao']) && $_POST['domingo-nao'] == 'domingo-nao' ? true : false; 
									$domingo_de = null;
									$domingo_ate = null;
									if($domingo_nao) {
										$domingo_de = null;
										$domingo_ate = null;
									} else {
										$domingo_de = htmlspecialchars($_POST['domingo-de'], ENT_QUOTES, 'UTF-8');
										$domingo_ate = htmlspecialchars($_POST['domingo-ate'], ENT_QUOTES, 'UTF-8');
									}
									
									$dias_funcionamento = array(
										'segunda' => array(
											'de' => $segunda_de,
											'ate' => $segunda_ate
										),
										'terca' => array(
											'de' => $terca_de,
											'ate' => $terca_ate
										),
										'quarta' => array(
											'de' => $quarta_de,
											'ate' => $quarta_ate
										),
										'quinta' => array(
											'de' => $quinta_de,
											'ate' => $quinta_ate
										),
										'sexta' => array(
											'de' => $sexta_de,
											'ate' => $sexta_ate
										),
										'sabado' => array(
											'de' => $sabado_de,
											'ate' => $sabado_ate
										),
										'domingo' => array(
											'de' => $domingo_de,
											'ate' => $domingo_ate
										),
									);
									//Horarios de Funcionamento


									if($_POST['entrega_propria'] == 's') {

										$possui_entrega = true;

										$limpavalorentrega = htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8');
										$corrigePonto = explode(',',$limpavalorentrega);
										$valor_entrega = implode('.', $corrigePonto);

									} else if ($_POST['entrega_propria'] == 'n') {

										$possui_entrega = false;
										$valor_entrega = '0.00';

									} else {
							
										$this->render('cadastre_se','layout2');
										die();

									}

									//imagem
									$foto_perfil = time().'-'.rand(111111111, 999999999).'.'.end($nomeArquivoArray);
									$foto_caminho = 'img/vendedor/';
									$local = $foto_caminho . $foto_perfil;
									//fim imagem
										
									$vendedor = Container::getModel('Vendedor');
									
									$vendedor->__set('nome', $nome)->__set('email', $email)->__set('senha', $senhacod)->__set('validasenha', $validasenha)->__set('documento', $documento)->__set('cidade', $cidade)->__set('endereco', $endereco)->__set('bairro', $bairro)->__set('telefone', $telefone)->__set('segmento', $segmento)->__set('sobrenos', $sobrenos)->__set('foto_caminho', $foto_caminho)->__set('foto_perfil', $foto_perfil)->__set('possui_entrega', $possui_entrega)->__set('valor_entrega', $valor_entrega);

									if($vendedor->validarCadastro() && $vendedor->getPorEmail() == false) {

										if($vendedor->validarCidade()) {
							
											if(move_uploaded_file($temporario,$local)) {

												$codigo_verificacao = rand(111111,999999);

												$codigo_hash = password_hash($codigo_verificacao, PASSWORD_DEFAULT);

												$enviado = true;

												if($enviado) {
													
													session_start();
													
													$_SESSION['nome'] = $nome;
													$_SESSION['email'] = $email;
													$_SESSION['senha'] = $senhacod;
													$_SESSION['validasenha'] = $validasenha;
													$_SESSION['documento'] = $documento;
													$_SESSION['cidade'] = $cidade;
													$_SESSION['endereco'] = $endereco;
													$_SESSION['bairro'] = $bairro;
													$_SESSION['telefone'] = $telefone;
													$_SESSION['segmento'] = $segmento;
													$_SESSION['sobrenos'] = $sobrenos;
													$_SESSION['foto_caminho'] = $foto_caminho;
													$_SESSION['foto_perfil'] = $foto_perfil;
													$_SESSION['possui_entrega'] = $possui_entrega;
													$_SESSION['valor_entrega'] = $valor_entrega;
													$_SESSION['valida_tipo_usuario'] = 2;
													$_SESSION['horario_funcionamento'] = $dias_funcionamento;

													setcookie('validar_email', $codigo_hash, COOKIE_OPTIONS_TEMP);

													header("Location: /codigo_verificador");
													die();

												}

											} else {

												$this->view->vendedor = array(
													'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
													'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
													'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
													'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
													'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
													'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
													'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
													'entrega_propria' => htmlspecialchars($_POST['entrega_propria'], ENT_QUOTES, 'UTF-8'),
													'valor_entrega' => htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8'),
													'segmento' => htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8'),
													'sobrenos' => htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8'),
													'erro' => 'imagem1',
												);
									
												$this->render('cadastre_se','layout2');
												die();

											}                                    

										} else {

											$this->view->vendedor = array(
												'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
												'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
												'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
												'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
												'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
												'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
												'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
												'entrega_propria' => htmlspecialchars($_POST['entrega_propria'], ENT_QUOTES, 'UTF-8'),
												'valor_entrega' => htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8'),
												'segmento' => htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8'),
												'sobrenos' => htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8'),
												'erro' => 'erro',
											);
								
											$this->render('cadastre_se','layout2');
											die();

										}			

									} else {

										$this->view->vendedor = array(
											'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
											'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
											'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
											'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
											'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
											'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
											'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
											'entrega_propria' => htmlspecialchars($_POST['entrega_propria'], ENT_QUOTES, 'UTF-8'),
											'valor_entrega' => htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8'),
											'segmento' => htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8'),
											'sobrenos' => htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8'),
											'erro' => 'usuarioexistente'
										);
							
										$this->render('cadastre_se','layout2');
										die();

									}                            
				
								} else {
				
									$this->view->vendedor = array(
										'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
										'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
										'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
										'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
										'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
										'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
										'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
										'entrega_propria' => htmlspecialchars($_POST['entrega_propria'], ENT_QUOTES, 'UTF-8'),
										'valor_entrega' => htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8'),
										'segmento' => htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8'),
										'sobrenos' => htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8'),
										'erro' => 'imagem2'
									);
						
									$this->render('cadastre_se','layout2');
									die();
				
								}
				
							} else {

								$this->view->vendedor = array(
									'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
									'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
									'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
									'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
									'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
									'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
									'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
									'entrega_propria' => htmlspecialchars($_POST['entrega_propria'], ENT_QUOTES, 'UTF-8'),
									'valor_entrega' => htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8'),
									'segmento' => htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8'),
									'sobrenos' => htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8'),
									'erro' => 'imagem4'
								);
					
								$this->render('cadastre_se','layout2');
								die();
		
							}

						} else {

							$this->view->vendedor = array(
								'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
								'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
								'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
								'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
								'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
								'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
								'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
								'entrega_propria' => htmlspecialchars($_POST['entrega_propria'], ENT_QUOTES, 'UTF-8'),
								'valor_entrega' => htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8'),
								'segmento' => htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8'),
								'sobrenos' => htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8'),
								'erro' => 'imagem5'
							);
				
							$this->render('cadastre_se','layout2');
							die();

						}

					}
					
				} else {

					$this->view->vendedor = array(
						'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
						'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
						'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
						'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
						'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
						'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
						'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
						'entrega_propria' => htmlspecialchars($_POST['entrega_propria'], ENT_QUOTES, 'UTF-8'),
						'valor_entrega' => htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8'),
						'segmento' => htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8'),
						'sobrenos' => htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8'),
						'erro' => 'imagem'
					);
		
					$this->render('cadastre_se','layout2');
					die();

				}
			
			} else {

				$this->view->vendedor = array(
					'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
					'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
					'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
					'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
					'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
					'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
					'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
					'entrega_propria' => htmlspecialchars($_POST['entrega_propria'], ENT_QUOTES, 'UTF-8'),
					'valor_entrega' => htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8'),
					'segmento' => htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8'),
					'sobrenos' => htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8'),
					'erro' => 'erro',
				);

				$this->render('cadastre_se','layout2');
				die();
			
			}

		} else {
			header("Location: /");
			die();
		}		
		
	}

	public function registrarUsuario() {
		
		if(isset($_POST) && isset($_POST['aceita-termos']) && $_POST['aceita-termos'] == 'sim') {
		
			//validar senha
			$padrao = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#\/])[A-Za-z\d@$!%*?&#\/]{8,20}$/';
			$senha = htmlspecialchars($_POST['senha'], ENT_QUOTES, 'UTF-8');
			$validasenha = htmlspecialchars($_POST['validasenha'],ENT_QUOTES, 'UTF-8');

			if (preg_match($padrao, $senha) && $senha == $validasenha && strlen($senha) >= 8) {
				
				$senhacod = password_hash($senha,PASSWORD_DEFAULT);

				//caso senha valida adiciona os registros
				$nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
				$emailfiltro = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
				$email = filter_var($emailfiltro, FILTER_VALIDATE_EMAIL);
				$documento = htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8');
				$cidade = htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8');
				$endereco = htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8');
				$bairro = htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8');
				$end_referencia = htmlspecialchars($_POST['end_referencia'], ENT_QUOTES, 'UTF-8');
				$telefone = htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8');
					
				$usuario = Container::getModel('Usuario');
				
				$usuario->__set('nome', $nome)->__set('email', $email)->__set('senha', $senhacod)->__set('validasenha', $validasenha)->__set('documento', $documento)->__set('cidade', $cidade)->__set('endereco', $endereco)->__set('bairro', $bairro)->__set('end_referencia', $end_referencia)->__set('telefone', $telefone);

				if($usuario->validarCadastro() && $usuario->getPorEmail() == false) {

					if($usuario->validarCidade()) {

						$codigo_verificacao = rand(111111,999999);

						$codigo_hash = password_hash($codigo_verificacao, PASSWORD_DEFAULT);

						$enviado = true;

						if($enviado) {
							
							session_start();
							$_SESSION['nome'] = $nome;
							$_SESSION['email'] = $email;
							$_SESSION['senha'] = $senhacod;
							$_SESSION['validasenha'] = $validasenha;
							$_SESSION['documento'] = $documento;
							$_SESSION['cidade'] = $cidade;
							$_SESSION['endereco'] = $endereco;
							$_SESSION['bairro'] = $bairro;
							$_SESSION['end_referencia'] = $end_referencia;
							$_SESSION['telefone'] = $telefone;
							$_SESSION['valida_tipo_usuario'] = 1;

							setcookie('validar_email', $codigo_hash, COOKIE_OPTIONS_TEMP);

							header("Location: /codigo_verificador");
							die();

						} else {

							$this->view->usuario = array(
								'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
								'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
								'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
								'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
								'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
								'end_referencia' => htmlspecialchars($_POST['end_referencia'], ENT_QUOTES, 'UTF-8'),
								'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
								'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
								'erro' => 'erro',
							);
			
							$this->render('cadastre_se','layout2');
							die();

						}	
		
					} else {

						$this->view->usuario = array(
							'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
							'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
							'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
							'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
							'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
							'end_referencia' => htmlspecialchars($_POST['end_referencia'], ENT_QUOTES, 'UTF-8'),
							'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
							'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
							'erro' => 'erro',
						);
		
						$this->render('cadastre_se','layout2');
						die();
		
					}			
		
				} else {
		
					$this->view->usuario = array(
						'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
						'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
						'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
						'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
						'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
						'end_referencia' => htmlspecialchars($_POST['end_referencia'], ENT_QUOTES, 'UTF-8'),
						'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
						'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
						'erro' => 'usuarioexistente'
					);
		
					$this->render('cadastre_se','layout2');
					die();
		
				}

			} else {

				$this->view->usuario = array(
					'nome' => htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8'),
					'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
					'documento' => htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8'),
					'endereco' => htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8'),
					'bairro' => htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8'),
					'end_referencia' => htmlspecialchars($_POST['end_referencia'], ENT_QUOTES, 'UTF-8'),
					'telefone' => htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8'),
					'cidade' => htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8'),
					'erro' => 'erro',
				);

				$this->render('cadastre_se','layout2');
				die();

			}

		} else {
			header("Location: /");
			die();
		}

	}

	

}


?>