<?php


namespace MF\Model;

abstract class Model {

	protected $db;
    protected $email;
    protected $senha;
	protected $id;
    protected $nome;
    protected $validasenha;
    protected $documento;
    protected $cidade;
    protected $id_cidade;
    protected $endereco;
    protected $bairro;
    protected $telefone;
	protected $autenticado;
	protected $tipo_usuario;

	public function __construct(\PDO $db) {
		$this->db = $db;
	}

	public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo,$valor) {
        $this->$atributo = $valor;
        return $this;
    }

	public function login(){
		
			$query = "SELECT id,nome,email,senha,endereco,bairro,telefone,id_cidade,referencia,tipo_usuario FROM usuarios WHERE email = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(1,$this->__get('email'));
			$stmt->execute();
	
			$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
	
			if($usuario == false) {
	
				$query = "SELECT id,nome,email,senha,endereco,bairro,telefone,id_cidade,entrega_propria,valor_entrega,foto_caminho,foto_perfil,tipo_usuario FROM vendedores WHERE email = ?";
				$stmt = $this->db->prepare($query);
				$stmt->bindValue(1,$this->__get('email'));
				$stmt->execute();
		
				$vendedor = $stmt->fetch(\PDO::FETCH_ASSOC);

				if($vendedor == false) {

					$this->__set('autenticado',false);

				} else {

					$validasenha = password_verify($this->__get('senha'), $vendedor['senha']);

					if($validasenha == false) {

						$this->__set('autenticado',false);

					} else {

						if($vendedor['entrega_propria']) {

							$this->__set('possui_entrega',true);
							$this->__set('valor_entrega',$vendedor['valor_entrega']);
							$this->__set('tipo_usuario',$vendedor['tipo_usuario']);
							$this->__set('id',$vendedor['id']);
							$this->__set('nome',$vendedor['nome']);
							$this->__set('endereco',$vendedor['endereco']);
							$this->__set('bairro',$vendedor['bairro']);
							$this->__set('telefone',$vendedor['telefone']);
							$this->__set('id_cidade',$vendedor['id_cidade']);
							$this->__set('foto_caminho',$vendedor['foto_caminho']);
							$this->__set('foto_perfil',$vendedor['foto_perfil']);							
							$this->__set('autenticado',true);
	
						} else {
	
							$this->__set('possui_entrega',false);
							$this->__set('tipo_usuario',$vendedor['tipo_usuario']);
							$this->__set('id',$vendedor['id']);
							$this->__set('nome',$vendedor['nome']);
							$this->__set('endereco',$vendedor['endereco']);
							$this->__set('bairro',$vendedor['bairro']);
							$this->__set('telefone',$vendedor['telefone']);
							$this->__set('id_cidade',$vendedor['id_cidade']);
							$this->__set('foto_caminho',$vendedor['foto_caminho']);
							$this->__set('foto_perfil',$vendedor['foto_perfil']);
							$this->__set('autenticado',true);
	
						}

					}					

				}

			} else {
	
				$validasenha = password_verify($this->__get('senha'), $usuario['senha']);
				
				if($validasenha == false) {
	
					$this->__set('autenticado',false);
	
				} else if($validasenha) {
	
					$this->__set('id',$usuario['id']);
					$this->__set('nome',$usuario['nome']);
					$this->__set('endereco',$usuario['endereco']);
					$this->__set('bairro',$usuario['bairro']);
					$this->__set('telefone',$usuario['telefone']);
					$this->__set('id_cidade',$usuario['id_cidade']);
					$this->__set('end_referencia',$usuario['referencia']);
					$this->__set('autenticado',true);
					$this->__set('tipo_usuario',$usuario['tipo_usuario']);
	
				}
	
			}
		
	}
}


?>