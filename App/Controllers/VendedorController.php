<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class VendedorController extends Action {

    public function vendedor() {

		$this->validaVendedor();

		if(isset($_GET['pagina']) && $_GET['pagina'] == 'historico') {

			$produto = Container::getModel('Produto');

			//total de registros
			$total_registros = $produto->totaisVendaHistorico($_SESSION['id'], date('y-m-d',mktime(0,0,0,date('m'),date('d')-30,date('y')))." 00:00:00", date('y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('y')))." 23:59:59");

			//paginacao
			if(isset($_GET['page']) && !empty($_GET['page'])) {

				$page = intval(htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'));

			} else {

				$page = 1;

			}
			//LIMIT e OFFSET tem que jogar na pesquisa do banco de dados
			$limit = 10;
			$offset = ($page - 1) * $limit;

			$this->view->total_paginas = ceil($total_registros['contagem'] / $limit);
			$this->view->pagina_atual = $page;
			$this->view->range_paginas = 3;

			$query = "SELECT v.id, v.data_venda, v.entrega_propria, v.valor_entrega, v.total_produtos, v.total_compra, u.nome as nome_usuario, u.telefone as telefone_usuario, v.cupom, v.id_cupom, v.valor_desconto, c.cupom as nome_cupom, c.desconto, f.finalizadora, v.observacao as observacao_compra, vi.id_produto, vi.nome_produto, vi.preco, vi.quantidade, vi.total, vi.observacao as observacao_item, v.endereco as endereco_alternativo, u.endereco, p.imagem1, v.aprovado, v.motivo_cancelamento  FROM vendas AS v
			INNER JOIN venda_item vi on v.id = vi.id_venda
			INNER JOIN vendedores ve on v.id_vendedor = ve.id
			INNER JOIN finalizadoras f on v.id_finalizadora = f.id
			INNER JOIN usuarios u on v.id_usuario = u.id
			INNER JOIN produtos p on vi.id_produto = p.id
			LEFT JOIN cupons c on v.id_cupom = c.id
			WHERE v.id_vendedor = ? AND data_venda BETWEEN ? AND ? AND (v.aprovado = 1 OR v.aprovado = 0 OR v.aprovado = 3) ORDER BY v.data_venda DESC LIMIT $limit OFFSET $offset";

			$data_inicial = date('y-m-d',mktime(0,0,0,date('m'),date('d')-30,date('y')))." 00:00:00";
			$data_final = date('y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('y')))." 23:59:59";

			$condictions = [$_SESSION['id'], $data_inicial, $data_final];
			$vendas_gerais = $produto->queryAll($query,$condictions);
			$vendas = array();

			foreach($vendas_gerais as $venda_unitaria) {

				if(!empty($vendas)) {

					$venda_existe = false;

					foreach($vendas as $key => $venda) {

						if($venda_unitaria['id'] == $venda['resumo']['id_venda']) {

							$venda_existe = true;

							$vendas[$key]['itens'][] = array(
								
								'nome_produto' => $venda_unitaria['nome_produto'],
								'imagem' => $venda_unitaria['imagem1'],
								'preco' => $venda_unitaria['preco'],
								'quantidade' => $venda_unitaria['quantidade'],
								'total' => $venda_unitaria['total'],
								'observacao_item' => $venda_unitaria['observacao_item'],
							);

						}

					}

					if(!$venda_existe) {

						$endereco = $venda_unitaria['endereco'];
						if(!empty($venda_unitaria['endereco_alternativo'])) {
							$endereco = $venda_unitaria['endereco_alternativo'];
						}

						$date = date_create($venda_unitaria['data_venda']);
						$data = date_format($date, 'd/m H:i');

						$vendas[] = array(
							'resumo' => array(
								'id_venda' => $venda_unitaria['id'],
								'data_venda' => $data,
								'entrega_propria' => $venda_unitaria['entrega_propria'],
								'valor_entrega' => $venda_unitaria['valor_entrega'],
								'total_produtos' => $venda_unitaria['total_produtos'],
								'total_compra' => $venda_unitaria['total_compra'],
								'comprador' => $venda_unitaria['nome_usuario'],
								'telefone' => $venda_unitaria['telefone_usuario'],
								'finalizadora' => $venda_unitaria['finalizadora'],
								'observacao_compra' => $venda_unitaria['observacao_compra'],
								'endereco' => $endereco,
								'status' => $venda_unitaria['aprovado'],
								'motivo_cancelamento' => $venda_unitaria['motivo_cancelamento'],
								'cupom' => $venda_unitaria['cupom'],
								'id_cupom' => $venda_unitaria['id_cupom'],
								'valor_desconto' => $venda_unitaria['valor_desconto'],
								'nome_cupom' => $venda_unitaria['nome_cupom'],
								'desconto' => $venda_unitaria['desconto'],
							),
							'itens' => array(
								array(
									
									'nome_produto' => $venda_unitaria['nome_produto'],
									'imagem' => $venda_unitaria['imagem1'],
									'preco' => $venda_unitaria['preco'],
									'quantidade' => $venda_unitaria['quantidade'],
									'total' => $venda_unitaria['total'],
									'observacao_item' => $venda_unitaria['observacao_item'],
								),
							)
						);

					}

				} else {

					$endereco = $venda_unitaria['endereco'];
					if(!empty($venda_unitaria['endereco_alternativo'])) {
						$endereco = $venda_unitaria['endereco_alternativo'];
					}

					$date = date_create($venda_unitaria['data_venda']);
						$data = date_format($date, 'd/m H:i');

						$vendas[] = array(
							'resumo' => array(
								'id_venda' => $venda_unitaria['id'],
								'data_venda' => $data,
								'entrega_propria' => $venda_unitaria['entrega_propria'],
								'valor_entrega' => $venda_unitaria['valor_entrega'],
								'total_produtos' => $venda_unitaria['total_produtos'],
								'total_compra' => $venda_unitaria['total_compra'],
								'comprador' => $venda_unitaria['nome_usuario'],								
								'telefone' => $venda_unitaria['telefone_usuario'],
								'finalizadora' => $venda_unitaria['finalizadora'],
								'observacao_compra' => $venda_unitaria['observacao_compra'],
								'endereco' => $endereco,
								'status' => $venda_unitaria['aprovado'],
								'motivo_cancelamento' => $venda_unitaria['motivo_cancelamento'],
								'cupom' => $venda_unitaria['cupom'],
								'id_cupom' => $venda_unitaria['id_cupom'],
								'valor_desconto' => $venda_unitaria['valor_desconto'],
								'nome_cupom' => $venda_unitaria['nome_cupom'],
								'desconto' => $venda_unitaria['desconto'],
							),
							'itens' => array(
								array(
									
									'nome_produto' => $venda_unitaria['nome_produto'],
									'imagem' => $venda_unitaria['imagem1'],
									'preco' => $venda_unitaria['preco'],
									'quantidade' => $venda_unitaria['quantidade'],
									'total' => $venda_unitaria['total'],
									'observacao_item' => $venda_unitaria['observacao_item'],
								),
							)
						);

				}

			}

			$this->view->vendas = $vendas;

		}

        $this->view->pagina = 'inicio';

        $this->render('vendedor');
    }

	public function apiVendas() {

		$this->validaVendedor();

		$this->removerPendentePrazo();

		$produto = Container::getModel('Produto');

		$id_vendedor = $_SESSION['id'];
		$data_inicial_pendentes = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d')-4,date('Y')));
		$data_inicial_gerais = date("Y-m-d")." 00:00:00";
		$data_final = date("Y-m-d")." 23:59:59";

		$query = "SELECT v.id, v.data_venda, v.entrega_propria, v.valor_entrega, v.total_produtos, v.total_compra, u.nome as nome_usuario, v.cupom, v.id_cupom, v.valor_desconto, c.cupom as nome_cupom, c.desconto, f.finalizadora, v.observacao as observacao_compra, vi.id_produto, vi.nome_produto, vi.preco, vi.quantidade, vi.total, vi.observacao as observacao_item, v.endereco as endereco_alternativo, u.telefone, u.endereco, p.imagem1, v.aprovado, v.motivo_cancelamento  
		FROM vendas AS v
		INNER JOIN venda_item vi on v.id = vi.id_venda
		INNER JOIN vendedores ve on v.id_vendedor = ve.id
		INNER JOIN finalizadoras f on v.id_finalizadora = f.id
		INNER JOIN usuarios u on v.id_usuario = u.id
		INNER JOIN produtos p on vi.id_produto = p.id
		LEFT JOIN cupons c on v.id_cupom = c.id
		WHERE v.id_vendedor = ? AND v.aprovado IN (2) AND data_venda BETWEEN ? AND ?
		
		UNION
		
		SELECT v.id, v.data_venda, v.entrega_propria, v.valor_entrega, v.total_produtos, v.total_compra, u.nome as nome_usuario, v.cupom, v.id_cupom, v.valor_desconto, c.cupom as nome_cupom, c.desconto, f.finalizadora, v.observacao as observacao_compra, vi.id_produto, vi.nome_produto, vi.preco, vi.quantidade, vi.total, vi.observacao as observacao_item, v.endereco as endereco_alternativo, u.telefone, u.endereco, p.imagem1, v.aprovado, v.motivo_cancelamento  
		FROM vendas AS v
		INNER JOIN venda_item vi on v.id = vi.id_venda
		INNER JOIN vendedores ve on v.id_vendedor = ve.id
		INNER JOIN finalizadoras f on v.id_finalizadora = f.id
		INNER JOIN usuarios u on v.id_usuario = u.id
		INNER JOIN produtos p on vi.id_produto = p.id
		LEFT JOIN cupons c on v.id_cupom = c.id
		WHERE v.id_vendedor = ? AND v.aprovado IN (0,1,3) AND data_venda BETWEEN ? AND ?
		ORDER BY (aprovado = 2) DESC, data_venda DESC;
		";

		

		$condictions = [$id_vendedor, $data_inicial_pendentes, $data_final, $id_vendedor, $data_inicial_gerais, $data_final];
		$vendas_gerais = $produto->queryAll($query,$condictions);
		$vendas = array();
		$aberto = false;
		$proximo_fim = true;
		$horario_funcionamento = $this->horarioFuncionamento($id_vendedor);
		if($horario_funcionamento != false) {

			$aberto = $horario_funcionamento['aberto'];
			$proximo_fim = $horario_funcionamento['proximo_fim'];

		}

		foreach($vendas_gerais as $venda_unitaria) {

            if(!empty($vendas)) {

                $venda_existe = false;

                foreach($vendas as $key => $venda) {

                    if($venda_unitaria['id'] == $venda['resumo']['id_venda']) {

                        $venda_existe = true;

                        $vendas[$key]['itens'][] = array(
                            
                            'nome_produto' => $venda_unitaria['nome_produto'],
							'imagem' => $venda_unitaria['imagem1'],
                            'preco' => $venda_unitaria['preco'],
                            'quantidade' => $venda_unitaria['quantidade'],
                            'total' => $venda_unitaria['total'],
                            'observacao_item' => $venda_unitaria['observacao_item'],
                        );

                    }

                }

                if(!$venda_existe) {

                    $endereco = $venda_unitaria['endereco'];
                    if(!empty($venda_unitaria['endereco_alternativo'])) {
                        $endereco = $venda_unitaria['endereco_alternativo'];
                    }

                    $date = date_create($venda_unitaria['data_venda']);
                    $data = date_format($date, 'H:i');

                    $vendas[] = array(
                        'resumo' => array(
                            'id_venda' => $venda_unitaria['id'],
                            'data_venda' => $data,
                            'entrega_propria' => $venda_unitaria['entrega_propria'],
                            'valor_entrega' => $venda_unitaria['valor_entrega'],
                            'total_produtos' => $venda_unitaria['total_produtos'],
                            'total_compra' => $venda_unitaria['total_compra'],
                            'comprador' => $venda_unitaria['nome_usuario'],
                            'finalizadora' => $venda_unitaria['finalizadora'],
                            'observacao_compra' => $venda_unitaria['observacao_compra'],
                            'endereco' => $endereco,
							'telefone' => $venda_unitaria['telefone'],
							'status' => $venda_unitaria['aprovado'],
							'motivo_cancelamento' => $venda_unitaria['motivo_cancelamento'],
							'cupom' => $venda_unitaria['cupom'],
							'id_cupom' => $venda_unitaria['id_cupom'],
							'valor_desconto' => $venda_unitaria['valor_desconto'],
							'nome_cupom' => $venda_unitaria['nome_cupom'],
							'desconto' => $venda_unitaria['desconto'],
							'aberto' => $aberto,
							'proximo_fim' => $proximo_fim
                        ),
                        'itens' => array(
                            array(
                                
                                'nome_produto' => $venda_unitaria['nome_produto'],
								'imagem' => $venda_unitaria['imagem1'],
                                'preco' => $venda_unitaria['preco'],
                                'quantidade' => $venda_unitaria['quantidade'],
                                'total' => $venda_unitaria['total'],
                                'observacao_item' => $venda_unitaria['observacao_item'],
                            ),
                        )
                    );

                }

            } else {

                $endereco = $venda_unitaria['endereco'];
                if(!empty($venda_unitaria['endereco_alternativo'])) {
                    $endereco = $venda_unitaria['endereco_alternativo'];
                }

                $date = date_create($venda_unitaria['data_venda']);
                    $data = date_format($date, 'H:i');

                    $vendas[] = array(
                        'resumo' => array(
                            'id_venda' => $venda_unitaria['id'],
                            'data_venda' => $data,
                            'entrega_propria' => $venda_unitaria['entrega_propria'],
                            'valor_entrega' => $venda_unitaria['valor_entrega'],
                            'total_produtos' => $venda_unitaria['total_produtos'],
                            'total_compra' => $venda_unitaria['total_compra'],
                            'comprador' => $venda_unitaria['nome_usuario'],
                            'finalizadora' => $venda_unitaria['finalizadora'],
                            'observacao_compra' => $venda_unitaria['observacao_compra'],
                            'endereco' => $endereco,
							'telefone' => $venda_unitaria['telefone'],
							'status' => $venda_unitaria['aprovado'],
							'motivo_cancelamento' => $venda_unitaria['motivo_cancelamento'],
							'cupom' => $venda_unitaria['cupom'],
							'id_cupom' => $venda_unitaria['id_cupom'],
							'valor_desconto' => $venda_unitaria['valor_desconto'],
							'nome_cupom' => $venda_unitaria['nome_cupom'],
							'desconto' => $venda_unitaria['desconto'],
							'aberto' => $aberto,
							'proximo_fim' => $proximo_fim
                        ),
                        'itens' => array(
                            array(
                                
                                'nome_produto' => $venda_unitaria['nome_produto'],
								'imagem' => $venda_unitaria['imagem1'],
                                'preco' => $venda_unitaria['preco'],
                                'quantidade' => $venda_unitaria['quantidade'],
                                'total' => $venda_unitaria['total'],
                                'observacao_item' => $venda_unitaria['observacao_item'],
                            ),
                        )
                    );

            }

        }

		$this->view->vendas = $vendas;

		$this->render('api_vendas', 'sem-layout');

	}

	public function apiVendaPendente() {

		$this->validaVendedor();

		$produto = Container::getModel('Produto');

		$data_final = date('Y-m-d').' 23:59:59';
		$data_inicial = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d')-4,date('Y'))); //para buscar vendas pendentes de dias retroativos, necessario realizar o busca duplica (para vendas pendentes uma e para vendas gerais outra)

		$query = "SELECT * FROM vendas WHERE id_vendedor = ? and aprovado = 2 AND data_venda BETWEEN ? AND ?";
		$condictions = [$_SESSION['id'], $data_inicial, $data_final];

		$retorno = $produto->queryAll($query,$condictions);

		if($retorno != false) {
			echo json_encode(true);
		} else {
			echo json_encode(false);
		}

	}

	public function aprovarVenda() {

		$this->validaVendedor();

		if(isset($_GET['venda']) && !empty($_GET['venda'])) {

			$id_venda = htmlspecialchars($_GET['venda'], ENT_QUOTES, 'UTF-8');
			$id_vendedor = $_SESSION['id'];

			$venda = Container::getModel('Venda');
			$verifica_venda = $venda->validaVendaPendente($id_venda, $id_vendedor);
			$data_venda = $verifica_venda['data_venda'];
			$data_atual = date('Y-m-d H:i:s');

			if($verifica_venda != false) {

				$aprovar_venda = $venda->aprovarVenda($id_venda, $id_vendedor, $data_atual, $data_venda);
				if($aprovar_venda != false) {

					//WhatsappController::enviarModelo('aviso_cliente_aceito', $id_venda);

					if(!$verifica_venda['entrega_propria']) {

						//WhatsappController::enviarModelo('guia_entregador', $id_venda);

					}

					header("Location: /vendedor?act=venda_aprovada");

				} else {

					header("Location: /vendedor?act=venda_naoaprovada");

				}

			} else {

				header("Location: /vendedor?act=nao_encontrada");

			}

		} else {
			header("Location: /vendedor?act=nao_encontrada");
		}

	}

	public function recusarVenda() {

		$this->validaVendedor();

		if(isset($_GET['venda']) && !empty($_GET['venda']) && isset($_POST['motivo_cancelamento']) && !empty($_POST['motivo_cancelamento'])) {

			$id_venda = htmlspecialchars($_GET['venda'], ENT_QUOTES, 'UTF-8');
			$id_vendedor = $_SESSION['id'];
			$motivo_cancelamento = htmlspecialchars($_POST['motivo_cancelamento'], ENT_QUOTES, 'UTF-8');

			$venda = Container::getModel('Venda');
			$verifica_venda = $venda->validaVendaPendente($id_venda, $id_vendedor);
			$data_venda = $verifica_venda['data_venda'];
			$data_atual = date('Y-m-d H:i:s');

			if($verifica_venda != false) {

				$venda_itens = $venda->buscarVendaItem($id_venda);
				foreach($venda_itens as $item) {

					$id_produto = $item['id_produto'];
					$quantidade = $item['quantidade'];

					$venda->estornarEstoqueVendaCancelada($id_produto, $quantidade, $id_vendedor);

				}
				$venda->recusarVenda($id_venda, $id_vendedor, $motivo_cancelamento, $data_atual, $data_venda);
				//WhatsappController::enviarModelo('aviso_cliente_recusado', $id_venda);
				header("Location: /vendedor?act=venda_recusada");
				//Fazer as tratativas da venda
				//voltar o estoque dos itens para a loja

			} else {

				header("Location: /vendedor?act=nao_encontrada");

			}

		} else {
			header("Location: /vendedor?act=nao_encontrada");
		}
		
	}

    public function relatorio() {

		$this->validaVendedor();

        $this->view->pagina = 'relatorio';

        $this->render('relatorio');
    }

	public function relatorioVendedor() {

		$this->validaVendedor();

		if(isset($_POST) && !empty($_POST['de']) && !empty($_POST['ate']) && !empty($_POST['estado'])) {

			$id_vendedor = $_SESSION['id'];

			$de = htmlspecialchars($_POST['de'], ENT_QUOTES, 'UTF-8');
			$ate = htmlspecialchars($_POST['ate'], ENT_QUOTES, 'UTF-8');
			$estado = htmlspecialchars($_POST['estado'], ENT_QUOTES, 'UTF-8');

			$relatorio = Container::getModel('Vendedor');

			$this->view->vendedor = $relatorio->vendedor($id_vendedor);

			$this->view->vendas = $relatorio->relatorioPersonalizado($id_vendedor, $de, $ate, $estado);

			$this->view->totais = $relatorio->relatorioTotais($id_vendedor, $de, $ate);

			if(empty($this->view->totais['geral']['quantidade'])) {

				header("Location: /relatorio?act=nenhuma_venda");
				die();

			}

			$this->view->fretes = $relatorio->relatorioFrete($id_vendedor, $de, $ate);

			$this->render('relatorios-vendedor', 'sem-layout');

		} else {

			header("Location: /relatorio?act=erro");
			die();

		}        

	}

    public function cadastroProduto() {

		$this->validaVendedor();

        $this->view->pagina = 'cadastro de produtos';

        $this->render('cadastro_produto');
    }

    public function cadastrarProduto() {

		$this->validaVendedor();

		$i = 1;
		$imagens = array();

		if(isset($_FILES['imagem1']) && $_FILES['imagem1']['error'] == 0) {
			foreach($_FILES as $imagem) {

				if(isset($imagem) && $imagem['error'] == 0) {

					$temporario = $imagem['tmp_name'];
						
					$nomeArquivoArray = explode('.',$imagem['name']);

					if(isset($nomeArquivoArray[1])){
						if(preg_match('/^(jpeg|png|jpg)$/', end($nomeArquivoArray))) {
								
							$valida_tipo = getimagesize($temporario);

							$width = (float) $valida_tipo[0];

							$height = (float) $valida_tipo[1];
							
							$proporcao = $width / $height;

							if($proporcao > 1.03 || $proporcao < 0.97) {
								header("Location: /cadastro_produto?act=medidas");
								die();
							}
					
							if($valida_tipo != false && isset($valida_tipo['mime'])){
					
								if(preg_match('/^image\/(jpeg|png)$/', $valida_tipo['mime'])){

									$foto_nome = time().'-'.rand(111111111, 999999999).'.'.end($nomeArquivoArray);
									$foto_caminho = 'img/produto/';
									$local = $foto_caminho . $foto_nome;

									$imagens[] = array(
										'nome' => $foto_nome,
										'caminho' => $foto_caminho,
										'local' => $local,
										'temporario' => $temporario
									);

								}

							}

						}

					}
						
					$i++;

				}

			}			
				
		} else {

			header("Location: /cadastro_produto?act=error_imagem");
			die();

		}

		if(!isset($imagens[0])) {

			header("Location: /cadastro_produto?act=error_imagem");
			die();

		}
								
		$nome_produto = htmlspecialchars($_POST['nome_produto'], ENT_QUOTES, 'UTF-8');
		$descricao = htmlspecialchars($_POST['descricao'], ENT_QUOTES, 'UTF-8');
		$variacao = htmlspecialchars($_POST['variacao'], ENT_QUOTES, 'UTF-8');
		$categoria = htmlspecialchars($_POST['categoria'], ENT_QUOTES, 'UTF-8');
		$publico = htmlspecialchars($_POST['publico'], ENT_QUOTES, 'UTF-8');

		$limpapreco = htmlspecialchars($_POST['preco'], ENT_QUOTES, 'UTF-8');
		$corrigePonto = explode(',',$limpapreco);
		$preco = implode('.', $corrigePonto);

		$estoque = htmlspecialchars($_POST['estoque'], ENT_QUOTES, 'UTF-8');
		$imagem1 = isset($imagens[0]) ? $imagens[0]['local'] : false;
		$imagem2 = isset($imagens[1]) ? $imagens[1]['local'] : false;
		$imagem3 = isset($imagens[2]) ? $imagens[2]['local'] : false;
									
		$produto = Container::getModel('Produto');
		$produto->__set('id_vendedor', $_SESSION['id'])->__set('nome_produto', $nome_produto)->__set('descricao',$descricao)->__set('variacao',$variacao)->__set('categoria',$categoria)->__set('publico',$publico)->__set('preco',$preco)->__set('estoque',$estoque)->__set('imagem1',$imagem1)->__set('imagem2',$imagem2)->__set('imagem3',$imagem3);

		if($produto->validarDados()) {			
						
			foreach($imagens as $imagem) {

				move_uploaded_file($imagem['temporario'],$imagem['local']);
				
			}

			if($produto->salvar()){

				header("Location: /cadastro_produto?act=sucesso");
				die();

			} else {

				foreach($imagens as $imagem) {

					if(file_exists($imagem['local'])) {

						unlink($imagem['local']);

					}

				}

				header("Location: /cadastro_produto?act=erro");
				die();

			}

		} else {
						
			header("Location: /cadastro_produto?act=erro");
			die();

		}            

    }

    public function meusProdutos() {

		$this->validaVendedor();

		$produtos = Container::getModel('Produto');

		//total de registros
		$total_registros = $produtos->consultaVariavel('produtos', 'COUNT(*) as contagem', ['id_vendedor'], [$_SESSION['id']]);

		//paginacao
		if(isset($_GET['page']) && !empty($_GET['page'])) {

			$page = intval(htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'));

		} else {

			$page = 1;

		}
		$limit = 12;
		$offset = ($page - 1) * $limit;

		$this->view->total_paginas = ceil($total_registros[0]['contagem'] / $limit);
		$this->view->pagina_atual = $page;
		$this->view->range_paginas = 2;

		$tabela = 'produtos';
		$busca = 'id, nome, descricao, variacao, preco, estoque, categoria, imagem1, num_vendas, ativo';
		$where = ['id_vendedor'];
		$where_campo = [$_SESSION['id']];
		$orderby = 'data_criacao DESC';

		$this->view->produtos = $produtos->consultaVariavel($tabela, $busca, $where, $where_campo, $orderby, $limit, $offset);

        $this->view->pagina = 'meus produtos';

        $this->render('meus_produtos');
    }

	public function editarProduto() {

		$this->validaVendedor();

		if(isset($_GET['id']) && !empty($_GET['id'])) {

			$id_produto = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
			$id_vendedor = $_SESSION['id'];

			$produto = Container::getModel('Produto');

			$query = "SELECT * FROM produtos WHERE id_vendedor = ? AND id = ?";
			$condictions = [$id_vendedor, $id_produto];
			$dados_produto = $produto->queryAll($query,$condictions);

			if($dados_produto != false) {

				$this->view->produto = $dados_produto;

				$this->render('editar_produto');

			}

		} else {

			header("Location: /meus_produto?act=nao_encontrado");

		}		

	}

	public function atualizarProduto() {

		$this->validaVendedor();

		if(isset($_POST) && !empty($_POST)) {

			$id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');
			$descricao = htmlspecialchars($_POST['descricao'], ENT_QUOTES, 'UTF-8');
			$variacao = htmlspecialchars($_POST['variacao'], ENT_QUOTES, 'UTF-8');
			$preco = htmlspecialchars($_POST['preco'], ENT_QUOTES, 'UTF-8');
			$estoque = htmlspecialchars($_POST['estoque'], ENT_QUOTES, 'UTF-8');

			$produto = Container::getModel('Produto');
			$produto->__set('descricao', $descricao)->__set('variacao', $variacao)->__set('preco', $preco)->__set('estoque', $estoque)->__set('id_vendedor', $_SESSION['id']);
			$dados_validos = $produto->validarDadosUpdate();

			if($dados_validos) {

				$update = $produto->atualizar($id);

				if($update) {

					header("Location: /meus_produtos?act=atualizado");

				} else {

					header("Location: /editar_produto?id=$id&act=erro_atualizar");

				}

			} else {

				header("Location: /editar_produto?id=$id&act=erro_validar");

			}

		} else {

			header("Location: /meus_produtos?act=erro");

		}

	}

	public function excluirProduto() {

		$this->validaVendedor();

		if(isset($_GET['produto']) && !empty($_GET['produto'])) {

			$id = htmlspecialchars($_GET['produto'], ENT_QUOTES, 'UTF-8');

			$produto = Container::getModel('Produto');

			$query = "SELECT * FROM produtos WHERE id = ? AND id_vendedor = ?";
			$condictions = [$id, $_SESSION['id']];

			$busca_produto = $produto->queryAll($query, $condictions);

			if($busca_produto != false) {

				if(isset($busca_produto[0]['imagem1'])) {
					$imagem = $busca_produto[0]['imagem1'];
					if(!unlink($imagem)) {
						header("Location: /meus_produtos?act=erro_excluir_img");
					}
				}
				if(isset($busca_produto[0]['imagem2'])) {
					$imagem = $busca_produto[0]['imagem2'];
					if(!unlink($imagem)) {
						header("Location: /meus_produtos?act=erro_excluir_img");
					}
				}
				if(isset($busca_produto[0]['imagem3'])) {
					$imagem = $busca_produto[0]['imagem3'];
					if(!unlink($imagem)) {
						header("Location: /meus_produtos?act=erro_excluir_img");
					}
				}
				$excluir_produto = $produto->excluir($id, $_SESSION['id']);

				if($excluir_produto) {
					header("Location: /meus_produtos?act=excluido");
				} else {
					header("Location: /meus_produtos?act=nao_excluido");
				}

			} else {

				header("Location: /meus_produtos?act=erro");

			}

		} else {

			header("Location: /meus_produtos?act=erro");

		}

	}

    public function editarPerfil() {

		$this->validaVendedor();

		$id_vendedor = $_SESSION['id'];

		$vendedor = Container::getModel('Vendedor');

		$dados_vendedor = $vendedor->vendedor($id_vendedor);

		$this->view->vendedor = [
			'nome' => $dados_vendedor['nome'],
			'email' => $dados_vendedor['email'],
			'documento' => $dados_vendedor['documento'],
			'cidade' => $dados_vendedor['cidade'],
			'endereco' => $dados_vendedor['endereco'],
			'bairro' => $dados_vendedor['bairro'],
			'telefone' => $dados_vendedor['telefone'],
			'segmento' => $dados_vendedor['segmento'],
			'entrega_propria' => $dados_vendedor['entrega_propria'],
			'valor_entrega' => $dados_vendedor['valor_entrega'],
			'sobrenos' => $dados_vendedor['descricao'],
			'imagem1' => $dados_vendedor['foto_caminho'].$dados_vendedor['foto_perfil'],
		];

        $this->view->pagina = 'editar perfil';

        $this->render('editar_perfil');

    }

	public function atualizarPerfil() {

		$this->validaVendedor();

		$nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
		$documento = htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8');
		$cidade = htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8');
		$endereco = htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8');
		$bairro = htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8');
		$telefone = htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8');
		$segmento = htmlspecialchars($_POST['segmento'], ENT_QUOTES, 'UTF-8');
		$sobrenos = htmlspecialchars($_POST['sobrenos'], ENT_QUOTES, 'UTF-8');

		$imagem_atual = '';

		if($_POST['entrega_propria'] == 's') {

			$possui_entrega = true;

			$limpavalorentrega = htmlspecialchars($_POST['valor_entrega'], ENT_QUOTES, 'UTF-8');
			$corrigePonto = explode(',',$limpavalorentrega);
			$valor_entrega = implode('.', $corrigePonto);

		} else if ($_POST['entrega_propria'] == 'n') {

			$possui_entrega = false;
			$valor_entrega = '0.00';

		} else {

			header("Location: /editar_perfil?act=erro");
			die();

		}


		$imagens = null;

		if(isset($_FILES['imagem1']) && $_FILES['imagem1']['error'] == 0) {

			$imagem = $_FILES['imagem1'];

			if(isset($imagem) && $imagem['error'] == 0) {

				$temporario = $imagem['tmp_name'];
					
				$nomeArquivoArray = explode('.',$imagem['name']);

				if(isset($nomeArquivoArray[1])){
					if(preg_match('/^(jpeg|png|jpg)$/', end($nomeArquivoArray))) {
							
						$valida_tipo = getimagesize($temporario);

						$width = (float) $valida_tipo[0];

						$height = (float) $valida_tipo[1];
						
						$proporcao = $width / $height;

						if($proporcao > 1.03 || $proporcao < 0.97) {
							header("Location: /editar_perfil?act=medidas");
							die();
						}
				
						if($valida_tipo != false && isset($valida_tipo['mime'])){
				
							if(preg_match('/^image\/(jpeg|png)$/', $valida_tipo['mime'])){

								$foto_nome = time().'-'.rand(111111111, 999999999).'.'.end($nomeArquivoArray);
								$foto_caminho = 'img/vendedor/';
								$local = $foto_caminho . $foto_nome;

								$imagens = array(
									'nome' => $foto_nome,
									'caminho' => $foto_caminho,
									'local' => $local,
									'temporario' => $temporario
								);

							}

						}

					}

				}

			}			
				
		}

		$vendedor = Container::getModel('Vendedor');

		$id_vendedor = $_SESSION['id'];

		if(!empty($imagens)) {

			$dados_vendedor = $vendedor->vendedor($id_vendedor);

			$imagem_atual = $dados_vendedor['foto_caminho'].$dados_vendedor['foto_perfil'];

			move_uploaded_file($imagens['temporario'],$imagens['local']);
			unlink($imagem_atual);
			
			$vendedor->__set('nome', $nome)->__set('documento', $documento)->__set('cidade', $cidade)->__set('endereco', $endereco)->__set('bairro', $bairro)->__set('telefone', $telefone)->__set('segmento', $segmento)->__set('sobrenos', $sobrenos)->__set('possui_entrega', $possui_entrega)->__set('valor_entrega', $valor_entrega)->__set('foto_caminho', 'img/vendedor/')->__set('foto_perfil', $imagens['nome'])->__set('alterar_imagem', true);

			if($vendedor->validarAtualizarCadastro()) {

				if($vendedor->validarCidade()) {

					move_uploaded_file($imagens['temporario'],$imagens['local']);
					unlink($imagem_atual);

					$vendedor->atualizar($id_vendedor);

					header("Location: /editar_perfil?act=cadastro_atualizado");

				} else {
					header("Location: /editar_perfil?act=erro_atualizar");
				}
				
			} else {
				header("Location: /editar_perfil?act=erro_atualizar");
			}

		} else {

			$vendedor->__set('nome', $nome)->__set('documento', $documento)->__set('cidade', $cidade)->__set('endereco', $endereco)->__set('bairro', $bairro)->__set('telefone', $telefone)->__set('segmento', $segmento)->__set('sobrenos', $sobrenos)->__set('possui_entrega', $possui_entrega)->__set('valor_entrega', $valor_entrega);

			if($vendedor->validarAtualizarCadastro()) {

				if($vendedor->validarCidade()) {

					$vendedor->atualizar($id_vendedor);

					header("Location: /editar_perfil?act=cadastro_atualizado");

				} else {
					header("Location: /editar_perfil?act=erro_atualizar");
				}
				
			} else {
				header("Location: /editar_perfil?act=erro_atualizar");
			}

		}
		
		

		$nina = '5 ';

	}

	public function removerPendentePrazo() {

		$id_vendedor = $_SESSION['id'];

		$remover = Container::getModel('Venda');

		$pendentes_atrasadas = $remover->pendentesAtrasadas($id_vendedor);

		if($pendentes_atrasadas != false) {

			foreach($pendentes_atrasadas as $venda) {

				$id_venda = $venda['id'];
				$remover->cancelarPendentesAtrasadas($id_venda);

			}

		}

	}

}

?>