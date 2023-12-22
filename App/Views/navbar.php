<?php
use App\Controllers\CarrinhoController;
use App\Models\Produto;
use MF\Model\Container;
?>
<?php if(isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 1) { ?>
<!-- Carrinho -->
<div class="toggle-cart-bar"></div>
      <div class="cart-bar">
         <div class="cart-bar-header">
            <h6>Carrinho</h6>
            <span class="cart-bar-cls-btn">
            <i class="ti-angle-right nav-cls"></i>
            </span>
         </div>
         <div class="cart-bar-body">
            <?php if(isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) { foreach($_SESSION['carrinho'] as $key => $vendedor) { ?>
            <h6><?= $vendedor['informacao']['nome_vendedor'] ?></h6>
            <?php foreach($vendedor['produtos'] as $key2 => $produto) { 

               $dados = Container::getModel('Produto');

               $dados_produto = $dados->buscarProdutoId($produto['id_produto']);

               $explode_produto = explode(' ',$dados_produto['nome']);
               $implode_produto = implode('_',$explode_produto);
            ?>
            <div class="product-card">
               <div class="row m-0">
                  <div class="product-card-img">
                     <a href="/produto/<?= $dados_produto['id'] ?>/<?= $implode_produto ?>">
                        <img src="<?= HOST_APLIC ?><?= $dados_produto['imagem1'] ?>">
                     </a>
                  </div>
                  <div class="product-card-detail">
                     <h6 class="product-title"><?= $dados_produto['nome'] ?></h6>
                     <p><span><?= $_SESSION['carrinho'][$key]['produtos'][$key2]['quantidade'] ?></span> X</p>
                     <div class="product-quality">
                        
                        <p class="qty">
                           <?php 
                  
                              $preco = $dados_produto['preco'];
                              $precoFormatado = number_format($preco, 2, ',', '');

                              echo $precoFormatado;

                           ?>
                        </p>
                     </div>
                     
                     
                  </div>
               </div>
            </div>
            <?php } ?>
            <hr>
            <?php } } else {?>
               <p class="d-inline-block" style="color: #777;">Seu carrinho está<p class="text-danger d-inline-block">&nbsp;vazio</p> :(</p>
            <?php } ?>
            
         </div>
         <div class="cart-bar-footer">
            <div class="action-btn">
               <a href="/carrinho" class="btn">Ver Carrinho</a>
            </div>
         </div>
      </div>
<!--Navbar normal-->
<header>
         <!-- Brand Nav -->
         <div class="top-brand-section">
            <div class="container">
               <div class="top-brand">
                  <div class="brand-logo mx-auto">
                     <a class= "nab-logo" href= "/" > <img src= "<?= HOST_APLIC ?>assets/img/logo/logo.png" style="width: 100px;" class= "img-fluid" alt= "logotipo" > </a>
                  </div>
               </div>
            </div>
         </div>
         <!-- Navigation menus -->
         <div class="md-nav">
            <div class="container">
               <div class="menu-bar d-flex justify-content-center">
                  <div class="menu-bar-overlay"></div>
                  <nav class="mega-navbar">
                     <div class="nav-title">Navigation <i class="ti-angle-right nav-cls"></i></div>
                     <div class="md-navbar-collapse">
                        <div class="navbar-collapse-bg"></div>
                        <ul class="md-navbar-nav">
                           <li class="md-nav-item md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'inicio'){ ?>ativo<?php } ?>">
                              <a class="md-nav-link link-nav" href="/">
                                 <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                 <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                 <path d="M9 22V12h6v10"></path>
                                 </svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Inicio</span>
                              </a>
                           </li>
                           <li class="md-nav-item dropdown md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'categorias'){ ?>ativo<?php } ?>">
                              <a class="md-nav-link link-nav " href="/categorias">
                                 <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M3 3h7v7H3z"></path>
                                 <path d="M14 3h7v7h-7z"></path>
                                 <path d="M14 14h7v7h-7z"></path>
                                 <path d="M3 14h7v7H3z"></path>
                                 </svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Categorias</span>
                              </a>
                           </li>
                           <li class="md-nav-item md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'pesquisar'){ ?>ativo<?php } ?>">
                           
                              <a href="javascript:void(0)" class="search-toggle-btn md-nav-link link-nav">
                                 <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M11 3a8 8 0 1 0 0 16 8 8 0 1 0 0-16z"></path>
                                 <path d="m21 21-4.35-4.35"></path>
                                 </svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Pesquisar</span>
                              </a>
                              
                           </li>
                           <li class="md-nav-item md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'carrinho'){ ?>ativo<?php } ?>">

                              <a class="toggle-cart md-nav-link link-nav link-nav" href="javascript:void(0)">
                                 <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M9 20a1 1 0 1 0 0 2 1 1 0 1 0 0-2z"></path>
                                 <path d="M20 20a1 1 0 1 0 0 2 1 1 0 1 0 0-2z"></path>
                                 <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                 </svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Carrinho</span>
                              </a>
                                                             
                           </li>
                           <li class="md-nav-item md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'perfil'){ ?>ativo<?php } ?>">
                              <a class="md-nav-link link-nav" href="/perfil">
                                 <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                 <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                 <path d="M12 3a4 4 0 1 0 0 8 4 4 0 1 0 0-8z"></path>
                                 </svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Perfil</span>
                              </a>
                           </li>
                        </ul>
                        
                     </div>
                  </nav>
               </div>
            </div>
         </div>
         <!-- Search panel -->
         <div class="search-panel-full">
            <div class="search-panel">
               <div class="container">
                  <div class="row sidebar-new-product">
                     <div class="col-md-12">
                        <form action="/pesquisar" class="animated" method="GET">
                           <input type="text" name="pesquisa" placeholder="Pesquise na Offer" />
                           <button class="search-btn icon-btn" type="submit" style="border: none; background: none;"><i class="ti-search"></i></button>
                           <a class="search-close icon-btn" href=""><i class="ti-close"></i></a>
                        </form>
                        <div class="find-product">
                           <div class="row">
                              <div class="col-lg-4 col-md-6 search-product animated">
                                 <a href="/vendedores">
                                    <div class="product-card">
                                       <div class="card-image">
                                          <div class="new-product-img">
                                             <img class="p-2" src="assets/img/blog/vendedores.png" alt="vendedores">
                                          </div>
                                       </div>
                                       <div class="card-info">
                                          <div class="product-slider-right">
                                             <h2 class="new-product-name">
                                                <a href="/vendedores">Vendedores</a>
                                             </h2>
                                          </div>
                                       </div>
                                    </div>
                                 </a>
                              </div>
                              <div class="col-lg-4 col-md-6 search-product animated">
                                 <a href="/categorias?categoria=moda">
                                    <div class="product-card">
                                       <div class="card-image">
                                          <div class="new-product-img">
                                             <img class="p-2" src="assets/img/blog/moda.png" alt="moda">
                                          </div>
                                       </div>
                                       <div class="card-info">
                                          <div class="product-slider-right">
                                             <h2 class="new-product-name">
                                                <a href="/categorias?categoria=moda">Moda</a>
                                             </h2>
                                          </div>
                                       </div>
                                    </div>
                                 </a>
                              </div>
                              <div class="col-lg-4 col-md-6 search-product animated">
                                 <a href="/categorias?categoria=eletronicos">
                                    <div class="product-card">
                                       <div class="card-image">
                                          <div class="new-product-img">
                                             <img class="p-2" src="assets/img/blog/eletronicos.png" alt="eletronicos">
                                          </div>
                                       </div>
                                       <div class="card-info">
                                          <div class="product-slider-right">
                     
                                             <h2 class="new-product-name">
                                                <a href="/categorias?categoria=eletronicos">Eletronicos</a>
                                             </h2>
                                          </div>
                                       </div>
                                    </div>
                                 </a>
                              </div>
                              <div class="col-lg-4 col-md-6 search-product animated">
                                 <a href="/categorias?categoria=presente_papelaria">
                                    <div class="product-card">
                                       <div class="card-image">
                                          <div class="new-product-img">
                                             <img class="p-2" src="assets/img/blog/presentes_papelaria.png" alt="presentes_papelaria">
                                          </div>
                                       </div>
                                       <div class="card-info">
                                          <div class="product-slider-right">
                                             
                                             <h2 class="new-product-name">
                                                <a href="/categorias?categoria=presente_papelaria">Presentes/Papelaria</a>
                                             </h2>
                                          </div>
                                       </div>
                                    </div>
                                 </a>
                              </div>
                              <div class="col-lg-4 col-md-6 search-product animated">
                                 <a href="/categorias?categoria=cama_mesa_banho">
                                    <div class="product-card">
                                       <div class="card-image">
                                          <div class="new-product-img">
                                             <img class="p-2" src="assets/img/blog/cama_mesa_banho.png" alt="cama_mesa_banho">
                                          </div>
                                       </div>
                                       <div class="card-info">
                                          <div class="product-slider-right">
                                             <h2 class="new-product-name">
                                                <a href="/categorias?categoria=cama_mesa_banho">Cama/Mesa/Banho</a>
                                             </h2>
                                          </div>
                                       </div>
                                    </div>
                                 </a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </header>







<!-- Navbar responsive -->
<div class="nav-toolbar">

         <div class="container">
            <div class="nav-panel">
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'inicio'){ ?>ativo<?php } ?>">
                  <a href="/">
                     <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                     <path d="M9 22V12h6v10"></path>
                     </svg>
                  </a>
               </div>
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'categorias'){ ?>ativo<?php } ?>">
                  <a href="/categorias">
                     <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="M3 3h7v7H3z"></path>
                     <path d="M14 3h7v7h-7z"></path>
                     <path d="M14 14h7v7h-7z"></path>
                     <path d="M3 14h7v7H3z"></path>
                     </svg>
                  </a>
               </div>
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'pesquisar'){ ?>ativo<?php } ?>">
                  <a href="javascript:void(0)" class="search-small-media search-toggle-btn">
                     <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="M11 3a8 8 0 1 0 0 16 8 8 0 1 0 0-16z"></path>
                     <path d="m21 21-4.35-4.35"></path>
                     </svg>
                  </a>
               </div>
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'carrinho'){ ?>ativo<?php } ?>">
                  <a href="javascript:void(0)" class="toggle-cart">
                     <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="M9 20a1 1 0 1 0 0 2 1 1 0 1 0 0-2z"></path>
                     <path d="M20 20a1 1 0 1 0 0 2 1 1 0 1 0 0-2z"></path>
                     <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                     </svg>
                  </a>
               </div>
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'perfil'){ ?>ativo<?php } ?>">
                  <a href="/perfil">
                     <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                     <path d="M12 3a4 4 0 1 0 0 8 4 4 0 1 0 0-8z"></path>
                     </svg>
                  </a>
               </div>
            </div>
         </div>
</div>


<?php } else if(isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 2) { ?>

      
<!--Navbar normal-->
<header>
         <!-- Brand Nav -->
         <div class="top-brand-section">
            <div class="container">
               <div class="top-brand">
                  <div class="brand-logo mx-auto">
                     <a class= "nab-logo" href= "index.html" > <img src= "<?= HOST_APLIC ?>assets/img/logo/logo.png" style="width: 100px;" class= "img-fluid" alt= "logotipo" > </a>
                  </div>
               </div>
            </div>
         </div>
         <!-- Navigation menus -->
         <div class="md-nav">
            <div class="container-fluid">
               <div class="menu-bar d-flex justify-content-center">
                  <div class="menu-bar-overlay"></div>
                  <nav class="mega-navbar">
                     <div class="nav-title">Navigation <i class="ti-angle-right nav-cls"></i></div>
                     <div class="md-navbar-collapse">
                        <div class="navbar-collapse-bg"></div>
                        <ul class="md-navbar-nav">
                           <li class="md-nav-item md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'inicio'){ ?>ativo<?php } ?>">
                              <a class="md-nav-link link-nav" href="/vendedor">
                                 <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                 <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                 <path d="M9 22V12h6v10"></path>
                                 </svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Inicio</span>
                              </a>
                           </li>
                           <li class="md-nav-item md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'relatorio'){ ?>ativo<?php } ?>">
                              <a class="md-nav-link link-nav" href="/relatorio">
                                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon-bar"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Relatorio</span>
                              </a>
                           </li>
                           <li class="md-nav-item md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'cadastro de produtos'){ ?>ativo<?php } ?>">
                           
                              <a href="/cadastro_produto" class="md-nav-link link-nav">
                                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon-bar"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Cadastrar Produto</span>
                              </a>
                              
                           </li>
                           <li class="md-nav-item md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'meus produtos'){ ?>ativo<?php } ?>">

                              <a class="md-nav-link link-nav" href="/meus_produtos">
                                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon-bar"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Meus Produtos</span>
                              </a>
                                                             
                           </li>
                           <li class="md-nav-item md-dropdown <?php if(isset($this->view->pagina) && $this->view->pagina == 'editar perfil'){ ?>ativo<?php } ?>">
                              <a class="md-nav-link link-nav" href="/editar_perfil">
                                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon-bar"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                 <span class="nav-texto d-md-inline d-sm-none d-none d-lg-inline-block">Editar Perfil</span>
                              </a>
                           </li>
                        </ul>
                        
                     </div>
                  </nav>
               </div>
            </div>
         </div>
      </header>







<!-- Navbar responsive -->
<div class="nav-toolbar">

         <div class="container">
            <div class="nav-panel">
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'inicio'){ ?>ativo<?php } ?>">
                  <a href="/">
                     <svg class="icon-bar" fill="none" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                     <path d="M9 22V12h6v10"></path>
                     </svg>
                  </a>
               </div>
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'relatorio'){ ?>ativo<?php } ?>">
                  <a href="/relatorio">
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon-bar"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
                  </a>
               </div>
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'cadastro_produto'){ ?>ativo<?php } ?>">
                  <a href="/cadastro_produto">
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon-bar"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                  </a>
               </div>
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'meus_produtos'){ ?>ativo<?php } ?>">
                  <a href="/meus_produtos">
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon-bar"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                  </a>
               </div>
               <div class="nav-panel-icons <?php if(isset($this->view->pagina) && $this->view->pagina == 'editar_perfil'){ ?>ativo<?php } ?>">
                  <a href="/editar_perfil">
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" class="icon-bar"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                  </a>
               </div>
            </div>
         </div>
</div>
      

<?php } ?>

