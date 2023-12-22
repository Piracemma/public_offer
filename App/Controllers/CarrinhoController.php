<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use App\Controllers\AppController;

class CarrinhoController extends Action {

    public function adicionarCarrinho() {

        $this->validaUsuario();

        if(isset($_GET) && isset($_GET['produto']) && !empty($_GET['produto']) && isset($_GET['nome']) && !empty($_GET['nome']) && isset($_GET['id_vendedor']) && !empty($_GET['id_vendedor'])) {

            $id = htmlspecialchars($_GET['produto'], ENT_QUOTES, 'UTF-8');
            $urldecode = urldecode($_GET['nome']);
            $nome = htmlspecialchars($urldecode, ENT_QUOTES, 'UTF-8');
            $id_vendedor = htmlspecialchars($_GET['id_vendedor'], ENT_QUOTES, 'UTF-8');

        } else {

            header("Location: /?act=selecione_produto");

        }

        $header = "Location: /produtos_vendedor?vendedor=$id_vendedor";

        $this->adicionarProdutoCarrinho($header);

    }

    public function finalizarCompra() {

        $this->validaUsuario();

        $header = "Location: /carrinho";

        $this->adicionarProdutoCarrinho($header);

    }

    public function finalizar() {

        $this->validaUsuario();

        if(isset($_GET) && isset($_GET['vendedor']) && !empty($_GET['vendedor'])) {

            $finalizadora = 0;
            $endereco = null;
            $observacao = null;

            if(isset($_POST['finalizadora']) && !empty($_POST['finalizadora'])) {

                if(isset($_POST['endereco']) && !empty($_POST['endereco'])) {

                    $end = htmlspecialchars($_POST['endereco'], ENT_QUOTES, 'UTF-8');

                    $endereco = $end;

                }

                if(isset($_POST['observacao']) && !empty($_POST['observacao'])) {

                    $observ = htmlspecialchars($_POST['observacao'], ENT_QUOTES, 'UTF-8');

                    $observacao = $observ;

                }

                $valida_finalizadora = Container::getModel('Produto');

                $post = htmlspecialchars($_POST['finalizadora'], ENT_QUOTES, 'UTF-8');
                
                $retorno = $valida_finalizadora->validaFinalizadora($post);

                if($retorno != false) {

                    $finalizadora = (int) $post;

                } else {

                    header('Location: /confirma_dados?act=selecione_finaliazdora');
                    die();

                }                

            }

            $id_vendedor = htmlspecialchars($_GET['vendedor'], ENT_QUOTES, 'UTF-8');

            CarrinhoController::somarTotaisVendedor();

            $dados_compra = array(
                'id_usuario' => $_SESSION['id'],
                'id_vendedor' => '',
                'entrega_propria' => '',
                'valor_entrega' => 0,
                'soma_produtos' => 0,
                'total' => 0,
                'comissao' => 0,
                'finalizadora' => $finalizadora,
                'endereco' => $endereco,
                'observacao' => $observacao,
                'cupom' => array(),
                'produtos' => array()
            );

            foreach($_SESSION['carrinho'] as $key => $vendedor) {

                if($vendedor['informacao']['id_vendedor'] == $id_vendedor) {

                    $desconto = 0;

                    if(isset($vendedor['informacao']['cupom']) && !empty($vendedor['informacao']['cupom'])) {

                        $desconto = $vendedor['informacao']['cupom']['valor_desconto'];

                        $dados_compra['cupom'] = [
                            'id_cupom' => $vendedor['informacao']['cupom']['id_cupom'],
                            'cupom' => $vendedor['informacao']['cupom']['cupom'],
                            'desconto' => $vendedor['informacao']['cupom']['desconto'],
                            'validade' => $vendedor['informacao']['cupom']['validade'],
                            'valor_desconto' => $vendedor['informacao']['cupom']['valor_desconto']
                        ];

                    }

                    foreach($vendedor['produtos'] as $key2 => $produto) {

                        $produto_info = Container::getModel('Produto');

                        $ativo_estoque = $produto_info->verificaAtivoEstoque($produto['id_produto']);

                        $quantidade = $produto['quantidade'];

                        if(isset($ativo_estoque) && !empty($ativo_estoque)) {

                            if($ativo_estoque['ativo']) {

                                if($ativo_estoque['estoque'] < $quantidade) {

                                    unset($_SESSION['carrinho'][$key]['produtos'][$key2]);
                                    header('Location: /carrinho?act=estoque_insuficiente'); 
                                    die();

                                }

                            } else {

                                unset($_SESSION['carrinho'][$key]['produtos'][$key2]);
                                header('Location: /carrinho?act=produto_excluido');
                                die();

                            }

                        } else {

                            header('Location: /carrinho?act=produto_invalido');
                            die();

                        }

                        $dados_produto = $produto_info->buscarProdutoId($produto['id_produto']);

                        $dados_compra['produtos'][] = array(
                            'id_produto' => $dados_produto['id'],
                            'nome' => $dados_produto['nome'],
                            'preco' => $dados_produto['preco'],
                            'quantidade' => $produto['quantidade'],
                            'total' => $dados_produto['preco'] * $produto['quantidade'],
                            'observacao' => $produto['observacao']
                        );

                    }

                    $dados_compra['id_vendedor'] = $vendedor['informacao']['id_vendedor'];
                    $dados_compra['entrega_propria'] = $vendedor['informacao']['entrega_propria'];
                    $dados_compra['valor_entrega'] = $vendedor['informacao']['valor_entrega'];

                    foreach($dados_compra['produtos'] as $produto) {
        
                        $dados_compra['soma_produtos'] += $produto['total'];
        
                    }
        
                    $dados_compra['total'] = $dados_compra['soma_produtos'] + $dados_compra['valor_entrega'] - $desconto;
                    $dados_compra['comissao'] = ($dados_compra['soma_produtos']*COMISSAO) - $desconto;

                }

            }

            if($dados_compra['id_vendedor'] == '' || empty($dados_compra['id_vendedor'])) {

                header('Location: /carrinho?act=vendedor_invalido');
                die();

            }

            $venda = Container::getModel('Produto');
           
            if(isset($dados_compra['cupom']) && !empty($dados_compra['cupom']['id_cupom'])) {

                $finalizar = $venda->finalizarVenda($dados_compra['id_usuario'], $dados_compra['id_vendedor'], $dados_compra['entrega_propria'], $dados_compra['valor_entrega'], $dados_compra['soma_produtos'], $dados_compra['total'], $dados_compra['finalizadora'], $dados_compra['endereco'], $dados_compra['observacao'], date("Y-m-d H:i:s"), $dados_compra['comissao'], 1, $dados_compra['cupom']['id_cupom'], $dados_compra['cupom']['valor_desconto']);

            } else {

                $finalizar = $venda->finalizarVenda($dados_compra['id_usuario'], $dados_compra['id_vendedor'], $dados_compra['entrega_propria'], $dados_compra['valor_entrega'], $dados_compra['soma_produtos'], $dados_compra['total'], $dados_compra['finalizadora'], $dados_compra['endereco'], $dados_compra['observacao'], date("Y-m-d H:i:s"), $dados_compra['comissao'], 0);

            }

            if(isset($finalizar['fetch']) && $finalizar['fetch']) {

                if(isset($finalizar['id_venda']) && !empty($finalizar['id_venda'])) {

                    $id_venda = $finalizar['id_venda'];

                    foreach($dados_compra['produtos'] as $produto) {

                        $venda->finalizarVendaItem($id_venda, $produto['id_produto'], $produto['nome'], $produto['preco'], $produto['quantidade'], $produto['total'], $produto['observacao']);

                        $venda->atualizarEstoque($produto['id_produto'], $produto['quantidade']);

                    }

                    $venda->atalizarVendasCompras($dados_compra['id_usuario'], $dados_compra['id_vendedor']);

                    foreach ($_SESSION['carrinho'] as $key => $vendedor) {

                        if ($vendedor['informacao']['id_vendedor'] == $dados_compra['id_vendedor']) {

                            unset($_SESSION['carrinho'][$key]);

                            break;

                        }

                    }

                    $_SESSION['venda_pendente'][] = array(
                        'id_venda' => $id_venda,
                        'status' => 'pendente',
                        'exibido' => false
                    ); 
                    
                    header('Location: /perfil');
                    die();

                }

            } else {

                header('Location: /carrinho?act=erro_finalizar');
                die();

            }


        } else {

            header('Location: /carrinho?act=vendedor_invalido');
            die();

        }

    }

    public function removerProduto() {

        $this->validaUsuario();

        if(isset($_GET) && isset($_GET['id_produto']) && !empty($_GET['id_produto'])) {

            $id = htmlspecialchars($_GET['id_produto'], ENT_QUOTES, 'UTF-8');
            $id_produto = (int) $id;

            foreach($_SESSION['carrinho'] as $key => $vendedor) {

                foreach($vendedor['produtos'] as $key2 => $produto) {
    
                    if($produto['id_produto'] == $id_produto) {
    
                        unset($_SESSION['carrinho'][$key]['produtos'][$key2]);
    
                    }
    
                }
    
            }

            header("Location: /carrinho?act=produto_removido");

        } else {

            header("Location: /carrinho?act=produto_nao_encontrado");

        }

    }

    public static function somarTotaisVendedor() {

        foreach($_SESSION['carrinho'] as $key => $vendedor) {
        
            $_SESSION['carrinho'][$key]['informacao']['total'] = 0;
            $_SESSION['carrinho'][$key]['informacao']['soma_produtos'] = 0;

            foreach($vendedor['produtos'] as $produto) {

                $pesquisa_produto = Container::getModel('Produto');

                $preco = $pesquisa_produto->buscarProdutoId($produto['id_produto']);

                $valor = $preco['preco'] * $produto['quantidade'];

                $_SESSION['carrinho'][$key]['informacao']['soma_produtos'] += $valor;

            }

            if(isset($_SESSION['carrinho'][$key]['informacao']['cupom'])) {

                $data_cupom = $_SESSION['carrinho'][$key]['informacao']['cupom']['validade'];
                    
                $data_atual = new \DateTime();
                
                $data_cupom_obj = new \DateTime($data_cupom);
                
                if ($data_atual <= $data_cupom_obj) {

                    $desconto = $_SESSION['carrinho'][$key]['informacao']['soma_produtos'] * $_SESSION['carrinho'][$key]['informacao']['cupom']['desconto'];

                    if($desconto > 10) {

                        $desconto = 10;

                    }

                    $_SESSION['carrinho'][$key]['informacao']['total'] = $_SESSION['carrinho'][$key]['informacao']['soma_produtos'] + $_SESSION['carrinho'][$key]['informacao']['valor_entrega'] - $desconto;

                    $_SESSION['carrinho'][$key]['informacao']['cupom']['valor_desconto'] = $desconto;

                } else {

                    $_SESSION['carrinho'][$key]['informacao']['total'] = $_SESSION['carrinho'][$key]['informacao']['soma_produtos'] + $_SESSION['carrinho'][$key]['informacao']['valor_entrega'];

                    $remover_cupom = new AppController;
                    $remover_cupom->removerCupom($_SESSION['carrinho'][$key]['informacao']['id_vendedor']);
                    header("Location: /carrinho");

                }

            } else {

                $_SESSION['carrinho'][$key]['informacao']['total'] = $_SESSION['carrinho'][$key]['informacao']['soma_produtos'] + $_SESSION['carrinho'][$key]['informacao']['valor_entrega'];

            }            

        }

    }

    public static function excluirProdutosEstoqueInativo() {

        $carrinho = Container::getModel('Produto');

        foreach($_SESSION['carrinho'] as $key => $vendedor) {

            foreach($vendedor['produtos'] as $key2 => $produto) {

                $dados_produto = $carrinho->verificaAtivoEstoque($produto['id_produto']);

                if(!$dados_produto['ativo'] || $dados_produto['estoque'] == 0 || $produto['quantidade'] > $dados_produto['estoque']) {

                    unset($_SESSION['carrinho'][$key]['produtos'][$key2]);

                }

            }

        }

        foreach($_SESSION['carrinho'] as $key => $vendedor) {

            if($vendedor['produtos'] == '' || empty($vendedor['produtos'])) {

                unset($_SESSION['carrinho'][$key]);

            }

        }

    }

    public function adicionarProdutoCarrinho($header) {

        if(isset($_GET) && isset($_GET['produto']) && !empty($_GET['produto']) && isset($_GET['nome']) && !empty($_GET['nome'])) {

            $id = htmlspecialchars($_GET['produto'], ENT_QUOTES, 'UTF-8');
            $urldecode = urldecode($_GET['nome']);
            $nome = htmlspecialchars($urldecode, ENT_QUOTES, 'UTF-8');
            $explode = explode('_',$nome);
            $produto_nome = implode(' ',$explode);
            $observacao = '';
            if(isset($_GET['observacao']) && !empty($_GET['observacao'])) {

                $urlobservacao = urldecode($_GET['observacao']);
                $observacao = htmlspecialchars($urlobservacao, ENT_QUOTES, 'UTF-8');

            }

            $produto = Container::getModel('Produto');

            $produto_info = $produto->buscarProduto($id,$produto_nome);

            if($produto_info != false) {                

                if(isset($_SESSION['carrinho']) && isset($_SESSION['carrinho'][0])) {

                    CarrinhoController::excluirProdutosEstoqueInativo();

                    $valida_vendedor = false;
        
                    foreach($_SESSION['carrinho'] as $key => $vendedor) {
        
                        if($vendedor['informacao']['id_vendedor'] == $produto_info['id_vendedor']) {
        
                            $valida_vendedor = true;

                            $produto_existe = false;
        
                            foreach($vendedor['produtos'] as $key2 => $produto) {
        
                                if($produto['id_produto'] == $produto_info['id']) {
        
                                    $produto_existe = true;
                                    $_SESSION['carrinho'][$key]['produtos'][$key2]['quantidade'] += 1;
                                    
                                    if(!empty($observacao)) {

                                        $_SESSION['carrinho'][$key]['produtos'][$key2]['observacao'] = $_SESSION['carrinho'][$key]['produtos'][$key2]['observacao'].'-'.$observacao;

                                    }
        
                                }
        
                            }
                            
                            if(!$produto_existe) {
        
                                $_SESSION['carrinho'][$key]['produtos'][] = array(
        
                                    'id_produto' => $produto_info['id'],
                                    'quantidade' => 1,
                                    'observacao' => $observacao,
        
                                );
        
                            }
        
                        }
        
                    }
        
                    if(!$valida_vendedor) {
        
                        $valor_entrega = FRETE_CASSIA;
        
                        if($produto_info['entrega_propria']) {
        
                            $valor_entrega = $produto_info['valor_entrega'];
        
                        }
        
                        $_SESSION['carrinho'][] = array(
        
                            'informacao' => array(
        
                                'id_vendedor' => $produto_info['id_vendedor'],
                                'nome_vendedor' => $produto_info['nome_vendedor'],
                                'entrega_propria' => $produto_info['entrega_propria'],
                                'valor_entrega' => $valor_entrega,
                                'soma_produtos' => 0,
                                'total' => 0
        
                            ),
                            'produtos' => array(
                                array(
        
                                    'id_produto' => $produto_info['id'],
                                    'quantidade' => 1,
                                    'observacao' => $observacao,
        
                                )
                            )
        
                        );
        
                    }
        
                    CarrinhoController::somarTotaisVendedor();
        
                } else {
        
                    $valor_entrega = FRETE_CASSIA;
        
                    if($produto_info['entrega_propria']) {
        
                        $valor_entrega = $produto_info['valor_entrega'];
        
                    }
        
                    $_SESSION['carrinho'][] = array(
        
                        'informacao' => array(
        
                            'id_vendedor' => $produto_info['id_vendedor'],
                            'nome_vendedor' => $produto_info['nome_vendedor'],
                            'entrega_propria' => $produto_info['entrega_propria'],
                            'valor_entrega' => $valor_entrega,
                            'soma_produtos' => 0,
                            'total' => 0
        
                        ),
                        'produtos' => array(
                            array(
        
                                'id_produto' => $produto_info['id'],
                                'quantidade' => 1,
                                'observacao' => $observacao,
                            )
                        )
        
                    );
        
                    CarrinhoController::somarTotaisVendedor();
        
                }
                
                header($header);

            } else {

                header("Location: /?act=produto_nao_encontrado");

            }

        } else {

            header("Location: /?act=selecione_produto");

        }

    }

    public function validaUsuario() {

		$this->validaAutenticacao();

		if(!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 1) {

			header('Location: /');
			die();

		}

	}

}

?>