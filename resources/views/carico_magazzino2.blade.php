<!doctype html>
<html lang="en" class="md">
<?php $dest = $documenti; ?>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, user-scalable=no, shrink-to-fit=no, viewport-fit=cover">
    <link rel="apple-touch-icon" href="img/icona_arca.png">
    <link rel="icon" href="img/icona_arca.png">
    <link rel="stylesheet" href="/vendor/bootstrap-4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="/vendor/materializeicon/material-icons.css">
    <link rel="stylesheet" href="/vendor/swiper/css/swiper.min.css">
    <link id="theme" rel="stylesheet" href="/css/style.css" type="text/css">
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

    <!-- page main start -->
    <div class="page">
        <form class="searchcontrol">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text close-search"><i class="material-icons">keyboard_backspace</i>
                    </button>
                </div>
                <input type="text" id="cerca" class="form-control border-0" placeholder="Cerca Cliente..."
                       aria-label="Username">
                <input type="hidden" id="dest" value="<?php echo $dest; ?>">
            </div>
        </form>
        <header class="row m-0 fixed-header">
            <div class="left">
                <a style="padding-left:20px;" href="/magazzino/attivo"><i class="material-icons">arrow_back_ios</i></a>
            </div>
            <div class="col center">
                <a href="#" class="logo">
                    <figure><img src="/img/logo_arca.png" alt=""></figure>
                    Scelta Cliente</a>
            </div>
            <div class="right">
                <a href="javascript:void(0)" class="searchbtn" onclick="document.getElementById('cerca').focus();"><i
                        class="material-icons">search</i></a>
            </div>
        </header>

        <div class="page-content">
            <div class="content-sticky-footer">

                <div class="background bg-170"><img src="/img/background.png" alt=""></div>
                <div class="w-100">
                    <h1 class="text-center text-white title-background">Scegli un
                        Cliente<br><small><?php echo $documenti ?></small></h1>
                </div>

                <ul class="list-group" id="ajax" style="max-height:500px;">
                    @if(sizeof($fornitori)<=0)
                        <li class="list-group-item"><h5 style="text-align: center">Nessun Cliente</h5></li>
                    @endif
                    <?php foreach ($fornitori as $f){ ?>
                    <li class="list-group-item">
                        <a <?php if (str_replace(' ', '', $documenti) == 'LPL' || str_replace(' ', '', $documenti) == 'PRV' || str_replace(' ', '', $documenti) == 'DDT' || str_replace(' ', '', $documenti) == 'RCF') echo 'href="/magazzino/carico03/' . $f->Id_CF . '/' . $documenti . '"'; else echo 'href="/magazzino/carico3/' . $f->Id_CF . '/' . $documenti . '"'; ?> class="media">
                            <div class="media-body">
                                <h5><?php echo $f->Cd_CF ?> (<?php echo number_format($f->doc_da_lavorare,0); ?>)</h5>
                                <p>Nome: <?php echo $f->Descrizione ?></p>
                            </div>
                        </a>
                    </li>

                    <?php } ?>

                </ul>


            </div>
        </div>
    </div>
    <!-- page main ends -->

</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/js/popper.min.js"></script>
<script src="/vendor/bootstrap-4.1.3/js/bootstrap.min.js"></script>
<script src="/vendor/cookie/jquery.cookie.js"></script>
<script src="/vendor/sparklines/jquery.sparkline.min.js"></script>
<script src="/vendor/circle-progress/circle-progress.min.js"></script>
<script src="/vendor/swiper/js/swiper.min.js"></script>
<script src="/js/main.js"></script>
</body>

</html>
<script type="text/javascript">


    $('#cerca').on('keydown', function (e) {
        if (e.which == 13) {
            cerca_fornitore_new($('#cerca').val());
            e.preventDefault();
        }
    });

    function cerca_fornitore_new(testo) {

        dest = document.getElementById('dest').value;

        $.ajax({
            url: "<?php echo URL::asset('ajax/cerca_fornitore_new') ?>/" + encodeURIComponent(testo) + "/" + dest,
            context: document.body
        }).done(function (result) {
            $('#ajax').html(result);
        });

    }

</script>