<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		$routes['home'] = array(
			'route' => '/',
			'controller' => 'indexController',
			'action' => 'index'
		);

		$routes['politica_de_privacidade'] = array(
			'route' => '/politica_de_privacidade',
			'controller' => 'indexController',
			'action' => 'politicaPrivacidade'
		);
		
		$routes['termos_e_condicoes'] = array(
			'route' => '/termos_e_condicoes',
			'controller' => 'indexController',
			'action' => 'termosCondicoes'
		);

		$routes['sair'] = array(
			'route' => '/sair',
			'controller' => 'AuthController',
			'action' => 'sair'
		);

		$routes['autenticar'] = array(
			'route' => '/autenticar',
			'controller' => 'AuthController',
			'action' => 'autenticar'
		);

		$routes['esqueci_senha'] = array(
			'route' => '/esqueci_senha',
			'controller' => 'IndexController',
			'action' => 'esqueciSenha'
		);

		$routes['recuperar_senha'] = array(
			'route' => '/recuperar_senha',
			'controller' => 'IndexController',
			'action' => 'recuperarSenha'
		);

		$routes['codigo_verificador'] = array(
			'route' => '/codigo_verificador',
			'controller' => 'IndexController',
			'action' => 'codigoVerificador'
		);

		$routes['reenviar_email'] = array(
			'route' => '/reenviar_email',
			'controller' => 'IndexController',
			'action' => 'reenviarEmail'
		);

		$routes['nova_senha'] = array(
			'route' => '/nova_senha',
			'controller' => 'IndexController',
			'action' => 'novaSenha'
		);

		$routes['cadastre_se'] = array(
			'route' => '/cadastre_se',
			'controller' => 'indexController',
			'action' => 'cadastreSe'
		);

		$routes['registrar_usuario'] = array(
			'route' => '/registrar_usuario',
			'controller' => 'indexController',
			'action' => 'registrarUsuario'
		);

		$routes['registrar_vendedor'] = array(
			'route' => '/registrar_vendedor',
			'controller' => 'indexController',
			'action' => 'registrarVendedor'
		);

		$routes['login'] = array(
			'route' => '/login',
			'controller' => 'indexController',
			'action' => 'login'
		);

		//Vendedor

		$routes['vendedor'] = array(
			'route' => '/vendedor',
			'controller' => 'VendedorController',
			'action' => 'vendedor'
		);

		$routes['aprovar_venda'] = array(
			'route' => '/aprovar_venda',
			'controller' => 'VendedorController',
			'action' => 'aprovarVenda'
		);

		$routes['recusar_venda'] = array(
			'route' => '/recusar_venda',
			'controller' => 'VendedorController',
			'action' => 'recusarVenda'
		);

		$routes['api_vendas'] = array(
			'route' => '/api_vendas',
			'controller' => 'VendedorController',
			'action' => 'apiVendas'
		);

		$routes['api_venda_pendente'] = array(
			'route' => '/api_venda_pendente',
			'controller' => 'VendedorController',
			'action' => 'apiVendaPendente'
		);

		$routes['relatorio'] = array(
			'route' => '/relatorio',
			'controller' => 'VendedorController',
			'action' => 'relatorio'
		);

		$routes['relatorios-vendedor'] = array(
			'route' => '/relatorios-vendedor',
			'controller' => 'VendedorController',
			'action' => 'relatorioVendedor'
		);

		$routes['cadastro_produto'] = array(
			'route' => '/cadastro_produto',
			'controller' => 'VendedorController',
			'action' => 'cadastroProduto'
		);
		
		$routes['cadastrar_produto'] = array(
			'route' => '/cadastrar_produto',
			'controller' => 'VendedorController',
			'action' => 'cadastrarProduto'
		);

		$routes['meus_produtos'] = array(
			'route' => '/meus_produtos',
			'controller' => 'VendedorController',
			'action' => 'meusProdutos'
		);

		$routes['editar_produto'] = array(
			'route' => '/editar_produto',
			'controller' => 'VendedorController',
			'action' => 'editarProduto'
		);

		$routes['atualizar_produto'] = array(
			'route' => '/atualizar_produto',
			'controller' => 'VendedorController',
			'action' => 'atualizarProduto'
		);

		$routes['excluir_produto'] = array(
			'route' => '/excluir_produto',
			'controller' => 'VendedorController',
			'action' => 'excluirProduto'
		);

		$routes['editar_perfil'] = array(
			'route' => '/editar_perfil',
			'controller' => 'VendedorController',
			'action' => 'editarPerfil'
		);

		$routes['atualizar_perfil'] = array(
			'route' => '/atualizar_perfil',
			'controller' => 'VendedorController',
			'action' => 'atualizarPerfil'
		);

		//Fim Vendedor


		//Usuario

		$routes['categorias'] = array(
			'route' => '/categorias',
			'controller' => 'AppController',
			'action' => 'categorias'
		);

		$routes['pesquisar'] = array(
			'route' => '/pesquisar',
			'controller' => 'AppController',
			'action' => 'pesquisar'
		);

		$routes['novos_produtos'] = array(
			'route' => '/novos_produtos',
			'controller' => 'AppController',
			'action' => 'novosProdutos'
		);

		$routes['produtos_destaques'] = array(
			'route' => '/produtos_destaques',
			'controller' => 'AppController',
			'action' => 'produtosDestaques'
		);

		$routes['carrinho'] = array(
			'route' => '/carrinho',
			'controller' => 'AppController',
			'action' => 'carrinho'
		);

		$routes['vendedores'] = array(
			'route' => '/vendedores',
			'controller' => 'AppController',
			'action' => 'vendedores'
		);

		$routes['produtos_vendedor'] = array(
			'route' => '/produtos_vendedor',
			'controller' => 'AppController',
			'action' => 'produtosVendedor'
		);

		$routes['cupom'] = array(
			'route' => '/cupom',
			'controller' => 'AppController',
			'action' => 'cupom'
		);

		$routes['excluir_cupom'] = array(
			'route' => '/excluir_cupom',
			'controller' => 'AppController',
			'action' => 'excluirCupom'
		);

		$routes['perfil'] = array(
			'route' => '/perfil',
			'controller' => 'AppController',
			'action' => 'perfil'
		);

		$routes['atualizar_perfil_usuario'] = array(
			'route' => '/atualizar_perfil_usuario',
			'controller' => 'AppController',
			'action' => 'atualizarPerfil'
		);

		$routes['produto'] = array(
			'route' => '/produto',
			'controller' => 'AppController',
			'action' => 'produto'
		);

		$routes['produto-img'] = array(
			'route' => '/produto_img',
			'controller' => 'AppController',
			'action' => 'produtoImg'
		);

		$routes['adicionar_carrinho'] = array(
			'route' => '/adicionar_carrinho',
			'controller' => 'CarrinhoController',
			'action' => 'adicionarCarrinho'
		);

		$routes['finalizar_compra'] = array(
			'route' => '/finalizar_compra',
			'controller' => 'CarrinhoController',
			'action' => 'finalizarCompra'
		);

		$routes['finalizar'] = array(
			'route' => '/finalizar',
			'controller' => 'CarrinhoController',
			'action' => 'finalizar'
		);

		$routes['remover_produto'] = array(
			'route' => '/remover_produto',
			'controller' => 'CarrinhoController',
			'action' => 'removerProduto'
		);

		$routes['confirmar_dados'] = array(
			'route' => '/confirmar_dados',
			'controller' => 'AppController',
			'action' => 'confirmarDados'
		);

		$routes['devolucao'] = array(
			'route' => '/devolucao',
			'controller' => 'AppController',
			'action' => 'devolucao'
		);

		$routes['solicitar_devolucao'] = array(
			'route' => '/solicitar_devolucao',
			'controller' => 'AppController',
			'action' => 'solicitarDevolucao'
		);

		//Fim Usuario

		//WhatsApp
		$routes['webhook'] = array(
			'route' => '/webhook',
			'controller' => 'WhatsappController',
			'action' => 'webHook'
		);
		$routes['whatsapp_adm'] = array(
			'route' => '/whatsapp_adm',
			'controller' => 'WhatsappController',
			'action' => 'whatsappAdm'
		);
		$routes['enviar_mensagem'] = array(
			'route' => '/enviar_mensagem',
			'controller' => 'WhatsappController',
			'action' => 'enviarMensagem'
		);
		//WhatsApp

		$this->setRoutes($routes);
	}

}

?>