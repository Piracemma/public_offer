# Offer

- Marketplace desenvolvido com PHP, HTML, CSS e JS.
- Foi criado uma estrutura MVC manualmente utilizando o autoload do composer para mapear as pastas e namespaces.
- Carrinho de compras completo armazenado na $_SESSION, separado por vendedor, validando a quantidade com o banco de dados e removendo itens excluidos ou sem estoque.
- Variaveis de ambiente no arquivo Vendor/MF/init/Bootstrap.php
- Facil conexão com os Models através do metodo estatico: Container::getModel();
  
  ----
        $venda = Container::getModel('Venda');
        $venda->buscarVendaItem();
  ----
- Arquivos JS e CSS removidos por ser de propriedade dividida.
- Back-end completo.
- Front-end com todo o HTML, sem JS e CSS.
