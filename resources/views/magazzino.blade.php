<!doctype html>
<html lang="en" class="md">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, user-scalable=no, shrink-to-fit=no, viewport-fit=cover">
    <link rel="apple-touch-icon" href="img/icona_arca.png">
    <link rel="icon" href="img/icona_arca.png">
    <link rel="stylesheet" href="vendor/bootstrap-4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/materializeicon/material-icons.css">
    <link rel="stylesheet" href="vendor/swiper/css/swiper.min.css">
    <link id="theme" rel="stylesheet" href="css/style.css" type="text/css">
    <title>SMART LOGISTIC</title>
</head>

<body class="color-theme-blue push-content-right theme-light">
<div class="loader justify-content-center ">
    <div class="maxui-roller align-self-center">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>
<div class="wrapper">
    <!-- sidebar left start -->
    <div class="sidebar sidebar-left">
        <div class="profile-link">
            <a href="#" class="media">
                <div class="w-auto h-100">
                    <figure class="avatar avatar-40"><img src="img/user1.png" alt=""></figure>
                </div>
                <div class="media-body">
                    <h5>John Doe <span class="status-online bg-success"></span></h5>
                    <p>India</p>
                </div>
            </a>
        </div>
        <nav class="navbar">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a href="" class="item-link item-content dropdown-toggle" id="navbarDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="item-title">
                            <i class="material-icons">menu</i> Menu
                        </div>
                    </a>
                    <div class="dropdown-menu">
                        <a href="javascript:void(0)" class="sidebar-close  dropdown-item">
                            Menu Overlay (This)
                        </a>
                        <a href="#" class="sidebar-close dropdown-item menu-right">
                            Push Content
                        </a>
                        <a href="javascript:void(0)" class="sidebar-close dropdown-item popup-open" data-toggle="modal"
                           data-target="#fullscreenmenu">
                            Full Screen
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="index.html" class="sidebar-close">
                        <div class="item-title">
                            <i class="material-icons">poll</i> Dashboard
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="projects.html" class="sidebar-close">
                        <div class="item-title">
                            <i class="material-icons">add_shopping_cart</i> Projects
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="project-detail.html" class="sidebar-close">
                        <div class="item-title">
                            <i class="material-icons">filter_none</i> Project Details
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="user-profile.html" class="sidebar-close">
                        <div class="item-title">
                            <i class="material-icons">person</i> User Profile
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="aboutus.html" class="sidebar-close">
                        <div class="item-title">
                            <i class="material-icons">domain</i> About
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="colorscheme.html" class="sidebar-close">
                        <div class="item-title">
                            <i class="material-icons">format_color_fill</i> Color Scheme
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="component.html" class="sidebar-close">
                        <div class="item-title">
                            <i class="material-icons">pages</i> Component
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="404.html" class="sidebar-close">
                        <div class="item-title">
                            <i class="material-icons">warning</i> Default Route (404)
                        </div>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="profile-link text-center">
            <a href="login.html" class="btn btn-outline-white btn-block px-3">Logout</a>
        </div>
    </div>
    <!-- sidebar left ends -->

    <!-- sidebar right start -->
    <div class="sidebar sidebar-right">
        <header class="row m-0 fixed-header">
            <div class="left">
                <a href="javascript:void(0)" class="menu-left-close"><i
                        class="material-icons">keyboard_backspace</i></a>
            </div>
            <div class="col center">
                <a href="" class="logo">Best Rated</a>
            </div>
        </header>
        <div class="page-content text-white">
            <div class="row mx-0 mt-3">
                <div class="col">
                    <div class="card bg-none border-0 shadow-none">
                        <div class="card-body userlist_large">
                            <div class="media">
                                <figure class="avatar avatar-120 rounded-circle my-2">
                                    <img src="img/user1.png" alt="user image">
                                </figure>
                                <div class="media-body">
                                    <h4 class="mt-0 text-white">Max Johnsons</h4>
                                    <p class="text-white">VP, Maxartkiller Co. Ltd., India</p>
                                    <h5 class="text-warning my-2">
                                        <i class="material-icons">star</i>
                                        <i class="material-icons">star</i>
                                        <i class="material-icons">star</i>
                                        <i class="material-icons">star</i>

                                    </h5>
                                    <div class="mb-0">MobileUX is HTML template based on Bootstrap 4.1 framework. This
                                        html template can be used in various business domains like Manufacturing,
                                        inventory, IT, administration etc.
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- sidebar right ends -->

    <!-- fullscreen menu start -->

    <div class="modal fade popup-fullmenu" id="fullscreenmenu" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content fullscreen-menu">
                <div class="modal-header">
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <a href="/profile/" class="block user-fullmenu popup-close">
                        <figure>
                            <img src="img/user1.png" alt="">
                        </figure>
                        <div class="media-content">
                            <h6>John Doe<br><small>India</small></h6>
                        </div>
                    </a>
                    <br>
                    <div class="row mx-0">
                        <div class="col">
                            <div class="menulist">
                                <ul>
                                    <li>
                                        <a href="index.html" class="popup-close">
                                            <div class="item-title">
                                                <i class="icon material-icons md-only">poll</i> Dashboard
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="projects.html" class="popup-close">
                                            <div class="item-title">
                                                <i class="icon material-icons md-only">add_shopping_cart</i> Projects
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="project-detail.html" class="popup-close">
                                            <div class="item-title">
                                                <i class="icon material-icons md-only">filter_none</i> Details
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="user-profile.html" class="popup-close">
                                            <div class="item-title">
                                                <i class="icon material-icons md-only">person</i> Profile
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="aboutus.html" class="popup-close">
                                            <div class="item-title">
                                                <i class="icon material-icons md-only">domain</i> About
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="component.html" class="popup-close">
                                            <div class="item-title">
                                                <i class="icon material-icons md-only">pages</i> Component
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row mx-0">
                        <div class="col">
                            <a href="login.html" class="rounded btn btn-outline-white text-white popup-close">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- fullscreen menu ends -->

    <!-- page main start -->
    <div class="page">
        <form class="searchcontrol">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text close-search"><i class="material-icons">keyboard_backspace</i>
                    </button>
                </div>
                <input type="email" class="form-control border-0" placeholder="Cerca Articolo..." aria-label="Username">
            </div>
        </form>
        <header class="row m-0 fixed-header">
            <div class="left">
                <a style="padding-left:20px;" href="/"><i class="material-icons">arrow_back_ios</i></a>
            </div>
            <div class="col center">
                <a href="#" class="logo">
                    <figure><img src="img/logo_arca.png" alt=""></figure>
                    Magazzino</a>
            </div>
            <div class="right">
                <a style="padding-left:20px;" href="/"><i class="material-icons">home</i></a>
            </div>
        </header>

        <div class="page-content">
            <div class="content-sticky-footer">

                <div class="background bg-170"><img src="img/background.png" alt=""></div>
                <div class="w-100">
                    <h1 class="text-center text-white title-background">Gestione Magazzino</h1>
                </div>

                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="<?php echo URL::asset('magazzino/attivo') ?>" class="media">
                            <div class="media-body">
                                <h5>Ciclo Attivo</h5>
                                <p>Effettua un Scarico di Magazzino a Cliente</p>
                            </div>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo URL::asset('magazzino/passivi') ?>" class="media">
                            <div class="media-body">
                                <h5>Ciclo Passivo</h5>
                                <p>Effettua un Ordine di Carico di Magazzino </p>
                            </div>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo URL::asset('magazzino/altri') ?>" class="media">
                            <div class="media-body">
                                <h5>Trasferimento</h5>
                                <p>Operazioni per movimentazione merce magazzini interni</p>
                            </div>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="<?php echo URL::asset('magazzino/cerca_documento') ?>" class="media">
                            <div class="media-body">
                                <h5>Cerca Documento</h5>
                                <p>Cerca documento tramite identificativo.</p>
                            </div>
                        </a>
                    </li>
                    <!--

                    <li class="list-group-item">
                        <a href="<?php echo URL::asset('magazzino/altri') ?>" class="media">
                            <div class="media-body">
                                <h5>Trasferimento</h5>
                                <p>Operazioni svolte con Altri Tipi di Documenti</p>
                            </div>
                        </a>
                    </li>

                    <li class="list-group-item">
                        <a href="<?php echo URL::asset('magazzino/carico') ?>" class="media">
                            <div class="media-body">
                                <h5>Ordine</h5>
                                <p>Effettua un Ordine di Carico di Magazzino Da Fornitore</p>
                            </div>
                        </a>
                    </li>

                    <li class="list-group-item">
                        <a href="<?php echo URL::asset('magazzino/carico1') ?>" class="media">
                            <div class="media-body">
                                <h5>Carico</h5>
                                <p>Effettua un Carico di Magazzino Da Fornitore</p>
                            </div>
                        </a>
                    </li>

                    <li class="list-group-item">
                        <a href="<?php echo URL::asset('magazzino/scarico') ?>" class="media">
                            <div class="media-body">
                                <h5>Preventivo</h5>
                                <p>Effettua un Preventivo di Scarico di Magazzino Verso Clienti</p>
                            </div>
                        </a>
                    </li>

                    <li class="list-group-item">
                        <a href="<?php echo URL::asset('magazzino/scarico1') ?>" class="media">
                            <div class="media-body">
                                <h5>Scarico</h5>
                                <p>Effettua un Scarico di Magazzino Verso Clienti</p>
                            </div>
                        </a>
                    </li>
-->

                    <li class="list-group-item">
                        <a class="media" href="<?php echo URL::asset('magazzino/inventario') ?>">
                            <div class="media-body">
                                <h5>Inventario</h5>
                                <p>Effettua Inventario Rettificando le Quantità</p>
                            </div>
                        </a>
                    </li>


                </ul>

            </div>
        </div>
    </div>
    <!-- page main ends -->

</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="vendor/bootstrap-4.1.3/js/bootstrap.min.js"></script>
<script src="vendor/cookie/jquery.cookie.js"></script>
<script src="vendor/sparklines/jquery.sparkline.min.js"></script>
<script src="vendor/circle-progress/circle-progress.min.js"></script>
<script src="vendor/swiper/js/swiper.min.js"></script>
<script src="js/main.js"></script>
</body>

</html>
