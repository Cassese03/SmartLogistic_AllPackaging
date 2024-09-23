<html lang="it">
<head>
    <style>
        @page {
            size: 40mm 18mm;
            margin: 0;
        }
    </style>
    <title>QRCode</title>
</head>
<?php if ($scadenza == 'undefined/undefined/') $scadenza = ''; ?>

<body>
<div style="display: flex;height: 16mm;width: 38mm;margin:0;padding-left:5%;overflow: hidden;">
    <img id='barcode'
         src="https://api.qrserver.com/v1/create-qr-code/?data={{$alias.';'.$scadenza.';'.$lotto}}&amp;size=50x50"
         alt="QRCode" title="QRCode" width="50" height="50"/>
    <div style="display:flex;flex-direction:column">
        <div style="padding-left: 5%!important;"><a style="font-size:14px;font-weight: bolder;">{{$alias}}</a></div>
        <div style="padding-left: 5%!important;"><a style="font-size:14px;font-weight: bolder;">{{$lotto}}</a></div>
        <div style="padding-left: 5%!important;"><a
                    style="font-size:14px;font-weight: bolder;">{{$scadenza}}</a></div>
    </div>
</div>
</body>
</html>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', (event) => {
        window.print();
        window.onafterprint = function () {
            top.location.href = '/qrcode';
        };
    });
</script>
