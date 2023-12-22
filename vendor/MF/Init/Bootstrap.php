<?php

namespace MF\Init;

abstract class Bootstrap {
	private $routes;

	abstract protected function initRoutes(); 

	public function __construct() {
		$this->initRoutes();
		$this->run($this->getUrl());
	}

	public function getRoutes() {
		return $this->routes;
	}

	public function setRoutes(array $routes) {
		$this->routes = $routes;
	}

	protected function run($uri) {

		$armazena = explode('/',$uri);
		$armazena[1] = isset($armazena[1]) ? $armazena[1] : '';
		$armazena_urldecode = urldecode($armazena[1]);
		$armazena_caminho = htmlspecialchars($armazena_urldecode, ENT_QUOTES, 'UTF-8');
		$url = '/'.$armazena_caminho;
		$id_produto = isset($armazena[2]) ? $armazena[2] : '';
		$id = htmlspecialchars($id_produto, ENT_QUOTES, 'UTF-8');
        $nome_produto = isset($armazena[3]) ? $armazena[3] : '';
		$nome_urldecode = urldecode($nome_produto);
		$nome = htmlspecialchars($nome_urldecode, ENT_QUOTES, 'UTF-8');
        $explode = explode('_',$nome);
        $produto = implode(' ',$explode);

		if($url == '/produto') {
			
			if(isset($armazena[2]) && $id != '' && isset($armazena[3]) && $produto != '') {
				
				foreach ($this->getRoutes() as $key => $route) {
					if($url == $route['route']) {
						$class = "App\\Controllers\\".ucfirst($route['controller']);
		
						$controller = new $class;
						
						$action = $route['action'];
		
						$controller->$action($id,$produto);
					}
				}
				
			} else {

				header("Location: /");
				die();
				
			}
			
		} else {

			$validaRoute = false;
			
			foreach ($this->getRoutes() as $key => $route) {
				if($url == $route['route']) {
					$class = "App\\Controllers\\".ucfirst($route['controller']);
	
					$controller = new $class;
					
					$action = $route['action'];
	
					$controller->$action();

					$validaRoute = true;
				}
			}

			if(!$validaRoute) {

				header("Location: /");
				die();

			}
			
		}		
		
	}

	protected function getUrl() {
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}
}

//VARIAVEIS DE AMBIANTE:

//Host Atual
define('HOST_APLIC','http://localhost:9000/'); 

//Banco de dados
define('DB_CONFIG','mysql:host=localhost;dbname=offer;charset=utf8');
define('DB_USER','root');
define('DB_PASSWORD','');

//Comissao
define('COMISSAO', 0.07);

//Valor Frete Cidade
define('FRETE_CASSIA', 3.00);

//VALOR FRETE/VENDEDOR/CIDADE
define('ENTREGA_VENDEDOR_CASSIA', 2.00);

//Caminho Views
define('PATH_VIEW','../App/Views/');
define('NAVBAR','../App/Views/navbar.php');

//coockie definições
define('COOKIE_DOMAIN', 'localhost');
define('COOKIE_SECURE', false);
define('COOKIE_OPTIONS_TEMP', array (
	'expires' => time()+3600,                        
	'path' => '/',                        
	'domain' => COOKIE_DOMAIN,                        
	'secure' => COOKIE_SECURE,                        
	'httponly' => true,                        
	'samesite' => 'Strict' // None || Lax  || Strict                        
));
define('COOKIE_OPTIONS_INFINITY', array (
	'expires' => time()+2592000,                        
	'path' => '/',                        
	'domain' => COOKIE_DOMAIN,                        
	'secure' => COOKIE_SECURE,                        
	'httponly' => true,                        
	'samesite' => 'Strict' // None || Lax  || Strict                        
));

?>