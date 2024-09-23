<?php
$cliente = DB::SELECT('SELECT * FROM CF WHERE Cd_CF = \''.$id_dotes->Cd_CF.'\'')[0];
$contatto = DB::SELECT('SELECT * FROM CFContatto WHERE Cd_CF = \''.$id_dotes->Cd_CF.'\'')[0];
$dorig = DB::SELECT('SELECT * FROM DORIG WHERE Id_DOTes = \''.$id_dotes->Id_DoTes.'\'');
DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = $id_dotes->Id_DoTes exec asp_DO_End $id_dotes->Id_DoTes");
$date = date('d/m/Y',strtotime($id_dotes->DataDoc)) ;
$pagamento =  DB::SELECT('SELECT * FROM PG WHERE Cd_PG = \''.$id_dotes->Cd_PG.'\'')[0];
$dototali = DB::SELECT('SELECT * FROM DOTotali WHERE Id_DoTes = \''.$id_dotes->Id_DoTes.'\'')[0];
$creazione = date('d/m/Y H:i:s',strtotime($id_dotes->TimeIns));
$porto = DB::SELECT('SELECT * FROM DOPorto where Cd_DOPorto =\''.$id_dotes->Cd_DoPorto.'\'');
if(sizeof($porto) > 0)
    $porto = $porto[0]->Descrizione;
$trasporto = DB::SELECT('SELECT * FROM DOTrasporto where Cd_DOTrasporto =\''.$id_dotes->Cd_DoTrasporto.'\'');
if(sizeof($trasporto) > 0)
    $trasporto = $trasporto[0]->Descrizione;
$data_trasporto = ($id_dotes->TrasportoDataora) ? $id_dotes->TrasportoDataora:'';
if($data_trasporto != '')
    $data_trasporto = date('d-m-y',strtotime($data_trasporto));
$spedizione= DB::SELECT('SELECT * FROM DOSped where Cd_DOSped =\''.$id_dotes->Cd_DoSped.'\'');
if(sizeof($spedizione) > 0)
    $spedizione = $spedizione[0]->Descrizione;
$aspetto_beni = DB::SELECT('SELECT * FROM DOAspBene where Cd_DOAspBene =\''.$id_dotes->Cd_DoAspBene.'\'');
if(sizeof($aspetto_beni) > 0)
    $aspetto_beni = $aspetto_beni[0]->Descrizione;
//$banca = 'IT-62-C-C0200876312-000401045594 BANCA UNICREDIT';
$banca = DB::SELECT('SELECT * FROM Banca where Cd_CGConto = \''.$id_dotes->Cd_CGConto_Banca.'\' ');
$banca2 = $banca[0]->Iban;
$banca2 .= ' - ';
$banca2 .= $banca[0]->Descrizione;
$banca  = $banca2;

$html = '<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css" media="print">
    @page
        {
            size: auto;   /* auto is the initial value */
            margin: 0mm;  /* this affects the margin in the printer settings */
        }
        .container {
            position: relative;
            text-align: center;
        }
       body{
            margin: 0px;
            padding: 0;
            width: 21cm;
            height: 29.7cm;
       }
       label{
            font-family: Tahoma;
       }
    </style>
</head>
<body>

<div class="container">
    <img  src="';$html.= URL::asset('img/RCF.jpg');$html .= '" alt="DDT" style="width:99%;z-index:1;filter: grayscale(99%);">
   <label style="position: absolute;top: 260px;left: 40px;z-index:10;font-size:12px">Utente</label>
    <label style="position: absolute;top: 298px;left: 35px;z-index:10;font-size:11px">'.$id_dotes->Cd_CF.'</label>
    <label style="position: absolute;top: 840px;left: 36px;z-index:10;font-size:12px">'; $html .= ($trasporto) ? $trasporto:''; $html.='</label>
    <label style="position: absolute;top: 870px;left: 36px;z-index:10;font-size:12px">'; $html .= ($spedizione) ? $spedizione:''; $html.='</label>
    <label style="position: absolute;top: 870px;left: 160px;z-index:10;font-size:12px">'; $html .= ($porto) ? $porto:''; $html.='</label>
    <label style="position: absolute;top: 870px;left: 300px;z-index:10;font-size:12px">'; $html .= ($data_trasporto) ? $data_trasporto:''; $html.='</label>
    <label style="position: absolute;top: 840px;left: 180px;z-index:10;font-size:12px">'; $html .= ($aspetto_beni) ? $aspetto_beni:''; $html.='</label>
    <label style="position: absolute;top: 298px;left: 85px;z-index:10;font-size:12px">'.$cliente->PartitaIva.'</label>
    <label style="position: absolute;top: 60px;left: 450px;z-index:10;font-size:12px">'.$cliente->Descrizione.'</label>
    <label style="position: absolute;top: 80px;left: 450px;z-index:10;font-size:12px">'.$cliente->Indirizzo.'</label>
    <label style="position: absolute;top: 100px;left: 450px;z-index:10;font-size:12px">'.$cliente->Cap.' - '.$cliente->Localita .'</label>
    <label style="position: absolute;top: 300px;left: 180px;z-index:10;font-size:12px">'.$cliente->CodiceFiscale.'</label>
    <label style="position: absolute;top: 1090px;left: 550px;z-index:10;font-size:12px"> Data di Creazione : '.$creazione.'</label>
    <label style="position: absolute;top: 300px;left: 580px;z-index:10;font-size:12px;font-weight: bold">'.$id_dotes->NumeroDoc.'</label>
    <label style="position: absolute;top: 300px;left: 300px;z-index:10;font-size:12px">'; $html .= ($contatto->Telefono) ? $contatto->Telefono:''; $html.='</label>
    <label style="position: absolute;top: 300px;left: 640px;z-index:10;font-size:12px;font-weight: bold">'.$date.'</label>
    <label style="position: absolute;top: 330px;left: 36px;z-index:10;font-size:12px">'.$pagamento->Descrizione.'</label>
    <label style="position: absolute;top: 330px;left:300px;z-index:10;font-size:12px">'.$banca.'</label>
    <label style="text-align: left;position: absolute;top: 980px;left: 620px;z-index:10;font-size:14px;font-weight: bold">EUR.</label>
    <label style="text-align: right;position: absolute;top: 980px;left: 690px;z-index:10;font-size:14px;font-weight: bold">'.number_format($dototali->TotDocumentoV,2,',','.').'</label>
    <label style="position: absolute;top: 335px;left: 300px;z-index:10;font-size:12px">'; $html.='</label>
    <div style="text-align:left;position: absolute;top: 370px;left: 35px;z-index:10;font-size:12px">';
foreach ($dorig as $d){
    $html.= '<label>'.$d->Cd_AR.'</label><br>';
}
$html .='
    </div>
        <div style="text-align:left;position: absolute;top: 370px;left: 160px;z-index:10;font-size:12px">';
foreach ($dorig as $d){
    $html.= '<label>'.substr($d->Descrizione,0,53).'</label><br>';
}
$html .='
    </div>
        <div style="text-align:left;position: absolute;top: 370px;left: 465px;z-index:10;font-size:12px">';
foreach ($dorig as $d){
    $html.= '<label>'.$d->Cd_ARMisura.'</label><br>';
}
$html .='
    </div>
        <div style="text-align:right;position: absolute;top: 370px;left: 515px;z-index:10;font-size:12px">';
foreach ($dorig as $d){
    $html.= '<label>'.number_format($d->Qta,2,',','.').'</label><br>';
}
$html .='
    </div>
        <div style="text-align:right;position: absolute;top: 370px;left: 570px;z-index:10;font-size:12px">';
foreach ($dorig as $d){
    $html.= '<label>'.number_format(floatval($d->PrezzoUnitarioV)+floatval((($d->PrezzoUnitarioV/100)*22)),4,',','.').'</label><br>';
}
$html .='
    </div>
        <div style="text-align:right;position: absolute;top: 370px;left: 625px;z-index:10;font-size:12px">';
foreach ($dorig as $d){
    if($d->ScontoRiga != '')
        $html.= '<label>'.$d->ScontoRiga.'</label><br>';
}
$html .='
    </div>
        <div style="text-align:right;position: absolute;top: 370px;left: 690px;z-index:10;font-size:12px">';
foreach ($dorig as $d){
    $html.= '<label>'.number_format(floatval($d->PrezzoTotaleV)+floatval((($d->PrezzoTotaleV/100)*22)),2,',','.').'</label><br>';
}
$html .='
    </div>
        <div style="text-align:right;position: absolute;top: 370px;left: 740px;z-index:10;font-size:12px">';
foreach ($dorig as $d){/*
    if($d->Cd_Aliquota != '')
        $html.= '<label>'.$d->Cd_Aliquota.'</label><br>';*/
}
$html .='
    </div>


    <br>
</div>

</body>
<script type="text/javascript">
        window.print();
</script>
</html>';
echo $html;exit();
//<label style="position: absolute;top: 335px;left: 300px;z-index:10;font-size:12px">'; $html .= ($id_dotes->Cd_CGConto_Banca) ? $id_dotes->Cd_CGConto_Banca:''; $html.='</label>

?>
