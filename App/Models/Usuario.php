<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model {

    
    protected $end_referencia;

    public function salvar() {//a fazer

        $query = "INSERT INTO usuarios(nome,email,senha,endereco,bairro,id_cidade,referencia,documento,telefone,tipo_usuario) VALUES (?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$this->__get('nome'));
        $stmt->bindValue(2,$this->__get('email'));
        $stmt->bindValue(3,$this->__get('senha'));
        $stmt->bindValue(4,$this->__get('endereco'));
        $stmt->bindValue(5,$this->__get('bairro'));
        $stmt->bindValue(6,$this->__get('id_cidade'));
        $stmt->bindValue(7,$this->__get('end_referencia'));
        $stmt->bindValue(8,$this->__get('documento'));
        $stmt->bindValue(9,$this->__get('telefone'));
        $stmt->bindValue(10,1);
        $stmt->execute();

        return $this;
    }

    public function atualizar($id_usuario) {

        $query = "UPDATE usuarios SET nome = ?, endereco = ?, bairro = ?, id_cidade = ?, referencia = ?, documento = ?, telefone = ? WHERE id = ?";

        $stmt = $this->db->prepare($query);
            $stmt->bindValue(1,$this->__get('nome'));
            $stmt->bindValue(2,$this->__get('endereco'));
            $stmt->bindValue(3,$this->__get('bairro'));
            $stmt->bindValue(4,$this->__get('id_cidade'));
            $stmt->bindValue(5,$this->__get('end_referencia'));
            $stmt->bindValue(6,$this->__get('documento'));
            $stmt->bindValue(7,$this->__get('telefone'));
            $stmt->bindValue(8,$id_usuario);
            $stmt->execute();

            return $this;

    }

    public function validarCadastro() {//complementar

        $valido = true;

        if(strlen($this->__get('nome')) < 3) {
            $valido = false;
        }
        if(strlen($this->__get('email')) < 10) {
            $valido = false;
        }
        if(strlen($this->__get('endereco')) < 3) {
            $valido = false;
        }
        if(strlen($this->__get('bairro')) < 2) {
            $valido = false;
        }
        if(filter_var($this->__get('telefone'), FILTER_VALIDATE_INT) == false) {
            $valido = false;
        }
        if(filter_var($this->__get('email'), FILTER_VALIDATE_EMAIL) == false) {
            $valido = false;
        }
        $documento = $this->__get('documento');
        if(!$this->validar_cpf_cnpj($documento)) {
            $valido = false;
        }

        return $valido;
    }

    public function validarAtualizarCadastro() {//complementar

        $valido = true;

        if(strlen($this->__get('nome')) < 3) {
            $valido = false;
        }
        if(strlen($this->__get('endereco')) < 8) {
            $valido = false;
        }
        if(strlen($this->__get('bairro')) < 5) {
            $valido = false;
        }
        if(filter_var($this->__get('telefone'), FILTER_VALIDATE_INT) == false) {
            $valido = false;
        }
        $documento = $this->__get('documento');
        if(!$this->validar_cpf_cnpj($documento)) {
            $valido = false;
        }

        return $valido;
    }

    public function validarCidade() {

        $query = "SELECT id FROM cidades WHERE cidade = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$this->__get('cidade'));
        $stmt->execute();

        $cidadevalida = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($cidadevalida == false) {

            return false;

        } else {
            $this->__set('id_cidade',$cidadevalida['id']);
            
            return true;
        }

    }

    public function getPorEmail() {

        $query = "SELECT nome,email FROM usuarios where email = ? OR documento = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$this->__get('email'));
        $stmt->bindValue(2,$this->__get('documento'));
        $stmt->execute();

        $existe = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($existe == false) {

            $query = "SELECT nome,email FROM vendedores where email = ? OR documento = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1,$this->__get('email'));            
            $stmt->bindValue(2,$this->__get('documento'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        } else {

            return $existe;

        }

    }

    public function getPorId($id) {

        $query = "SELECT * FROM usuarios where id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$id);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function listarCompras($id_usuario, $limit, $offset) {

        $query = "SELECT v.id, v.data_venda, v.entrega_propria, v.valor_entrega, v.total_produtos, v.cupom, v.valor_desconto, v.total_compra, ve.nome as nome_vendedor, ve.endereco as endereco_vendedor, ve.telefone, f.finalizadora, v.observacao as observacao_compra, vi.id_produto, vi.nome_produto, vi.preco, vi.quantidade, vi.total, vi.observacao as observacao_item, v.endereco as endereco_alternativo, u.endereco, v.aprovado
        FROM 
        (
            SELECT *
            FROM vendas            
            WHERE id_usuario = ?
            ORDER BY data_venda DESC
            LIMIT $limit OFFSET $offset
        ) AS v
        INNER JOIN venda_item vi on v.id = vi.id_venda
        INNER JOIN vendedores ve on v.id_vendedor = ve.id
        INNER JOIN finalizadoras f on v.id_finalizadora = f.id
        INNER JOIN usuarios u on v.id_usuario = u.id
        ORDER BY v.data_venda DESC";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$id_usuario);
        $stmt->execute();

        $retorno = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $retorno;

    }

    public function totalListarCompras($id_usuario) {

        $query = "SELECT COUNT(*) as contagem FROM vendas WHERE id_usuario = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$id_usuario);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function usuario($id_vendedor) {

        $query = "SELECT v.nome, v.email, v.endereco, v.bairro, c.cidade, v.documento, v.telefone, v.referencia FROM usuarios v
        INNER JOIN cidades c ON v.id_cidade = c.id
        WHERE v.id = ?";

        $stmt = $this->db->prepare($query);
        
        $stmt->bindValue(1, $id_vendedor);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function recuperarPorEmail($email) {

        $query = "SELECT id, email, tipo_usuario FROM usuarios WHERE email = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $email);
        $stmt->execute();

        $existe = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($existe == false) {

            $query = "SELECT id, email, tipo_usuario FROM vendedores WHERE email = ?";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(1, $email);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        } else {

            return $existe;

        }

    }

    public function enderecoUsuario($id) {

        $query = "SELECT endereco FROM usuarios WHERE id = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id);
        $stmt->execute();       

        return $stmt->fetch(\PDO::FETCH_ASSOC);        

    }

    public function atualizarSenha($tabela, $senha, $id_usuario, $tipo_usuario) {

        if($tabela == 'usuarios') {
            $query = "UPDATE usuarios SET senha = ? WHERE id = ? AND tipo_usuario = ?";
        }
        if($tabela == 'vendedores') {
            $query = "UPDATE vendedores SET senha = ? WHERE id = ? AND tipo_usuario = ?";
        }

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $senha);
        $stmt->bindValue(2, $id_usuario);
        $stmt->bindValue(3, $tipo_usuario);

        return $stmt->execute();

    }

    public function validar_cpf_cnpj($numero) {
        $numero = preg_replace('/\D/', '', $numero);
        
        if (strlen($numero) == 11) {
            if (preg_match('/^(\d)\1*$/', $numero)) {
                return false;
            }
            
            $soma = 0;
            for ($i = 0; $i < 9; $i++) {
                $soma += intval($numero[$i]) * (10 - $i);
            }
            
            $digito1 = ($soma % 11 < 2) ? 0 : 11 - ($soma % 11);
            
            $soma = 0;
            for ($i = 0; $i < 10; $i++) {
                $soma += intval($numero[$i]) * (11 - $i);
            }
            
            $digito2 = ($soma % 11 < 2) ? 0 : 11 - ($soma % 11);
            
            if ($numero[9] == $digito1 && $numero[10] == $digito2) {
                return true;
            }
        } elseif (strlen($numero) == 14) {
            if (preg_match('/^(\d)\1*$/', $numero)) {
                return false;
            }
            
            $soma = 0;
            $multiplicador = 5;
            for ($i = 0; $i < 12; $i++) {
                $soma += intval($numero[$i]) * $multiplicador;
                $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
            }
            
            $digito1 = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
            
            $soma = 0;
            $multiplicador = 6;
            for ($i = 0; $i < 13; $i++) {
                $soma += intval($numero[$i]) * $multiplicador;
                $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
            }
            
            $digito2 = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
            
            if ($numero[12] == $digito1 && $numero[13] == $digito2) {
                return true;
            }
        }
        
        return false;
    }

}


?>