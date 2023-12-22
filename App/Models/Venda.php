<?php

namespace App\Models;

use MF\Model\Container;
use MF\Model\Model;

class Venda extends Model {

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo,$valor) {
        $this->$atributo = $valor;
        return $this;
    }

    public function validaVendaPendente($id_venda, $id_vendedor) {

        $query = "SELECT id, data_venda, entrega_propria FROM vendas WHERE id = ? AND id_vendedor = ? AND aprovado = 2";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$id_venda);
        $stmt->bindValue(2,$id_vendedor);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function existeVendaPendente($id_vendedor, $date) {

        $query = "SELECT id FROM vendas WHERE id_vendedor = ? AND aprovado = 2 and data_venda BETWEEN ? AND ?";

        $stmt = $this->db->prepare($query);
        
        $stmt->bindValue(1,$id_vendedor);
        $stmt->bindValue(2, $date.' 00:00:00');
        $stmt->bindValue(3, $date.' 23:59:59');
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_BOUND);

    }

    public function aprovarVenda($id_venda, $id_vendedor, $data_atual, $data_venda) {

        $query = "UPDATE vendas SET aprovado = 1, data_venda = ?, informacoes = ? WHERE id = ? AND id_vendedor = ? AND aprovado = 2";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1,$data_atual);
        $stmt->bindValue(2,"Data da Venda: ".$data_venda);
        $stmt->bindValue(3,$id_venda);
        $stmt->bindValue(4,$id_vendedor);        

        return $stmt->execute();
    }

    public function recusarVenda($id_venda, $id_vendedor, $motivo_cancelamento, $data_atual, $data_venda) {

        $query = "UPDATE vendas SET aprovado = 0, motivo_cancelamento = ?, cupom = 0, id_cupom = null, valor_desconto = null, data_venda = ?, informacoes = ? WHERE id = ? AND id_vendedor = ? AND aprovado = 2";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $motivo_cancelamento);
        $stmt->bindValue(2, $data_atual);
        $stmt->bindValue(3, "Data da Venda: ".$data_venda);
        $stmt->bindValue(4,$id_venda);
        $stmt->bindValue(5,$id_vendedor);        

        return $stmt->execute();

    }

    public function pendentesAtrasadas($id_vendedor) {

        $data = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('m'),date('d')-5,date('Y')));

        $query = "SELECT id FROM vendas WHERE aprovado = 2 AND id_vendedor = ? AND data_venda <= ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $id_vendedor);
        $stmt->bindValue(2, $data);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function pendentesAtrasadasUsuario($id_usuario) {

        $data = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('m'),date('d')-5,date('Y')));

        $query = "SELECT id FROM vendas WHERE aprovado = 2 AND id_usuario = ? AND data_venda <= ?";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $id_usuario);
        $stmt->bindValue(2, $data);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function cancelarPendentesAtrasadas($id_venda) {

        $query = "UPDATE vendas set aprovado = 0, motivo_cancelamento = 'cancelado pelo sistema devido a atraso' WHERE id = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_venda);        

        return $stmt->execute();

    }

    public function buscarEstoqueProduto($id) {

        $query = "SELECT estoque FROM produtos WHERE id = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function estornarEstoqueVendaCancelada($id_produto, $quantidade, $id_vendedor) {

        $estoque_produto = $this->buscarEstoqueProduto($id_produto);

        $novo_estoque = $estoque_produto['estoque'] + $quantidade;

        if($estoque_produto['estoque'] <= 0) {

            $query = "UPDATE produtos SET estoque = ?, ativo = 1 WHERE id = ? AND id_vendedor = ?";

        } else {

            $query = "UPDATE produtos SET estoque = ? WHERE id = ? AND id_vendedor = ?";

        }

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $novo_estoque);
        $stmt->bindValue(2, $id_produto);
        $stmt->bindValue(3, $id_vendedor);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_BOUND);

    }

    public function buscarVendaItem($id_venda) {

        $query = "SELECT vi.id, vi.id_produto, vi.quantidade FROM vendas v
        INNER JOIN venda_item vi on v.id = vi.id_venda
        WHERE v.id = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_venda);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function buscarCupom($cupom) {

        $query = "SELECT * FROM cupons WHERE cupom = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $cupom);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function cupomUtilizado($id_usuario, $id_cupom) {

        $query = "SELECT * FROM vendas WHERE id_cupom = ? AND id_usuario = ? AND (aprovado = 2 OR aprovado = 1)";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_cupom);
        $stmt->bindValue(2, $id_usuario);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function verificarStatusVenda($id_venda) {

        $query = "SELECT aprovado FROM vendas WHERE id = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_venda);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function devolucaoVenda($id_venda, $id_usuario) {

        $query =
        "SELECT v.id, v.id_usuario, v.id_vendedor, v.data_venda, v.total_produtos, v.cupom, v.valor_desconto, v.valor_entrega , v.total_compra,
        vi.nome_produto, vi.preco, vi.quantidade, vi.total, 
        ve.nome, ve.endereco, ve.telefone 
        FROM vendas v 
        INNER JOIN venda_item vi ON v.id = vi.id_venda
        INNER JOIN vendedores ve ON v.id_vendedor = ve.id
        WHERE v.aprovado = 1 AND v.id = ? AND v.id_usuario = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_venda);
        $stmt->bindValue(2, $id_usuario);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function devolverVenda($id_venda, $data, $motivo, $data_venda_original) {

        $query = "UPDATE vendas SET data_venda = ?, aprovado = 3, motivo_cancelamento = ?, informacoes = ? WHERE id = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $data);
        $stmt->bindValue(2, $motivo);
        $stmt->bindValue(3, $data_venda_original);
        $stmt->bindValue(4, $id_venda);

        return $stmt->execute();

    }

    public function emailVendedor($id_venda) {
        
        $query = 
        "SELECT ve.email FROM vendas v
        INNER JOIN vendedores ve ON v.id_vendedor = ve.id
        WHERE v.id = ?";

        $stmt = $this->db->prepare($query);

        $stmt->bindValue(1, $id_venda);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }


}

?>