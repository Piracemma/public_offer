<?php

namespace App\Controllers;

//os recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;
use App\Controllers\CarrinhoController;

class AppController extends Action {

    public function categorias() {

        $this->validaUsuario();

        $this->view->pagina = 'categorias';

        if(isset($_GET['categoria']) && !empty($_GET['categoria'])) {

            $categoria = htmlspecialchars($_GET['categoria'], ENT_QUOTES, 'UTF-8');
        
            $categorias = [
                'moda' => 'moda',
                'eletronicos' => 'eletronicos',
                'presente_papelaria' => 'presentes/papelaria',
                'cama_mesa_banho' => 'cama/mesa/banho'
            ];

            if(isset($categorias[$categoria])) {

                $categoria_pesquisa = $categorias[$categoria];

                //Paginacao

                $produtos = Container::getModel('Produto');

                //total de registros
                $total_registros = $produtos->totalListarCategoria($categoria_pesquisa);

                if(isset($_GET['page']) && !empty($_GET['page'])) {

                    $page = intval(htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'));

                } else {

                    $page = 1;

                }
                //LIMIT e OFFSET tem que jogar na pesquisa do banco de dados
                $limit = 12;
                $offset = ($page - 1) * $limit;

                $this->view->total_paginas = ceil($total_registros['contagem'] / $limit);
                $this->view->pagina_atual = $page;
                $this->view->range_paginas = 4;
                $this->view->categoria = $categorias[$categoria];

                $produtos_gerais = $produtos->buscarProdutoCategoria($categoria_pesquisa, $limit, $offset);

                $this->view->produtos = $produtos_gerais;

                //Paginacao

                $this->render('categoria');

            } else {

                header("Location: /categorias");

            }

        } else {

            $this->render('categorias');

        }

    }

    public function pesquisar() {

        $this->validaUsuario();

        if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])) {

            $pesquisa = htmlspecialchars($_GET['pesquisa'], ENT_QUOTES, 'UTF-8');

            if(!empty($pesquisa) && strlen($pesquisa) >= 3 ) {

                // Função para dividir uma string em palavras-chave
                function extractKeywords($query) {
                    $keywords = explode(" ", $query);
                    return $keywords;
                }

                // Função para gerar variações de palavras-chave
                function generateVariations($keywords) {
                    $variations = [];

                    foreach ($keywords as $keyword) {
                        $length = strlen($keyword);

                        if($length >= 4) {

                            for ($i = 4; $i <= $length; $i++) {
                                $variations[] = substr($keyword, 0, $i);
                            }

                        } else if($length < 4) {

                            for ($i = 3; $i <= $length; $i++) {
                                $variations[] = substr($keyword, 0, $i);
                            }

                        }
                    }

                    return array_unique($variations);
                }

                // Função para calcular a relevância de uma correspondência
                function calculateRelevance($resultName, $keywords) {
                $relevance = 0;

                foreach ($keywords as $keyword) {
                    if (stripos($resultName, $keyword) !== false) {
                        $relevance += strlen($keyword);
                    }
                }

                return $relevance;
                }

                // Função para ordenar os resultados por relevância
                function sortByRelevance($results, $keywords) {
                usort($results, function ($a, $b) use ($keywords) {
                    $relevanceA = calculateRelevance($a['nome'], $keywords);
                    $relevanceB = calculateRelevance($b['nome'], $keywords);

                    return $relevanceB - $relevanceA;
                });

                return $results;
                }

                // Pega o post e gera as palavras
                $keywords = extractKeywords($pesquisa);

                //cria as variantes
                $variations = generateVariations($keywords);

                $pesquisa_consulta = Container::getModel('Produto');

                // Constrói a consulta SQL
                $searchQuery = $pesquisa_consulta->pesquisa($variations);

                //ESSE VAI SER IMPRESSO NA TELA
                $sortedResults = sortByRelevance($searchQuery, $keywords);


                //PAGINAÇAO
                $page = 1;

                if(isset($_GET['page']) && !empty($_GET['page'])) {

                    $page = intval(htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'));
        
                } else {
        
                    $page = 1;
        
                }
                //LIMIT e OFFSET tem que jogar na pesquisa do banco de dados
                $limit = 12;
                $offset = ($page - 1) * $limit;
        
                $this->view->total_paginas = ceil(count($sortedResults) / $limit);
                $this->view->pagina_atual = $page;
                $this->view->range_paginas = 2;
                
                $pesquisa_paginada = array();
                
                $final = ($page * $limit);

                if($final > count($sortedResults)) {

                    $final = count($sortedResults);

                }

                for($i = $offset; $i < $final; $i++) {

                    $pesquisa_paginada[] = $sortedResults[$i];

                }
                //PAGINAÇAO
                

                $this->view->pesquisa = $pesquisa_paginada;                

                $this->view->nome_pesquisa = $pesquisa;

                $this->view->pagina = 'pesquisar';

                $this->render('pesquisar');

            } else {

                header("Location: /");
                die();
    
            }

        } else {

            header("Location: /");
            die();

        }

    }

    public function carrinho() {

        $this->validaUsuario();

        if(isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {

            CarrinhoController::excluirProdutosEstoqueInativo();

            CarrinhoController::somarTotaisVendedor();

        }

        $this->view->pagina = 'carrinho';

        $this->render('carrinho');

    }

    public function cupom() {

        $this->validaUsuario();

        if(isset($_POST['cupom']) && !empty($_POST['cupom'])) {

            if(isset($_GET['vendedor']) && !empty($_GET['vendedor'])) {

                $cupom = htmlspecialchars($_POST['cupom'], ENT_QUOTES, 'UTF-8');
                $id_vendedor = htmlspecialchars($_GET['vendedor'], ENT_QUOTES, 'UTF-8');

                $verifica_cupom = Container::getModel('Venda');

                $valida_cupom = $verifica_cupom->buscarCupom($cupom);

                if($valida_cupom != false) {

                    $id_cupom = $valida_cupom['id'];
                    $id_usuario = $_SESSION['id'];

                    $cupom_utilizado = $verifica_cupom->cupomUtilizado($id_usuario, $id_cupom);

                    if($cupom_utilizado == false) {

                        $data_cupom = $valida_cupom['validade'];
                    
                        $data_atual = new \DateTime();
                        
                        $data_cupom_obj = new \DateTime($data_cupom);
                        
                        if ($data_atual <= $data_cupom_obj) {
                            
                            if(isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {

                                foreach($_SESSION['carrinho'] as $key => $vendedor) {

                                    if(isset($vendedor['informacao']['cupom']) && $vendedor['informacao']['cupom']['id_cupom'] == $valida_cupom['id']) {

                                        header("Location: /carrinho?vendedor=$id_vendedor&act=cupom_ja_aplicado");
                                        die();

                                    }

                                }

                                foreach($_SESSION['carrinho'] as $key => $vendedor) {

                                    if($vendedor['informacao']['id_vendedor'] == $id_vendedor) {

                                        $_SESSION['carrinho'][$key]['informacao']['cupom'] = [
                                            'id_cupom' => $valida_cupom['id'],
                                            'cupom' => $valida_cupom['cupom'],
                                            'desconto' => $valida_cupom['desconto'],
                                            'validade' => $valida_cupom['validade']
                                        ];

                                    }
                                
                                }

                                header("Location: /carrinho");
                                die();

                            } else {

                                header("Location: /");
                                die();

                            }

                        } else {
                            
                            header("Location: /carrinho?vendedor=$id_vendedor&act=cupom_vencido");
                            die();

                        }

                    } else {

                        header("Location: /carrinho?vendedor=$id_vendedor&act=cupom_utilizado");
                        die();

                    }


                } else {

                    header("Location: /carrinho?vendedor=$id_vendedor&act=cupom_nao_encontrado");
                    die();

                }

            } else {

                header("Location: /carrinho");
                die();

            }

        } else {

            header("Location: /carrinho");
            die();

        }

    }

    public function removerCupom($id_vendedor) {

        $this->validaUsuario();

        if(isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {

            foreach($_SESSION['carrinho'] as $key => $vendedor) {

                if($vendedor['informacao']['id_vendedor'] == $id_vendedor) {

                    unset($_SESSION['carrinho'][$key]['informacao']['cupom']);

                }

            }

        }

    }

    public function excluirCupom() {

        $this->validaUsuario();
        
        if(isset($_GET['vendedor']) && !empty($_GET['vendedor'])) {

            $id = htmlspecialchars($_GET['vendedor'], ENT_QUOTES, 'UTF-8');

            $this->removerCupom($id);

        }

        header("Location: /carrinho");
        die();

    }

    public function perfil() {

        $this->validaUsuario();

        $this->removerPendentePrazo();

        //PAGINA 'MINHAS COMPRAS'

        $compra = Container::getModel('Usuario');

        //total de registros
		$total_registros = $compra->totalListarCompras($_SESSION['id']);

		//paginacao
		if(isset($_GET['page']) && !empty($_GET['page'])) {

			$page = intval(htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'));

		} else {

			$page = 1;

		}
        //LIMIT e OFFSET tem que jogar na pesquisa do banco de dados
		$limit = 5;
		$offset = ($page - 1) * $limit;

		$this->view->total_paginas = ceil($total_registros['contagem'] / $limit);
		$this->view->pagina_atual = $page;
		$this->view->range_paginas = 4;

        $compras_gerais = $compra->listarCompras($_SESSION['id'], $limit, $offset);

        $compras = array();

        foreach($compras_gerais as $compra_unitaria) {

            if(!empty($compras)) {

                $compra_existe = false;

                foreach($compras as $key => $compra) {

                    if($compra_unitaria['id'] == $compra['resumo']['id_venda']) {

                        $compra_existe = true;

                        $compras[$key]['itens'][] = array(
                            'id_produto' => $compra_unitaria['id_produto'],
                            'nome_produto' => $compra_unitaria['nome_produto'],
                            'preco' => $compra_unitaria['preco'],
                            'quantidade' => $compra_unitaria['quantidade'],
                            'total' => $compra_unitaria['total'],
                            'observacao_item' => $compra_unitaria['observacao_item'],
                        );

                    }

                }

                if(!$compra_existe) {

                    $endereco = $compra_unitaria['endereco'];
                    if(!empty($compra_unitaria['endereco_alternativo'])) {
                        $endereco = $compra_unitaria['endereco_alternativo'];
                    }

                    $date = date_create($compra_unitaria['data_venda']);
                    $data = date_format($date, 'd/m/y H:i');

                    // Defina as duas datas que você deseja comparar
                    $data1 = new \DateTime($compra_unitaria['data_venda']);
                    $data2 = new \DateTime('now');

                    // Calcule a diferença entre as duas datas
                    $diferenca = $data1->diff($data2);

                    // Acesse o número de dias da diferença
                    $dias = $diferenca->days;

                    $compras[] = array(
                        'resumo' => array(
                            'id_venda' => $compra_unitaria['id'],
                            'data_venda' => $data,
                            'diferenca_dias' => $dias,
                            'entrega_propria' => $compra_unitaria['entrega_propria'],
                            'valor_entrega' => $compra_unitaria['valor_entrega'],
                            'total_produtos' => $compra_unitaria['total_produtos'],
                            'total_compra' => $compra_unitaria['total_compra'],
                            'vendedor' => $compra_unitaria['nome_vendedor'],
                            'telefone_vendedor' => $compra_unitaria['telefone'],
                            'endereco_vendedor' => $compra_unitaria['endereco_vendedor'],
                            'finalizadora' => $compra_unitaria['finalizadora'],
                            'observacao_compra' => $compra_unitaria['observacao_compra'],
                            'endereco' => $endereco,
                            'status' => $compra_unitaria['aprovado'],
                            'usuario' => $_SESSION['id'],
                            'cupom' => $compra_unitaria['cupom'],
                            'valor_desconto' => $compra_unitaria['valor_desconto']
                        ),
                        'itens' => array(
                            array(
                                'id_produto' => $compra_unitaria['id_produto'],
                                'nome_produto' => $compra_unitaria['nome_produto'],
                                'preco' => $compra_unitaria['preco'],
                                'quantidade' => $compra_unitaria['quantidade'],
                                'total' => $compra_unitaria['total'],
                                'observacao_item' => $compra_unitaria['observacao_item'],
                            ),
                        )
                    );

                }

            } else {

                $endereco = $compra_unitaria['endereco'];
                if(!empty($compra_unitaria['endereco_alternativo'])) {
                    $endereco = $compra_unitaria['endereco_alternativo'];
                }

                $date = date_create($compra_unitaria['data_venda']);
                $data = date_format($date, 'd/m/y H:i');

                // Defina as duas datas que você deseja comparar
                $data1 = new \DateTime($compra_unitaria['data_venda']);
                $data2 = new \DateTime('now');

                // Calcule a diferença entre as duas datas
                $diferenca = $data1->diff($data2);

                // Acesse o número de dias da diferença
                $dias = $diferenca->days;

                $compras[] = array(
                    'resumo' => array(
                        'id_venda' => $compra_unitaria['id'],
                        'data_venda' => $data,
                        'diferenca_dias' => $dias,
                        'entrega_propria' => $compra_unitaria['entrega_propria'],
                        'valor_entrega' => $compra_unitaria['valor_entrega'],
                        'total_produtos' => $compra_unitaria['total_produtos'],
                        'total_compra' => $compra_unitaria['total_compra'],
                        'vendedor' => $compra_unitaria['nome_vendedor'],
                        'telefone_vendedor' => $compra_unitaria['telefone'],
                        'endereco_vendedor' => $compra_unitaria['endereco_vendedor'],
                        'finalizadora' => $compra_unitaria['finalizadora'],
                        'observacao_compra' => $compra_unitaria['observacao_compra'],
                        'endereco' => $endereco,
                        'status' => $compra_unitaria['aprovado'],
                        'usuario' => $_SESSION['id'],
                        'cupom' => $compra_unitaria['cupom'],
                        'valor_desconto' => $compra_unitaria['valor_desconto']
                    ),
                    'itens' => array(
                        array(
                            'id_produto' => $compra_unitaria['id_produto'],
                            'nome_produto' => $compra_unitaria['nome_produto'],
                            'preco' => $compra_unitaria['preco'],
                            'quantidade' => $compra_unitaria['quantidade'],
                            'total' => $compra_unitaria['total'],
                            'observacao_item' => $compra_unitaria['observacao_item'],
                        ),
                    )
                );

            }

        }

        $this->view->compras = $compras;

        //FIM PAGINA 'MINHAS COMPRAS'
        //------------------
        //PAGINA 'EDITAR PERFIL'
        
        $id_usuario = $_SESSION['id'];

		$usuario = Container::getModel('Usuario');

		$dados_usuario = $usuario->usuario($id_usuario);

		$this->view->usuario = [
			'nome' => $dados_usuario['nome'],
			'email' => $dados_usuario['email'],
			'documento' => $dados_usuario['documento'],
			'cidade' => $dados_usuario['cidade'],
			'endereco' => $dados_usuario['endereco'],
			'bairro' => $dados_usuario['bairro'],
            'referencia' => $dados_usuario['referencia'],
			'telefone' => $dados_usuario['telefone'],
		];

        //FIM PAGINA 'EDITAR PERFIL'

        $this->view->pagina = 'perfil';

        $this->render('perfil');

    }

    public function atualizarPerfil() {

		$this->validaUsuario();

		$nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
		$documento = htmlspecialchars($_POST['documento'], ENT_QUOTES, 'UTF-8');
		$cidade = htmlspecialchars($_POST['cidade'], ENT_QUOTES, 'UTF-8');
		$endereco = htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8');
		$bairro = htmlspecialchars($_POST['bairro'], ENT_QUOTES, 'UTF-8');
		$telefone = htmlspecialchars($_POST['telefone'], ENT_QUOTES, 'UTF-8');
		$referencia = htmlspecialchars($_POST['referencia'], ENT_QUOTES, 'UTF-8');

		$usuario = Container::getModel('Usuario');

		$id_usuario = $_SESSION['id'];


			$usuario->__set('nome', $nome)->__set('documento', $documento)->__set('cidade', $cidade)->__set('endereco', $endereco)->__set('bairro', $bairro)->__set('telefone', $telefone)->__set('end_referencia', $referencia);

			if($usuario->validarAtualizarCadastro()) {

				if($usuario->validarCidade()) {

					$usuario->atualizar($id_usuario);

					header("Location: /perfil?pagina=editar_perfil&act=cadastro_atualizado");

				} else {
					header("Location: /perfil?pagina=editar_perfil&act=erro_atualizar");
				}
				
			} else {
				header("Location: /perfil?pagina=editar_perfil&act=erro_atualizar");
			}

	}

    public function produto($id,$produto_nome) {

        $this->validaUsuario();

        $produto = Container::getModel('Produto');
        
        if($produto->buscarProduto($id,$produto_nome) == false) {

            header("Location: /?act=nao_encontrado");
            die();

        } else {

            $produto_info = $produto->buscarProduto($id,$produto_nome);

            if($produto->produtosVendedor($produto_info['id_vendedor'],$id) != false) {

                $this->view->horario_funcionamento = $this->horarioFuncionamento($produto_info['id_vendedor']);

                $vendedor_produtos = $produto->produtosVendedor($produto_info['id_vendedor'],$id);
                $this->view->vendedor_produtos = $vendedor_produtos;

            }          

            $this->view->produto = $produto_info;
            $explode = explode(' ',$produto_info['nome']);
            $implode = implode('_',$explode);
            $this->view->produto_link = $implode;
            $this->view->id = $id;
            $this->view->id_vendedor = $produto_info['id_vendedor'];

            $this->view->pagina = 'produto';

            $this->render('produto');

        }        

    }

    public function produtoImg() {

        $id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');

        $produto = Container::getModel('Produto');

        if($produto->buscarProdutoId($id) != false) {

            $produto_img = $produto->buscarProdutoId($id);

            $caminho = array();

            if(!empty($produto_img['imagem1'])) {
                $caminho[] = HOST_APLIC.$produto_img['imagem1'];
            }
            if(!empty($produto_img['imagem2'])) {
                $caminho[] = HOST_APLIC.$produto_img['imagem2'];
            }
            if(!empty($produto_img['imagem3'])) {
                $caminho[] = HOST_APLIC.$produto_img['imagem3'];
            }

            $resposta = array('caminho' => $caminho);

            header('Content-Type: application/json');

            echo json_encode($resposta); 
            
            exit();

        }
        
    
    }

    public function confirmarDados() {

        $this->validaUsuario();

        $this->view->pagina = 'carrinho';

        if(isset($_GET['vendedor']) && !empty($_GET['vendedor'])) {

            $id_vendedor = htmlspecialchars($_GET['vendedor'], ENT_QUOTES, 'UTF-8');

            $info_usuario = Container::getModel('Usuario');

            $id = $_SESSION['id'];

            $endereco = $info_usuario->enderecoUsuario($id);

            $this->view->endereco = $endereco['endereco'];

            $this->view->vendedor = $id_vendedor;

            $this->render('confirmar_dados');

        } else {

            header('Location: /carrinho?act=selecione_vendedor');
			die();
            
        }        

    }

    public function novosProdutos() {

        $this->validaUsuario();

        //Paginacao

        $produtos = Container::getModel('Produto');

        //total de registros
        $total_registros = $produtos->totalNovosProdutos();

        if(isset($_GET['page']) && !empty($_GET['page'])) {

            $page = intval(htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'));

        } else {

            $page = 1;

        }
        //LIMIT e OFFSET tem que jogar na pesquisa do banco de dados
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $this->view->total_paginas = ceil($total_registros['contagem'] / $limit);
        $this->view->pagina_atual = $page;
        $this->view->range_paginas = 4;

        $produtos_gerais = $produtos->buscarNovosProdutos($limit, $offset);

        $this->view->produtos = $produtos_gerais;

        //Paginacao

        $this->render('novos_produtos');

    }

    public function produtosDestaques() {

        $this->validaUsuario();

        //Paginacao

        $produtos = Container::getModel('Produto');

        //total de registros
        $total_registros = $produtos->totalProdutosDestaques();

        if(isset($_GET['page']) && !empty($_GET['page'])) {

            $page = intval(htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'));

        } else {

            $page = 1;

        }
        //LIMIT e OFFSET tem que jogar na pesquisa do banco de dados
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $this->view->total_paginas = ceil($total_registros['contagem'] / $limit);
        $this->view->pagina_atual = $page;
        $this->view->range_paginas = 4;

        $produtos_gerais = $produtos->buscarProdutosDestaques($limit, $offset);

        $this->view->produtos = $produtos_gerais;

        //Paginacao

        $this->render('produtos_destaques');

    }

    public function vendedores() {

        $this->validaUsuario();

        $id_cidade = $_SESSION['id_cidade'];

        $vendedores = Container::getModel('Vendedor');

        //total de registros
        $recuperar_totais = $vendedores->totalVendedoresCidade($id_cidade);
        $total_registros = count($recuperar_totais);

        if(isset($_GET['page']) && !empty($_GET['page'])) {

            $page = intval(htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'));

        } else {

            $page = 1;

        }
        //LIMIT e OFFSET tem que jogar na pesquisa do banco de dados
        $limit = 9;
        $offset = ($page - 1) * $limit;

        $this->view->total_paginas = ceil($total_registros / $limit);
        $this->view->pagina_atual = $page;
        $this->view->range_paginas = 4;

        if(!isset($_GET['pagina'])) {

            $dados_vendedores = $vendedores->listarVendedoresCidade($id_cidade, 'alfabetica', $limit, $offset);

        } else if(isset($_GET['pagina']) && $_GET['pagina'] == 'mais_vendas') {

            $dados_vendedores = $vendedores->listarVendedoresCidade($id_cidade, 'mais_vendas', $limit, $offset);

        }

        $this->view->vendedores = $dados_vendedores;

        $this->render('vendedores');

    }

    public function produtosVendedor() {

        $this->validaUsuario();

        if(isset($_GET['vendedor']) && !empty($_GET['vendedor'])) {

            $id_vendedor = htmlspecialchars($_GET['vendedor'], ENT_QUOTES, 'UTF-8');

            $id_cidade = $_SESSION['id_cidade'];

            $produtos = Container::getModel('Produto');

            //total de registros
            $recuperar_totais = $produtos->totaisProdutosVendedor($id_vendedor, $id_cidade);

            if($recuperar_totais == false) {

                header("Location: /vendedores");
                die();

            }

            $total_registros = count($recuperar_totais);

            if(isset($_GET['page']) && !empty($_GET['page'])) {

                $page = intval(htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8'));

            } else {

                $page = 1;

            }

            //LIMIT e OFFSET tem que jogar na pesquisa do banco de dados
            $limit = 9;
            $offset = ($page - 1) * $limit;

            $this->view->total_paginas = ceil($total_registros / $limit);
            $this->view->pagina_atual = $page;
            $this->view->range_paginas = 4;

            $vendedor = Container::getModel('Vendedor');
            $dados_vendedor = $vendedor->vendedor($id_vendedor);
            $this->view->vendedor = $dados_vendedor;

            $this->view->horario_funcionamento = $this->horarioFuncionamento($id_vendedor);

            $dados_produto = $produtos->buscarProdutosVendedor($id_vendedor, $id_cidade, $limit, $offset);

            $this->view->produtos = $dados_produto;

            $this->render('produtos_vendedor');

        } else {

            header("Location: /vendedores");
            die();

        }

        

    }

    public function devolucao() {

        $this->validaUsuario();

        if(isset($_GET['venda']) && isset($_GET['usuario'])) {

            $id_venda = htmlspecialchars($_GET['venda'], ENT_QUOTES, 'UTF-8');
            $usuario = htmlspecialchars($_GET['usuario'], ENT_QUOTES, 'UTF-8');
            $id_usuario = $_SESSION['id'];
            
            if($usuario == $id_usuario) {

                $venda = Container::getModel('Venda');

                $venda_devolucao = $venda->devolucaoVenda($id_venda, $id_usuario);

                if($venda_devolucao == false) {

                    header("Location: /perfil");
                    die();

                } else {       
                    
                    // Defina as duas datas que você deseja comparar
                    $data1 = new \DateTime($venda_devolucao[0]['data_venda']);
                    $data2 = new \DateTime('now');

                    // Calcule a diferença entre as duas datas
                    $diferenca = $data1->diff($data2);

                    // Acesse o número de dias da diferença
                    $dias = $diferenca->days;

                    if($dias <= 7) {

                        $this->view->dados = $venda_devolucao;

                        $this->render('devolucao', 'layout');  

                    } else {

                        header("Location: /perfil");
                        die();

                    }                       

                }  

            } else {                

                header("Location: /perfil");
                die();    

            }  

                 

        } else {

            header("Location: /perfil");
            die();

        }

    }

    public function solicitarDevolucao() {

        $this->validaUsuario();

        if(isset($_GET['venda']) && isset($_GET['usuario']) && isset($_POST['motivo'])) {

            $motivos = ['Produto com Defeito', 'Não serviu', 'Não corresponde as expectativas', 'Outro'];

            $motivo = htmlspecialchars($_POST['motivo'], ENT_QUOTES, 'UTF-8');

            $confere_motivo = false;

            foreach($motivos as $moti) {
                if($motivo == $moti) {
                    $confere_motivo = true;
                }
            }

            if($confere_motivo) {

                $id_venda = htmlspecialchars($_GET['venda'], ENT_QUOTES, 'UTF-8');
                $id_usuario = htmlspecialchars($_GET['usuario'], ENT_QUOTES, 'UTF-8');

                $venda = Container::getModel('Venda');
                $venda_devolucao = $venda->devolucaoVenda($id_venda, $id_usuario);

                if($venda_devolucao == false) {
                    header("Location: /perfil");
                    die();
                } else {

                    // Defina as duas datas que você deseja comparar
                    $data1 = new \DateTime($venda_devolucao[0]['data_venda']);
                    $data2 = new \DateTime('now');

                    // Calcule a diferença entre as duas datas
                    $diferenca = $data1->diff($data2);

                    // Acesse o número de dias da diferença
                    $dias = $diferenca->days;

                    if($dias <= 7) {
                    
                        if(isset($_POST['comentario']) && !empty($_POST['comentario'])) {

                            $motivo .= ' - '.htmlspecialchars($_POST['comentario'], ENT_QUOTES, 'UTF-8');
                            
                        }

                        $data = date('Y-m-d H:i:s');

                        $data_venda_original = 'Data da Venda: '.$venda_devolucao[0]['data_venda'];

                        $devolucao = $venda->devolverVenda($id_venda, $data, $motivo, $data_venda_original);

                        $email_vendedor = $venda->emailVendedor($id_venda);

                        if($devolucao) {
                            
                            header("Location: /perfil?act=devolvido");
                            die();
                        } else {
                            header("Location: /perfil?act=erro_devolvido");
                            die();
                        }
                        
                    } else {

                        header("Location: /perfil");
                        die();

                    }

                }

            } else {
                header("Location: /perfil");
                die();    
            }

        } else {
            header("Location: /perfil");
            die();
        }

    }

    public function removerPendentePrazo() {

		$id_usuario = $_SESSION['id'];

		$remover = Container::getModel('Venda');

		$pendentes_atrasadas = $remover->pendentesAtrasadasUsuario($id_usuario);

		if($pendentes_atrasadas != false) {

			foreach($pendentes_atrasadas as $venda) {

				$id_venda = $venda['id'];
				$remover->cancelarPendentesAtrasadas($id_venda);

			}

		}

	}
    
}
?>
