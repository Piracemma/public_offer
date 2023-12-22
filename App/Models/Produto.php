<?php

namespace App\Models;

use MF\Model\Container;
use MF\Model\Model;

class Produto extends Model {

    private $id_vendedor;
    private $nome_produto;
    private $descricao;
    private $variacao;
    private $categoria;
    private $publico;
    private $preco;
    private $estoque;
    private $imagem1;
    private $imagem2;
    private $imagem3;

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo,$valor) {
        $this->$atributo = $valor;
        return $this;
    }

    public function validarDados(){

        $valido = true;

        if(filter_var($this->__get('estoque'), FILTER_VALIDATE_INT) == false && $this->__get('estoque') >= 1) {
            $valido = false;
        }
        if(((float)$this->__get('preco')) < 10) {
            $valido = false;
        }
        if(strlen($this->__get('nome_produto')) > 50 || strlen($this->__get('nome_produto')) < 5) {
            $valido = false;
        }
        if(strlen($this->__get('categoria')) < 1) {
            $valido = false;
        }
        if(strlen($this->__get('descricao')) < 1) {
            $valido = false;
        }
        if(strlen($this->__get('publico')) < 1) {
            $valido = false;
        }

        return $valido;

    }

    public function validarDadosUpdate(){

        $valido = true;

        if(filter_var($this->__get('estoque'), FILTER_VALIDATE_INT) == false || $this->__get('estoque') < 1) {
            $valido = false;
        }
        if(((float)$this->__get('preco')) < 10) {
            $valido = false;
        }
        if(strlen($this->__get('descricao')) < 1) {
            $valido = false;
        }

        return $valido;

    }

    public function salvar(){

        $query = "INSERT INTO produtos(nome,descricao,variacao,preco,estoque,categoria,publico,imagem1,imagem2,imagem3,id_vendedor,ativo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$this->__get('nome_produto'));
        $stmt->bindValue(2,$this->__get('descricao'));
        $stmt->bindValue(3,$this->__get('variacao'));
        $stmt->bindValue(4,$this->__get('preco'));
        $stmt->bindValue(5,$this->__get('estoque'));
        $stmt->bindValue(6,$this->__get('categoria'));
        $stmt->bindValue(7,$this->__get('publico'));
        $stmt->bindValue(8,$this->__get('imagem1'));
        $stmt->bindValue(9,$this->__get('imagem2'));
        $stmt->bindValue(10,$this->__get('imagem3'));
        $stmt->bindValue(11,$this->__get('id_vendedor'));
        $stmt->bindValue(12,true);
        return $stmt->execute();
        

    }

    public function atualizar($id) {

        $query = "UPDATE produtos SET descricao = ?, variacao = ?, preco = ?, estoque = ?, ativo = 1 WHERE id = ? AND id_vendedor = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$this->__get('descricao'));
        $stmt->bindValue(2,$this->__get('variacao'));
        $stmt->bindValue(3,$this->__get('preco'));
        $stmt->bindValue(4,$this->__get('estoque'));        
        $stmt->bindValue(5, $id);        
        $stmt->bindValue(6,$this->__get('id_vendedor'));

        return $stmt->execute();

    }

    public function excluir($id, $id_vendedor) {

        $query = "DELETE FROM produtos WHERE id = ? AND id_vendedor = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $id);
        $stmt->bindValue(2, $id_vendedor);
        
        return $stmt->execute();

    }

    public function buscarProduto($id,$produto) {

        $query = "SELECT p.id,p.nome,p.descricao,p.variacao,p.estoque,p.preco,p.imagem1,p.imagem2,p.imagem3,p.id_vendedor,v.nome as 'nome_vendedor',v.entrega_propria,v.valor_entrega FROM produtos p 
        INNER JOIN vendedores v ON p.id_vendedor = v.id
        WHERE p.id = ? AND p.nome = ? AND p.ativo = 1";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$id);
        $stmt->bindValue(2,$produto);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function buscarProdutoCategoria($categoria_pesquisa, $limit, $offset) {

        $query = "SELECT * FROM produtos WHERE ativo = 1 AND categoria = ? ORDER BY data_criacao DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $categoria_pesquisa);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function totalListarCategoria($categoria) {

        $query = "SELECT COUNT(*) as contagem FROM produtos WHERE ativo = 1 AND categoria = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $categoria);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function buscarNovosProdutos($limit, $offset) {

        $data_inicial = date('y-m-d',mktime(0,0,0,date('m'),date('d')-1000,date('y')));
        $data_final = date('y-m-d');

        $query = "SELECT * FROM produtos WHERE data_criacao BETWEEN ? AND ? AND ativo = 1 ORDER BY data_criacao DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($query);
        
        $stmt->bindValue(1, $data_inicial);
        $stmt->bindValue(2, $data_final);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function totalNovosProdutos() {

        $data_inicial = date('y-m-d',mktime(0,0,0,date('m'),date('d')-1000,date('y')));
        $data_final = date('y-m-d');

        $query = "SELECT COUNT(*) as contagem FROM produtos WHERE data_criacao BETWEEN ? AND ? AND ativo = 1";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $data_inicial);
        $stmt->bindValue(2, $data_final);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function novidades() {

        $data_inicial = date('y-m-d',mktime(0,0,0,date('m'),date('d')-1000,date('y')));
        $data_final = date('y-m-d');

        $query = "SELECT * FROM produtos WHERE data_criacao BETWEEN ? AND ? AND ativo = 1 ORDER BY data_criacao DESC LIMIT 12";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$data_inicial);
        $stmt->bindValue(2,$data_final);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }  
    
    public function buscarProdutosDestaques($limit, $offset) {

        $data_inicial = date('y-m-d',mktime(0,0,0,date('m'),date('d')-1000,date('y')));
        $data_final = date('y-m-d');

        $query = "SELECT * FROM produtos WHERE data_criacao BETWEEN ? AND ? AND ativo = 1 ORDER BY num_vendas DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($query);
        
        $stmt->bindValue(1, $data_inicial);
        $stmt->bindValue(2, $data_final);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function totalProdutosDestaques() {

        $data_inicial = date('y-m-d',mktime(0,0,0,date('m'),date('d')-1000,date('y')));
        $data_final = date('y-m-d');

        $query = "SELECT COUNT(*) as contagem FROM produtos WHERE data_criacao BETWEEN ? AND ? AND ativo = 1";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $data_inicial);
        $stmt->bindValue(2, $data_final);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function destaques() {

        $data_inicial = date('y-m-d',mktime(0,0,0,date('m'),date('d')-90,date('y')));
        $data_final = date('y-m-d');

        $query = "SELECT * FROM produtos WHERE data_criacao BETWEEN ? AND ? AND ativo = 1 ORDER BY num_vendas DESC LIMIT 12";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$data_inicial);
        $stmt->bindValue(2,$data_final);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function produtosVendedor($id_vendedor,$id_produto) {

        $query = "SELECT id,nome, preco, imagem1 FROM produtos WHERE id_vendedor = ? AND ativo = 1 AND id NOT IN(?) ORDER BY data_criacao DESC LIMIT 20";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$id_vendedor);
        $stmt->bindValue(2,$id_produto);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function buscarProdutosVendedor($id_vendedor, $id_cidade, $limit, $offset) {

        $query = "SELECT p.id, p.nome, p.preco, p.imagem1 FROM produtos p
        INNER JOIN vendedores v ON p.id_vendedor = v.id
        WHERE p.id_vendedor = ? AND p.ativo = 1 AND v.id_cidade = ?
        ORDER BY p.id DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$id_vendedor);
        $stmt->bindValue(2,$id_cidade);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function totaisProdutosVendedor($id_vendedor, $id_cidade) {

        $query = "SELECT p.id, p.nome, p.preco, p.imagem1 FROM produtos p
        INNER JOIN vendedores v ON p.id_vendedor = v.id
        WHERE p.id_vendedor = ? AND p.ativo = 1 AND v.id_cidade = ?
        ORDER BY data_criacao DESC";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$id_vendedor);
        $stmt->bindValue(2,$id_cidade);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function buscarProdutoId($id) {

        $query = "SELECT p.id,p.nome,p.preco,p.imagem1,p.imagem2,p.imagem3,p.id_vendedor,v.nome as 'nome_vendedor',v.entrega_propria,v.valor_entrega,p.estoque,p.num_vendas FROM produtos p 
        INNER JOIN vendedores v ON p.id_vendedor = v.id
        WHERE p.id = ? AND p.ativo = 1";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$id);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function verificaAtivoEstoque($id_produto) {

        $query = "SELECT estoque,ativo FROM produtos WHERE id = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$id_produto);
        $stmt->execute();

        $dados = $stmt->fetch(\PDO::FETCH_ASSOC);

        $retorno = array(
            'estoque' => $dados['estoque'],
            'ativo' => $dados['ativo']
        );

        return $retorno;

    }

    public function validaFinalizadora($id_finalizadora) {

        $query = "SELECT * FROM finalizadoras WHERE id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$id_finalizadora);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function finalizarVenda($id_usuario, $id_vendedor, $entrega_propria, $valor_entrega, $soma_produtos, $total, $finalizadora, $endereco, $observacao, $data, $comissao, $cupom, $id_cupom = null, $valor_desconto = null) {

        $query = "INSERT INTO vendas(id_usuario, id_vendedor, entrega_propria, valor_entrega, total_produtos, total_compra, id_finalizadora, endereco, observacao, aprovado, data_venda, comissao,cupom, id_cupom, valor_desconto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_usuario);
        $stmt->bindValue(2, $id_vendedor);
        $stmt->bindValue(3, $entrega_propria);
        $stmt->bindValue(4, $valor_entrega);
        $stmt->bindValue(5, $soma_produtos);
        $stmt->bindValue(6, $total);
        $stmt->bindValue(7, $finalizadora);
        $stmt->bindValue(8, $endereco);
        $stmt->bindValue(9, $observacao);
        $stmt->bindValue(10, 2);
        $stmt->bindValue(11,$data);
        $stmt->bindValue(12,$comissao);
        $stmt->bindValue(13,$cupom);
        $stmt->bindValue(14, $id_cupom);
        $stmt->bindValue(15, $valor_desconto);
        

        $retorno = array(
            'fetch' => $stmt->execute(),
            'id_venda' => $this->db->lastInsertId()
        );

        return $retorno;

    }

    public function finalizarVendaItem($id_venda, $id_produto, $nome, $preco, $quantidade, $total, $observacao) {

        $query = "INSERT INTO venda_item(id_venda, id_produto, nome_produto, preco, quantidade, total, observacao) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$id_venda);
        $stmt->bindValue(2,$id_produto);
        $stmt->bindValue(3,$nome);
        $stmt->bindValue(4,$preco);
        $stmt->bindValue(5,$quantidade);
        $stmt->bindValue(6,$total);
        $stmt->bindValue(7,$observacao);
        
        return $stmt->execute();

    }

    public function atualizarEstoque($id_produto, $quantidade) {

        $produto = $this->buscarProdutoId($id_produto);

        $atualiza_estoque = $produto['estoque'] - $quantidade;

        $atualiza_vendas = $produto['num_vendas'] + $quantidade;

        if($atualiza_estoque == 0) {

            $query = "UPDATE produtos SET estoque = ? , ativo = false, num_vendas = ? WHERE id = ?";

        } else {

            $query = "UPDATE produtos SET estoque = ?, num_vendas = ?  WHERE id = ?";

        }

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1,$atualiza_estoque);
        $stmt->bindValue(2,$atualiza_vendas);
        $stmt->bindValue(3,$id_produto);
        return $stmt->execute();

    }

    public function atalizarVendasCompras($id_usuario, $id_vendedor) {

        $usuario = Container::getModel('Usuario');
        $info_usuario = $usuario->getPorId($id_usuario);
        $info_usuario['num_compras'] += 1;

        $query_usuario = "UPDATE usuarios set num_compras = ? WHERE id = ?";

        $stmt_usuario = $this->db->prepare($query_usuario);
        $stmt_usuario->bindValue(1, $info_usuario['num_compras']);
        $stmt_usuario->bindValue(2, $id_usuario);
        $stmt_usuario->execute();
        

        $vendedor = Container::getModel('Vendedor');
        $info_vendedor = $vendedor->getPorId($id_vendedor);
        $info_vendedor['num_vendas'] += 1;

        $query_vendedor = "UPDATE vendedores set num_vendas = ? WHERE id = ?";

        $stmt_vendedor = $this->db->prepare($query_vendedor);
        $stmt_vendedor->bindValue(1, $info_vendedor['num_vendas']);
        $stmt_vendedor->bindValue(2, $id_vendedor);
        $stmt_vendedor->execute();

    }

    function pesquisa($variations) {

        $conditions = [];
    
        foreach ($variations as $variation) {
            $conditions[] = "nome LIKE ?";
        }
    
        $where = implode(" OR ", $conditions);

        $query = "SELECT * FROM produtos WHERE ativo = 1 AND (". $where.")";

        $stmt = $this->db->prepare($query);
    
        foreach ($variations as $index => $variation) {
            $stmt->bindValue($index + 1, "%$variation%");
        }
    
        $stmt->execute();
    
        $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
        return $resultado;

    }

    public function consultaVariavel($tabela, $busca, $condicoes = [1], $condicoes_campo = ['1'], $orderby = 'id', $limit = 1000000000, $offset = 0) {

        $wheres = [];

        foreach($condicoes as $condicao) {

            $wheres[] = "$condicao = ?";

        }

        $where = implode(" and ",$wheres);

        $query = "SELECT $busca FROM $tabela WHERE $where ORDER BY $orderby LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($query);

        foreach($condicoes_campo as $key => $campo) {

            $stmt->bindValue($key + 1, $campo);

        }
        
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function queryAll($query, $condictions) { 

        $stmt = $this->db->prepare($query);
        
        foreach($condictions as $key => $condiction) {

            $stmt->bindValue($key + 1, $condiction);
            
        }
        
        $stmt->execute(); 
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC); 
        
    }

    public function totaisVendaHistorico($id_vendedor, $data_inicio, $data_final) {
    
        $query = "SELECT COUNT(*) as contagem FROM vendas WHERE data_venda BETWEEN ? AND ? AND id_vendedor = ? AND (aprovado = 1 OR aprovado = 0 OR aprovado = 3)";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $data_inicio);
        $stmt->bindValue(2, $data_final);
        $stmt->bindValue(3, $id_vendedor);

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function totaisVendaDia($id_vendedor, $data_inicio, $data_final) {
    
        $query = "SELECT COUNT(*) as contagem FROM vendas WHERE data_venda BETWEEN ? AND ? AND id_vendedor = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $data_inicio);
        $stmt->bindValue(2, $data_final);
        $stmt->bindValue(3, $id_vendedor);

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }


}

