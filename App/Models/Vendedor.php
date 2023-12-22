<?php

namespace App\Models;

use MF\Model\Model;

class Vendedor extends Model {

    protected $possui_entrega;
    protected $valor_entrega;
    protected $segmento;
    protected $sobrenos;
    protected $foto_caminho;
    protected $foto_perfil;
    protected $alterar_imagem = false;

    public function salvar() {

        $query = "INSERT INTO vendedores(nome,email,senha,endereco,bairro,id_cidade,documento,telefone,entrega_propria,valor_entrega,comissao,foto_perfil,foto_caminho,segmento,descricao,tipo_usuario) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$this->__get('nome'));
        $stmt->bindValue(2,$this->__get('email'));
        $stmt->bindValue(3,$this->__get('senha'));
        $stmt->bindValue(4,$this->__get('endereco'));
        $stmt->bindValue(5,$this->__get('bairro'));
        $stmt->bindValue(6,$this->__get('id_cidade'));
        $stmt->bindValue(7,$this->__get('documento'));
        $stmt->bindValue(8,$this->__get('telefone'));
        $stmt->bindValue(9,$this->__get('possui_entrega'));
        $stmt->bindValue(10,$this->__get('valor_entrega'));
        $stmt->bindValue(11,0.07);
        $stmt->bindValue(12,$this->__get('foto_perfil'));
        $stmt->bindValue(13,$this->__get('foto_caminho'));
        $stmt->bindValue(14,$this->__get('segmento'));
        $stmt->bindValue(15,$this->__get('sobrenos'));
        $stmt->bindValue(16,2);

        $retorno = array(
            'fetch' => $stmt->execute(),
            'id_vendedor' => $this->db->lastInsertId()
        );

        return $retorno;
    }

    public function horariosFuncionamento($id_vendedor, $segunda_de, $segunda_ate, $terca_de, $terca_ate, $quarta_de, $quarta_ate, $quinta_de, $quinta_ate, $sexta_de, $sexta_ate, $sabado_de, $sabado_ate, $domingo_de, $domingo_ate) {

        $query = "INSERT INTO horarios_funcionamento(id_vendedor, segunda_de, segunda_ate, terca_de, terca_ate, quarta_de, quarta_ate, quinta_de, quinta_ate, sexta_de, sexta_ate, sabado_de, sabado_ate, domingo_de, domingo_ate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $segunda_de);
        $stmt->bindValue(3, $segunda_ate);
        $stmt->bindValue(4, $terca_de);
        $stmt->bindValue(5,$terca_ate);
        $stmt->bindValue(6, $quarta_de);
        $stmt->bindValue(7, $quarta_ate);
        $stmt->bindValue(8, $quinta_de);
        $stmt->bindValue(9, $quinta_ate);
        $stmt->bindValue(10, $sexta_de);
        $stmt->bindValue(11, $sexta_ate);
        $stmt->bindValue(12, $sabado_de);
        $stmt->bindValue(13, $sabado_ate);
        $stmt->bindValue(14, $domingo_de);
        $stmt->bindValue(15, $domingo_ate);

        return $stmt->execute();

    }

    public function buscarHorarioFuncionamento($id_vendedor) {

        $query = "SELECT segunda_de, segunda_ate, terca_de, terca_ate, quarta_de, quarta_ate, quinta_de, quinta_ate, sexta_de, sexta_ate, sabado_de, sabado_ate, domingo_de, domingo_ate FROM horarios_funcionamento WHERE id_vendedor = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $id_vendedor);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function atualizar($id_vendedor) {

        $alterar_imagem = $this->__get('alterar_imagem');

        if($alterar_imagem) {

            $query = "UPDATE vendedores SET nome = ?, endereco = ?, bairro = ?, id_cidade = ?, documento = ?, telefone = ?, entrega_propria = ?, valor_entrega = ?, foto_perfil = ?, foto_caminho = ?, segmento = ?, descricao = ? WHERE id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1,$this->__get('nome'));
            $stmt->bindValue(2,$this->__get('endereco'));
            $stmt->bindValue(3,$this->__get('bairro'));
            $stmt->bindValue(4,$this->__get('id_cidade'));
            $stmt->bindValue(5,$this->__get('documento'));
            $stmt->bindValue(6,$this->__get('telefone'));
            $stmt->bindValue(7,$this->__get('possui_entrega'));
            $stmt->bindValue(8,$this->__get('valor_entrega'));
            $stmt->bindValue(9,$this->__get('foto_perfil'));
            $stmt->bindValue(10,$this->__get('foto_caminho'));
            $stmt->bindValue(11,$this->__get('segmento'));
            $stmt->bindValue(12,$this->__get('sobrenos'));
            $stmt->bindValue(13,$id_vendedor);
            $stmt->execute();

            return $this;

        } else {

            $query = "UPDATE vendedores SET nome = ?, endereco = ?, bairro = ?, id_cidade = ?, documento = ?, telefone = ?, entrega_propria = ?, valor_entrega = ?, segmento = ?, descricao = ? WHERE id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(1,$this->__get('nome'));
            $stmt->bindValue(2,$this->__get('endereco'));
            $stmt->bindValue(3,$this->__get('bairro'));
            $stmt->bindValue(4,$this->__get('id_cidade'));
            $stmt->bindValue(5,$this->__get('documento'));
            $stmt->bindValue(6,$this->__get('telefone'));
            $stmt->bindValue(7,$this->__get('possui_entrega'));
            $stmt->bindValue(8,$this->__get('valor_entrega'));
            $stmt->bindValue(9,$this->__get('segmento'));
            $stmt->bindValue(10,$this->__get('sobrenos'));
            $stmt->bindValue(11,$id_vendedor);
            $stmt->execute();

            return $this;

        }

    }

    public function validarCadastro() {//complementar

        $valido = true;

        if(strlen($this->__get('nome')) < 3) {
            $valido = false;
        }
        if(strlen($this->__get('email')) < 10) {
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
        if(filter_var($this->__get('email'), FILTER_VALIDATE_EMAIL) == false) {
            $valido = false;
        }
        if($this->__get('segmento') == 'segmento') {
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
        if($this->__get('segmento') == 'segmento') {
            $valido = false;
        }
        $documento = $this->__get('documento');
        if(!$this->validar_cpf_cnpj($documento)) {
            $valido = false;
        }

        return $valido;
    }

    public function vendedor($id_vendedor) {

        $query = "SELECT v.id, v.nome, v.email, v.endereco, v.bairro, c.cidade, v.documento, v.telefone, v.entrega_propria, v.valor_entrega, v.foto_perfil, v.foto_caminho, v.segmento, v.descricao FROM vendedores v
        INNER JOIN cidades c ON v.id_cidade = c.id
        WHERE v.id = ?";

        $stmt = $this->db->prepare($query);
        
        $stmt->bindValue(1, $id_vendedor);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

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

        $query = "SELECT * FROM vendedores where id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$id);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }
    
    public function relatorioPersonalizado($id_vendedor, $de, $ate, $estado) {

        $query = "SELECT v.id, v.data_venda, v.total_produtos, v.valor_entrega, c.cupom as 'nome_cupom', v.cupom, v.id_cupom, v.valor_desconto, v.entrega_propria, v.total_compra, v.comissao, v.aprovado, v.motivo_cancelamento, v.informacoes FROM vendas v
        LEFT JOIN cupons c ON v.id_cupom = c.id
        WHERE v.id_vendedor = ? AND v.data_venda BETWEEN ? AND ?";

        if($estado == 'aprovadas') {
            $query = $query." AND aprovado = 1";
        } else if($estado == 'canceladas') {
            $query = $query." AND aprovado = 0";
        }

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $de.' 00:00::');
        $stmt->bindValue(3, $ate.' 23:59:59');
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function relatorioTotais($id_vendedor, $de, $ate) {

        $totais = array();

        //Total Geral
        $query = "SELECT SUM(total_compra) as 'total_geral', COUNT(*) as 'quantidade' FROM vendas WHERE id_vendedor = ? AND data_venda BETWEEN ? AND ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $de.' 00:00::');
        $stmt->bindValue(3, $ate.' 23:59:59');
        $stmt->execute();

        $totais['geral'] = $stmt->fetch(\PDO::FETCH_ASSOC);

        //Total Aprovadas
        $query = "SELECT SUM(total_compra) as 'total_aprovada', COUNT(*) as 'quantidade' FROM vendas WHERE id_vendedor = ? AND data_venda BETWEEN ? AND ? AND aprovado = 1";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $de.' 00:00::');
        $stmt->bindValue(3, $ate.' 23:59:59');
        $stmt->execute();

        $totais['aprovadas'] = $stmt->fetch(\PDO::FETCH_ASSOC);

        //Total Canceladas
        $query = "SELECT SUM(total_compra) as 'total_cancelada', COUNT(*) as 'quantidade' FROM vendas WHERE id_vendedor = ? AND data_venda BETWEEN ? AND ? AND aprovado = 0";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $de.' 00:00::');
        $stmt->bindValue(3, $ate.' 23:59:59');
        $stmt->execute();

        $totais['canceladas'] = $stmt->fetch(\PDO::FETCH_ASSOC);

        //Total Devolvidas
        $query = "SELECT SUM(total_compra) as 'total_devolvida', COUNT(*) as 'quantidade' FROM vendas WHERE id_vendedor = ? AND data_venda BETWEEN ? AND ? AND aprovado = 3";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $de.' 00:00::');
        $stmt->bindValue(3, $ate.' 23:59:59');
        $stmt->execute();

        $totais['devolvidas'] = $stmt->fetch(\PDO::FETCH_ASSOC);

        //Total Comissao
        $query = "SELECT SUM(comissao) as 'total_comissao', COUNT(*) as 'quantidade' FROM vendas WHERE id_vendedor = ? AND data_venda BETWEEN ? AND ? AND aprovado = 1";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $de.' 00:00::');
        $stmt->bindValue(3, $ate.' 23:59:59');
        $stmt->execute();

        $totais['comissao'] = $stmt->fetch(\PDO::FETCH_ASSOC);


        return $totais;

    }

    public function relatorioFrete($id_vendedor, $de, $ate) {

        $fretes = array();

        $query = "SELECT COUNT(*) as 'quantidade' FROM `vendas` WHERE id_vendedor = ? AND data_venda BETWEEN ? AND ? AND aprovado = 1 AND entrega_propria = 0";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $de.' 00:00::');
        $stmt->bindValue(3, $ate.' 23:59:59');
        $stmt->execute();

        $fretes['total_frete'] = $stmt->fetch(\PDO::FETCH_ASSOC);


        $query = "SELECT COUNT(*) as 'quantidade' FROM `vendas` WHERE id_vendedor = ? AND data_venda BETWEEN ? AND ? AND aprovado = 3 AND entrega_propria = 0";
        
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $de.' 00:00::');
        $stmt->bindValue(3, $ate.' 23:59:59');
        $stmt->execute();

        $fretes['total_frete_devolvido'] = $stmt->fetch(\PDO::FETCH_ASSOC);


        return $fretes;

    }

    public function listarVendedoresCidade($id_cidade, $ordem, $limit, $offset) {

        $query = "SELECT v.id, v.nome, v.foto_caminho, v.foto_perfil, MAX(p.id) as id_produto FROM vendedores v
        INNER JOIN produtos p ON v.id = p.id_vendedor
        WHERE v.id_cidade = ? AND p.ativo = 1 GROUP BY v.id";

        if($ordem == 'alfabetica') {

            $query = $query." ORDER BY nome LIMIT $limit OFFSET $offset";

        }

        if($ordem == 'mais_vendas') {

            $query = $query." ORDER BY v.num_vendas DESC LIMIT $limit OFFSET $offset";

        }

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_cidade);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function totalVendedoresCidade($id_cidade) {

        $query = "SELECT MAX(p.id) as id_produto FROM vendedores v
        INNER JOIN produtos p ON v.id = p.id_vendedor
        WHERE v.id_cidade = ? AND p.ativo = 1 GROUP BY v.id";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_cidade);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

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