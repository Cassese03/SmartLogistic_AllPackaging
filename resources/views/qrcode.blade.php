<!doctype html>
<html lang="en" class="md">

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

<body style="height: 100vh;" class="color-theme-blue push-content-right theme-light">
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


        <header class="row m-0 fixed-header">
            <div class="left">
                <a href="<?php echo URL::asset('') ?>"><i class="material-icons">keyboard_backspace</i></a>
            </div>
            <div class="col center">
                <a href="#" class="logo" style="text-decoration: none;">Crea QRCode </a>
            </div>
            <div class="right">
                <a style="padding-left:20px;" href="/"><i class="material-icons">home</i></a>
            </div>
        </header>
        <div class="page-content">

            <div class="content-sticky-footer">
                <div class="row mx-0" style="margin-top: 2%">

                    <div class="col-xl-4 col-sm-4 col-xs-12">
                        <label>Alias</label>
                        <input class="form-control" type="text" id="alias" placeholder="Alias">
                    </div>
                    <div class="col-xl-4 col-sm-4 col-xs-12">
                        <label>Data Scadenza</label>
                        <input class="form-control" type="date" id="scadenza" placeholder="Scadenza">
                    </div>
                    <div class="col-xl-4 col-sm-4 col-xs-12">
                        <label>Lotto</label>
                        <input class="form-control" type="text" id="lotto" placeholder="Lotto">
                    </div>

                    <button class="btn-primary" style="width: 100%;margin:3% 5% 2% 5%;" onclick="generateBarCode()">Invia
                        Dati
                    </button>

                </div>
                <div style="width:100vw;">
                    <h3 style="text-align: center;"> Ultime Etichette</h3>
                    <div class="row">
                        @foreach($ultimi as $u)
                            <div class="col-2" style="margin-bottom: 1%;"></div>
                            <div class="col-8" style="margin-bottom: 1%;" id="{{$u->Id_xQRCode}}"
                                 onclick="replayQRCode('{{$u->Id_xQRCode}}')">
                                <div class="row">
                                    <input class="col-4 form-control" type="text" readonly
                                           value="{{$u->Codice}}"
                                           id="codice_{{$u->Id_xQRCode}}">
                                    <input class="col-4 form-control" type="text" readonly
                                           value="{{$u->Scadenza}}"
                                           id="scadenza_{{$u->Id_xQRCode}}">
                                    <input class="col-4 form-control" type="text" readonly
                                           value="{{$u->Lotto}}"
                                           id="lotto_{{$u->Id_xQRCode}}">
                                </div>
                            </div>
                            <div class="col-2" style="margin-bottom: 1%;"></div>
                        @endforeach
                    </div>
                </div>


                <!-- Optional JavaScript -->
                <!-- jQuery first, then Popper.js, then Bootstrap JS -->
                <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
                <script src="/js/jquery-3.2.1.min.js"></script>
                <script src="/js/popper.min.js"></script>
                <script src="/vendor/bootstrap-4.1.3/js/bootstrap.min.js"></script>
                <script src="/vendor/cookie/jquery.cookie.js"></script>
                <script src="/vendor/sparklines/jquery.sparkline.min.js"></script>
                <script src="/vendor/circle-progress/circle-progress.min.js"></script>
                <script src="/vendor/swiper/js/swiper.min.js"></script>
                <script src="/js/main.js"></script>
                <script type="text/javascript">
                    function convertDateFormat(dateString) {
                        // Dividi la stringa originale nel formato "dd/MM/yyyy"
                        var parts = dateString.split('/');
                        var day = parts[0];
                        var month = parts[1];
                        var year = parts[2];

                        // Crea la nuova stringa nel formato "yyyy-MM-dd"
                        var formattedDate = year + '-' + month + '-' + day;
                        return formattedDate;
                    }

                    function replayQRCode(id) {
                        codice = document.getElementById('codice_' + id).value;
                        scadenza = document.getElementById('scadenza_' + id).value;
                        lotto = document.getElementById('lotto_' + id).value;

                        scadenza = convertDateFormat(scadenza);

                        $('#alias').val(codice);
                        $('#scadenza').val(scadenza);
                        $('#lotto').val(lotto);
                    }

                    function generateBarCode() {
                        var nric = $('#alias').val();
                        if (nric != '' || $('#alias').val() != undefined) {
                            scadenza = $('#scadenza').val();
                            if (scadenza != '' || $('#scadenza').val() != undefined) {
                                const parts = scadenza.split('-');
                                scadenza = `${parts[2]}/${parts[1]}/${parts[0]}`;

                                lotto = $('#lotto').val();
                                if (lotto != '' || $('#lotto').val() != undefined) {
                                    nric = nric.replaceAll(';', 'punto');
                                    nric = nric.replaceAll('/', 'slash');
                                    scadenza = scadenza.replaceAll(';', 'punto');
                                    scadenza = scadenza.replaceAll('/', 'slash');
                                    lotto = lotto.replaceAll(';', 'punto');
                                    lotto = lotto.replaceAll('/', 'slash');
                                    $.ajax({
                                        url: "<?php echo URL::asset('ajax/barcode_add') ?>/" + nric + '/' + scadenza + '/' + lotto,
                                    }).done(function (result) {
                                        top.location.href = '/resultqrcode/' + nric + '/' + scadenza + '/' + lotto;
                                    });
                                }
                            }
                        }
                    }
                </script>
</body>

</html>

