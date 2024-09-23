<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Else_;
use Spatie\GoogleCalendar\Event;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use NGT\Barcode\GS1Decoder\Decoder;
use Symfony\Component\VarDumper\Cloner\Data;


/**
 * Controller principale del webticket
 * Class HomeController
 * @package App\Http\Controllers
 */
class AjaxController extends Controller
{
    public function barcode_add($codice, $scadenza, $lotto)
    {
        $codice = str_replace('slash', '/', $codice);
        $lotto = str_replace('slash', '/', $lotto);
        $scadenza = str_replace('slash', '/', $scadenza);

        DB::table('xQRCode')->insertGetId(['Codice' => $codice, 'Scadenza' => $scadenza, 'Lotto' => $lotto]);

        $rowCount = DB::table('xQRCode')->count();

        if ($rowCount > 50) {
            DB::table('xQRCode')
                ->orderBy('id')
                ->limit($rowCount - 50)
                ->delete();
        }
    }

    public function inserisci_numero_colli($id_dotes)
    {
        try {
            DB::update('UPDATE DOTES SET Colli = (SELECT ISNULL(SUM(Qta),0) from DORig where Id_dotes = ' . $id_dotes . ' and Cd_AR like \'SCATOLO%\') where Id_DOTes = ' . $id_dotes);
        } catch (Exception $e) {
            return 'Errore';
        }
    }

    public function crea_doc_riordino($id_dotes)
    {
        try {
            $cd_do = DB::SELECT('SELECT * FROM DOTes where Id_DOTes = ' . $id_dotes)[0]->Cd_Do;
            if ($cd_do == 'OVC') {
                $righe = DB::SELECT('SELECT * FROM DORig where Id_DOTes = ' . $id_dotes . ' and QtaEvadibile > 0');
                if (sizeof($righe) > 0)
                    $Id_DoTes = DB::table('DOTes')->insertGetId(['Cd_CF' => $righe[0]->Cd_CF, 'Cd_Do' => 'RVC']);
                else
                    $Id_DoTes = 0;
                foreach ($righe as $r) {
                    $Id_DoRig = $r->Id_DORig;
                    $qtadaEvadere = $r->QtaEvadibile;
                    $magazzino = $r->Cd_MG_A;
                    $ubicazione = '0';
                    $lotto = $r->Cd_ARLotto;
                    $cd_cf = $r->Cd_CF;
                    $documento = 'RVC';
                    $cd_ar = $r->Cd_AR;
                    $insert_evasione['Cd_MG_P'] = $r->Cd_MG_P;
                    $insert_evasione['Cd_MG_A'] = $r->Cd_MG_A;
                    if ($lotto != null)
                        $insert_evasione['Cd_ARLotto'] = $lotto;
                    else
                        if (isset($insert_evasione['Cd_ARLotto'])) unset($insert_evasione['Cd_ARLotto']);

                    $Id_DoTes1 = $Id_DoTes;
                    $insert_evasione['Cd_AR'] = $cd_ar;
                    $insert_evasione['Id_DORig_Evade'] = $Id_DoRig;
                    $insert_evasione['PrezzoUnitarioV'] = $r->PrezzoUnitarioV;
                    $insert_evasione['Qta'] = $qtadaEvadere;
                    $insert_evasione['QtaEvasa'] = $insert_evasione['Qta'];

                    $Riga = DB::SELECT('SELECT * FROM DoRig where Id_DoRig=\'' . $Id_DoRig . '\'');
                    $insert_evasione['Cd_Aliquota'] = $r->Cd_Aliquota;
                    $insert_evasione['Cd_CGConto'] = $r->Cd_CGConto;
                    $insert_evasione['Id_DoTes'] = $Id_DoTes1;


                    $qta_evasa = DB::SELECT('SELECT * FROM DORig WHERE Id_DoRig= \'' . $Id_DoRig . '\' ')[0]->QtaEvasa;
                    $qta_evasa = intval($qta_evasa) + intval($qtadaEvadere);
                    $qta_evadibile = DB::SELECT('SELECT * FROM DORig WHERE Id_DoRig= \'' . $Id_DoRig . '\' ')[0]->QtaEvadibile;
                    $qta_evadibile = intval($qta_evadibile) - intval($qtadaEvadere);
                    DB::table('DoRig')->insertGetId($insert_evasione);
                    $Id_DoRig_OLD = DB::SELECT('SELECT TOP 1 * FROM DORIG ORDER BY Id_DORig DESC')[0]->Id_DORig;

                    if ($qtadaEvadere < $Riga[0]->QtaEvadibile) {
                        DB::UPDATE('Update DoRig set QtaEvadibile = \'' . $qta_evadibile . '\'WHERE Id_DoRig = \'' . $Id_DoRig . '\'');
                        DB::UPDATE('Update DoRig set QtaEvasa = \'' . $qta_evasa . '\'WHERE Id_DoRig = \'' . $Id_DoRig_OLD . '\'');
                    } else {
                        DB::UPDATE('Update DoRig set QtaEvadibile = \'0\'WHERE Id_DoRig = \'' . $Id_DoRig . '\'');
                        DB::update('Update dorig set Evasa = \'1\'   where Id_DoRig = \'' . $Id_DoRig . '\' ');
                        $Id_DoTes_old = DB::SELECT('SELECT * from DoRig where id_dorig = \'' . $Id_DoRig . '\' ')[0]->Id_DOTes;
                        DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = '$Id_DoTes_old'");
                        DB::statement("exec asp_DO_End '$Id_DoTes_old'");
                    }
                    DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = '$Id_DoTes1'");
                    DB::statement("exec asp_DO_End '$Id_DoTes1'");

                }
            }

        } catch (Exception $e) {
            return 'Errore';
        }
    }


    public function inserisci_peso($id_dotes, $peso)
    {
        try {
            DB::update('UPDATE DOTES SET PesoLordo = ' . $peso . ' where Id_DOTes = ' . $id_dotes);
        } catch (Exception $e) {
            return 'Errore';
        }
    }

    public function inserisci_scatolone($id_dotes, $ar, $qta)
    {
        DB::table('DORIG')->insertGetId(['Cd_AR' => $ar, 'Qta' => $qta, 'Id_DOTes' => $id_dotes, 'Cd_MG_A' => '00001', 'Cd_MG_P' => '00001', 'Cd_CGConto' => '06010101001', 'Cd_Aliquota' => 22]);
    }

    public function inserisci_scatolone_in_doc_evaso($id_dotes, $ar, $qta)
    {
        $id_dotes = DB::SELECT('select * from dorig where cd_do != \'RVC\' and id_dorig_evade in (SELECT Id_DOrig from dorig where id_dotes = \'' . $id_dotes . '\')');
        if (sizeof($id_dotes) > 0) {
            $id_dotes = $id_dotes[0]->Id_DOTes;
            DB::table('DORIG')->insertGetId(['Cd_AR' => $ar, 'Qta' => $qta, 'Id_DOTes' => $id_dotes, 'Cd_MG_A' => '00001', 'Cd_MG_P' => '00001', 'Cd_CGConto' => '06010101001', 'Cd_Aliquota' => 22]);
        } else {
            return 'error';
        }
    }

    public function cerca_articolo($q)
    {
// PAGINA ARTICOLI

        $articoli = DB::select('SELECT [Id_AR],[Cd_AR],[Descrizione] FROM AR where (Cd_AR Like \'' . $q . '%\' or  Descrizione Like \'%' . $q . '%\' or CD_AR IN (SELECT CD_AR from ARAlias where Alias LIKE \'%' . $q . '%\'))  Order By Id_AR DESC');
        if (sizeof($articoli) == '0') {
            $decoder = new Decoder($delimiter = '');
            $barcode = $decoder->decode($q);
            $where = ' where 1=1 ';

            foreach ($barcode->toArray()['identifiers'] as $field) {

                if ($field['code'] == '01') {
                    $testo = trim($field['content'], '*,');
                    $where .= ' and AR.Cd_AR Like \'%' . $testo . '%\'';
                }

            }
            $articoli = DB::select('SELECT [Id_AR],[Cd_AR],[Descrizione] FROM AR ' . $where . '  Order By Id_AR DESC');
        }
        if (sizeof($articoli) != '0')
            foreach ($articoli as $articolo) { ?>

                <li class="list-group-item">
                    <a href="/modifica_articolo/<?php echo $articolo->Id_AR ?>" class="media">
                        <div class="media-body">
                            <h5><?php echo $articolo->Descrizione ?></h5>
                            <p>Codice: <?php echo $articolo->Cd_AR ?></p>
                        </div>
                    </a>
                </li>

            <?php }


    }

    public function stampe($id_dotes)
    {
        DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = $id_dotes exec asp_DO_End $id_dotes");
        DB::statement("exec asp_DO_End $id_dotes");
        $id_dotes = DB::SELECT('SELECT * FROM DOTes WHERE Id_DOTes = \'' . $id_dotes . '\'')[0];
        if ($id_dotes->Cd_Do == 'DDT') {
            $html = View::make('stampe.ddt', compact('id_dotes'));
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
            $mpdf->curlAllowUnsafeSslRequests = true;
            $mpdf->SetTitle('Ddt');
            $mpdf->WriteHTML($html);
            $mpdf->Output('ddt.pdf', 'I');
        }
        if ($id_dotes->Cd_Do == 'RCF') {
            $html = View::make('stampe.rcf', compact('id_dotes'));
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
            $mpdf->curlAllowUnsafeSslRequests = true;
            $mpdf->SetTitle('Rcf');
            $mpdf->WriteHTML($html);
            $mpdf->Output('RCF.pdf', 'I');
        }
    }

    public function id_dotes($id_dotes)
    {
        DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = $id_dotes exec asp_DO_End $id_dotes");
        DB::statement("exec asp_DO_End $id_dotes");
    }

    public function visualizza_lotti($articolo)
    {

        $giacenza = DB::SELECT('SELECT SUM(QuantitaSign) as Giacenza  FROM MGMov where Cd_AR =\'' . $articolo . '\' and  Cd_MGEsercizio = YEAR(GETDATE()) and Cd_MG = \'00001\' ');
        foreach ($giacenza as $l) {
            ?>
            <li class="list-group-item">
                <a class="media" onclick="">
                    <div class="media-body">
                        <h3>Giacenza: <?php echo $l->Giacenza;
                            if ($l->Giacenza == '') echo '0';/*echo $l->Cd_AR.' - '.$l->Descrizione */ ?></h3>
                        <small><?php /*echo $l->Giacenza; if($l->Giacenza=='') echo '0';*/ ?></small>
                        <small><?php /*if($l->xCd_xPallet!='')echo 'Pallet : '.$l->xCd_xPallet ?></small>
                        <small><?php if($l->xNr_PalletFornitore!='')echo 'NrPalletFornitore : '.$l->xNr_PalletFornitore*/ ?></small>
                    </div>
                </a>
            </li>
        <?php }
    }

    /*
        public function storialotto($articolo,$lotto){
            $lotto1 = DB::SELECT('SELECT * FROM MGMov WHERE Cd_AR = \''.$articolo.'\' AND Cd_MGEsercizio = YEAR(GETDATE()) AND Cd_ARLotto = \''.$lotto.'\' ORDER BY DataMov ASC , PartenzaArrivo Desc');
            $giacenza =DB::SELECT('SELECT SUM(QuantitaSign) as Giacenza,Cd_AR,Cd_MG,Cd_ARLotto FROM MGMov WHERE Cd_AR = \''.$articolo.'\' AND Cd_ARLotto = \''.$lotto.'\' GROUP BY Cd_AR,Cd_ARLotto,Cd_MG HAVING SUM(QuantitaSign)>0');
            foreach ($lotto1 as $l){?>
                <li class="list-group-item">
                    <a class="media">
                        <div class="media-body">
                            <h5><?php echo $l->Cd_ARLotto ?></h5>
                            <p>Azione : <?php
                                if($l->Ini=='1') echo 'Iniziale';
                                if($l->Ret=='1') echo 'Rettifica';
                                if($l->Car=='1') echo 'Carico';
                                if($l->Sca=='1') echo 'Scarico';?></p>
                            <small>Magazzino : <?php echo  $l->Cd_MG ?></small>
                            <small>Quantita' : <?php echo floatval($l->QuantitaSign) ?></small>
                        </div>
                    </a>
                </li>
            <?php } ?>
            <li class="list-group-item">
                    <a class="media">
                        <div class="media-body">
                            <h5><?php echo $giacenza[0]->Cd_ARLotto ?></h5>
                            <p>Azione : <?php echo 'Giacenza'?></p>
                            <small>Magazzino : <?php echo  $giacenza[0]->Cd_MG ?></small>
                            <small>Quantita' : <?php echo floatval($giacenza[0]->Giacenza) ?></small>
                        </div>
                    </a>
                </li>
       <?php }

        public function inserisci_lotto($lotto,$articolo,$fornitore,$descrizione,$fornitore_pallet,$pallet){
            $esiste = DB::SELECT('SELECT * FROM ARLotto WHERE Cd_AR = \''.$articolo.'\' and Cd_ARLotto = \''.$lotto.'\' ');
            if(sizeof($esiste)>0){
                echo 'Impossibile creare il lotto in quanto gi?? esistente';
            }else {
                if($fornitore!='0') {
                    $fornitori = DB::select('SELECT [Id_CF],[Cd_CF],[Descrizione] FROM CF where Fornitore = 1 and (Cd_CF Like \'%' . $fornitore . '%\' or  Descrizione Like \'%' . $fornitore . '%\')  Order By Id_CF DESC');
                    if ($fornitori == null) {
                        echo 'Fornitore non trovato';
                        exit();
                    } else
                        $fornitori = $fornitori[0]->Cd_CF;
                }
                    $id_Lotto = DB::table('ARLotto')->insertGetId(['Cd_AR' => $articolo, 'Cd_ARLotto' => $lotto, 'Descrizione' => $descrizione]);
                if($fornitore!='0'){
                            DB::update("UPDATE ARLotto Set Cd_CF = '$fornitori' where Id_ARLotto = '$id_Lotto' ");
                }
                if($fornitore_pallet!='0'){
                    DB::update("UPDATE ARLotto Set xNr_PalletFornitore = '$fornitore_pallet' where Id_ARLotto = '$id_Lotto' ");
                }
                if($pallet!='0'){
                    DB::update("UPDATE ARLotto Set xCd_xPallet = '$pallet' where Id_ARLotto = '$id_Lotto' ");
                }
                echo 'Lotto Inserito Correttamente';
            }
        }
    */
    public function segnalazione_salva($id_dotes, $id_dorig, $testo)
    {
        $testo = str_replace('*', '', $testo);
        $testo = str_replace("slash", "/", $testo);
        $testo = str_replace("punto", ";", $testo);
        $id_dorig_evade = DB::select('SELECT * FROM DORig where Cd_DO != \'RVC\' AND Id_DORig_EVade in (SELECT Id_DORig from Dorig where id_dotes = \'' . $id_dotes . '\')');
        if (sizeof($id_dorig_evade) > 0) {
            $esiste = DB::SELECT('SELECT * FROM DoTes WHERE Id_DoTes = \'' . $id_dorig_evade[0]->Id_DOTes . '\' ')[0]->NotePiede;
            if ($esiste != null) {
                $esiste .= '                                    ';
                $esiste .= $testo;
                DB::update('Update DoTes set NotePiede = \'' . $esiste . '\' where Id_DoTes = \'' . $id_dorig_evade[0]->Id_DOTes . '\' ');
            } else
                DB::update('Update DOTes set NotePiede = \'' . $testo . '\' where Id_DoTes = \'' . $id_dorig_evade[0]->Id_DOTes . '\' ');
        }
    }

    public function segnalazione($id_dotes, $id_dorig, $testo)
    {

        if (substr($testo, 0, 2) == '01') {
            $decoder = new Decoder($delimiter = '');
            $barcode = $decoder->decode($testo);
            $where = 'Articolo ';
            foreach ($barcode->toArray()['identifiers'] as $field) {

                if ($field['code'] == '01') {
                    $contenuto = trim($field['content'], '*,');
                    $where .= $contenuto . ' con lotto ';

                }
                if ($field['code'] == '10') {
                    $where .= $field['content'] . ' non trovato.';

                }
                /*
                if ($field['code'] == '310') {
                    $decimali = floatval(substr($field['raw_content'],-2));
                    $qta = floatval(substr($field['raw_content'],0,4))+$decimali/100;
                    $where .= ' and Qta Like \'%' . $qta . '%\'';
                }*/

            }
        } else {
            $testo = trim($testo, '-');
            $where = $testo;
        }
        $esiste = DB::SELECT('SELECT * FROM DoTes WHERE Id_DoTes = \'' . $id_dotes . '\' ')[0]->NotePiede;
        if ($esiste != null) {
            $esiste .= '                                    ';
            $esiste .= $where;
            DB::update('Update DoTes set NotePiede = \'' . $esiste . '\' where Id_DoTes = \'' . $id_dotes . '\' ');
        } else
            DB::update('Update DOTes set NotePiede = \'' . $where . '\' where Id_DoTes = \'' . $id_dotes . '\' ');

    }

    public function cerca_fornitore_new($q = '', $dest)
    {

        $dest1 = DB::SELECT('SELECT * FROM DO WHERE Cd_DO = \'' . $dest . '\' ')[0]->CliFor;

        if ($dest1 == ('F')) {
            if ($q == '') {
                $fornitori = DB::select('SELECT [Id_CF],[Cd_CF],[Descrizione] FROM CF where Fornitore = 1 Order By Id_CF DESC');
            } else {
                $fornitori = DB::select('SELECT [Id_CF],[Cd_CF],[Descrizione] FROM CF where Fornitore = 1 and (Cd_CF Like \'%' . $q . '%\' or  Descrizione Like \'%' . $q . '%\')  Order By Id_CF DESC');
            }
        }
        if ($dest1 == ('C')) {
            if ($q == '') {
                $fornitori = DB::select('SELECT [Id_CF],[Cd_CF],[Descrizione] FROM CF where Cliente = 1 Order By Id_CF DESC');
            } else {
                $fornitori = DB::select('SELECT [Id_CF],[Cd_CF],[Descrizione] FROM CF where Cliente = 1 and (Cd_CF Like \'%' . $q . '%\' or  Descrizione Like \'%' . $q . '%\')  Order By Id_CF DESC');
            }
        }
        if ($dest == 'BCV') {
            foreach ($fornitori as $f) { ?>

                <li class="list-group-item">
                    <a href="/magazzino/trasporto_documento/BCV/<?php echo $f->Cd_CF ?>" class="media">
                        <div class="media-body">
                            <h5><?php echo $f->Descrizione ?></h5>
                            <p>Codice: <?php echo $f->Cd_CF ?></p>

                        </div>
                    </a>
                </li>

            <?php }
        } else {
            foreach ($fornitori as $f) { ?>

                <li class="list-group-item">
                    <a href="/magazzino/carico03/<?php echo $f->Id_CF ?>/<?php echo $dest ?>" class="media">
                        <div class="media-body">
                            <h5><?php echo $f->Descrizione ?></h5>
                            <p>Codice: <?php echo $f->Cd_CF ?></p>

                        </div>
                    </a>
                </li>

            <?php }
        }
    }

    public function cerca_cliente_new($q = '', $dest)
    {


        if ($q == '') {
            $clienti = DB::select('SELECT [Id_CF],[Cd_CF],[Descrizione] FROM CF where Cliente = 1 Order By Id_CF DESC');
        } else {
            $clienti = DB::select('SELECT [Id_CF],[Cd_CF],[Descrizione] FROM CF where Cliente = 1 and (Cd_CF Like \'%' . $q . '%\' or  Descrizione Like \'%' . $q . '%\')  Order By Id_CF DESC');
        }
        if ($dest == 'S2') {
            foreach ($clienti as $f) { ?>

                <li class="list-group-item">
                    <a href="/magazzino/scarico3/<?php echo $f->Id_CF ?>/OVC" class="media">
                        <div class="media-body">
                            <h5><?php echo $f->Descrizione ?></h5>
                            <p>Codice: <?php echo $f->Cd_CF ?></p>

                        </div>
                    </a>
                </li>

            <?php }
        }
        if ($dest == 'S02') {
            foreach ($clienti as $f) { ?>

                <li class="list-group-item">
                    <a href="/magazzino/scarico03/<?php echo $f->Id_CF ?>/DDT" class="media">
                        <div class="media-body">
                            <h5><?php echo $f->Descrizione ?></h5>
                            <p>Codice: <?php echo $f->Cd_CF ?></p>

                        </div>
                    </a>
                </li>

            <?php }
        }
    }

    public function cerca_articolo_codice($cd_cf, $codice, $Cd_ARLotto, $qta)
    {
        $codice = str_replace("slash", "/", $codice);
        $codice = str_replace("punto", ";", $codice);
        $Cd_ARLotto = str_replace("slash", "/", $Cd_ARLotto);
        $Cd_ARLotto = str_replace("punto", ";", $Cd_ARLotto);
        if ($Cd_ARLotto != '0')
            $scadenza = DB::select('SELECT * FROM ARLotto where Cd_ARLotto = \'' . $Cd_ARLotto . '\'');
        else
            $scadenza = [];

        $articoli = DB::select('SELECT AR.Id_AR,AR.Cd_AR,AR.Descrizione,ARAlias.Alias as barcode,ARARMisura.UMFatt,DORig.PrezzoUnitarioV,LSArticolo.Prezzo from AR
            LEFT JOIN ARAlias ON AR.Cd_AR = ARAlias.Cd_AR
            LEFT JOIN ARARMisura ON ARARMisura.Cd_AR = ARAlias.CD_AR and  ARARMisura.CD_ARMisura = ARAlias.CD_ARMisura
            LEFT JOIN LSArticolo ON LSArticolo.Cd_AR = AR.Cd_AR
            LEFT JOIN DORig ON DOrig.Cd_CF LIKE \'' . $cd_cf . '\' and DORig.Cd_AR = AR.Cd_AR
            where ARAlias.Alias Like \'' . $codice . '\' or AR.Cd_AR like \'' . $codice . '\'
            order by DORig.DataDoc DESC');

        $magazzino_selected = DB::select('SELECT MgMov.Cd_MG, Mg.Descrizione from MGMov LEFT JOIN MG ON MG.Cd_MG = MgMov.Cd_MG WHERE MgMov.Cd_ARLotto = \'' . $Cd_ARLotto . '\'  and MgMov.Cd_AR = \'' . $codice . '\' and MgMov.Cd_MGEsercizio = YEAR(GETDATE()) ');

        if ($magazzino_selected != null) {
            $magazzino_selected = $magazzino_selected[0];
            $magazzino_selezionato = $magazzino_selected->Cd_MG;
        } else
            $magazzino_selezionato = '0';

        $magazzini = DB::select('SELECT * from MG WHERE Cd_MG !=\'' . $magazzino_selezionato . '\' ');

        //TODO Controllare Data Scadenza togliere i commenti

        $date = date('d/m/Y', strtotime('today'));

        if ($Cd_ARLotto != '0')
            $lotto = DB::select('SELECT * FROM ARLotto WHERE Cd_AR = \'' . $articoli[0]->Cd_AR . '\' and Cd_ARLotto !=\'' . $Cd_ARLotto . '\'  ');
        else
            $lotto = DB::select('SELECT * FROM ARLotto WHERE Cd_AR = \'' . $articoli[0]->Cd_AR . '\'');

        if (sizeof($articoli) > 0) {
            $articolo = $articoli[0];
            echo '<h3>    Barcode: ' . $articolo->barcode . '<br>
                          Codice: ' . $articolo->Cd_AR . '<br>
                          Descrizione:<br>' . $articolo->Descrizione . '</h3>';
            ?>
            <script type="text/javascript">

                $('#modal_quantita').val(<?php echo intval($articolo->UMFatt) ?>);
                $('#modal_Cd_AR').val('<?php echo $articolo->Cd_AR ?>');

                <?php /*if($articolo->CostoDb){ ?>
                $('#modal_prezzo').val('<?php echo number_format($articolo->CostoDb,2,'.','') ?>');
                $('#modal_quantita').val(<?php echo intval($articolo->UMFatt) ?>);
                <?php } else {*/if($articolo->PrezzoUnitarioV){ ?>
                $('#modal_prezzo').val('<?php echo number_format($articolo->PrezzoUnitarioV, 2, '.', '') ?>');
                <?php } else { ?>
                $('#modal_prezzo').val('<?php echo number_format($articolo->Prezzo, 2, '.', '') ?>');
                <?php } ?>
                $('#modal_list_lotto').html
                <?php if($Cd_ARLotto != '0'){ ?>
                ('<option value="<?php echo $Cd_ARLotto ?>" selected><?php echo $Cd_ARLotto ?></option>')
                document.getElementById('modal_lotto').value = '<?php echo $Cd_ARLotto ?>';
                <?php } else {?>
                ('<option>Nessun Lotto</option>')
                document.getElementById('modal_lotto').value = 'Nessun Lotto';
                <?php } ?>
                <?php foreach($lotto as $l){?>
                $('#modal_list_lotto').append('<option><?php echo $l->Cd_ARLotto ?></option>')
                <?php } ?>
                $('#modal_data_scadenza').html
                <?php if(sizeof($scadenza) > 0){ ?>
                ('<option value="<?php echo $scadenza[0]->DataScadenza ?>" lotto="<?php echo $scadenza[0]->Cd_ARLotto ?>" selected><?php echo date('d/m/Y', strtotime($scadenza[0]->DataScadenza));  ?></option>')
                <?php } ?>
                ('<option lotto="Nessun Lotto" >Nessuna Scadenza</option>')
                <?php foreach($lotto as $l){?>
                $('#modal_data_scadenza').append('<option value="<?php echo $l->DataScadenza;?>" lotto="<?php echo $l->Cd_ARLotto;?>"><?php echo date('d/m/Y', strtotime($l->DataScadenza)) ?></option>')
                <?php } ?>
                $('#modal_magazzino_P').html
                <?php  if($magazzino_selezionato != '0'){ ?>
                ('<option><?php echo $magazzino_selected->Cd_MG . ' - ' . $magazzino_selected->Descrizione?></option>')
                <?php } ?>
                <?php foreach($magazzini as $m){?>
                $('#modal_magazzino_P').append('<option><?php echo $m->Cd_MG . ' - ' . $m->Descrizione ?></option>')
                <?php } ?>

            </script>
            <?php
        }

        if (sizeof($articoli) < 1) {
            $articoli = DB::select('
                SELECT AR.Id_AR,AR.Cd_AR,AR.Descrizione,ARARMisura.UMFatt,DORig.PrezzoUnitarioV,LSArticolo.Prezzo from AR
                LEFT JOIN ARARMisura ON ARARMisura.Cd_AR = AR.CD_AR
                LEFT JOIN LSArticolo ON LSArticolo.Cd_AR = AR.Cd_AR
                LEFT JOIN LSRevisione ON LSRevisione.Id_LSRevisione = LSArticolo.Id_LSRevisione and LSRevisione.Cd_LS = \'LSF\'
                LEFT JOIN DORig ON DOrig.Cd_CF LIKE \'' . $cd_cf . '\' and DORig.Cd_AR = AR.Cd_AR
                where AR.CD_AR LIKE \'' . $codice . '\'
                order by DORig.DataDoc DESC');
            if ($Cd_ARLotto != '')
                $lotto = DB::select('SELECT * FROM ARLotto WHERE Cd_AR = \'' . $codice . '\' and Cd_ARLotto !=\'' . $Cd_ARLotto . '\' and  Cd_ARLotto in (select Cd_ARLotto from MGMov group by Cd_ARLotto having SUM(QuantitaSign) > 0)  ');
            else
                $lotto = DB::select('SELECT * FROM ARLotto WHERE Cd_AR = \'' . $codice . '\' and Cd_AR in (select Cd_AR from MGMov group by Cd_AR having SUM(QuantitaSign) > 0) ');
            if (sizeof($articoli) > 0) {
                $articolo = $articoli[0];
                echo '<h3>Barcode : Non inserito <br>
                          Codice: ' . $articolo->Cd_AR . '<br>
                          Pezzi x Collo: ' . intval($articolo->UMFatt) . '<br><br>
                          Descrizione:<br>' . $articolo->Descrizione . '</h3>';
                ?>
                <script type="text/javascript">

                    $('#modal_Cd_AR').val('<?php echo $articolo->Cd_AR ?>');
                    <?php if($articolo->PrezzoUnitarioV){ ?>
                    $('#modal_prezzo').val('<?php echo number_format($articolo->PrezzoUnitarioV, 2, '.', '') ?>');
                    <?php } else { ?>
                    $('#modal_prezzo').val('<?php echo number_format($articolo->Prezzo, 2, '.', '') ?>');
                    <?php }?>
                    $('#modal_lotto').html
                    <?php if($Cd_ARLotto != '0'){ ?>
                    ('<option><?php echo $Cd_ARLotto ?></option>');
                    <?php } ?>
                    $('#modal_lotto').append('<option>Nessun Lotto</option>');
                    <?php foreach($lotto as $l){?>
                    $('#modal_lotto').append('<option><?php echo $l->Cd_ARLotto ?></option>')
                    <?php } ?>


                </script>
                <?php
            }
        }
    }

    public function salva_documento1($Id_DoTes, $Cd_DO)
    {
        $righe = DB::SELECT('SELECT * FROM DoRig WHERE Id_DoTes = \'' . $Id_DoTes . '\' and QtaEvadibile > \'0\' ');
        foreach ($righe as $riga) {
            ?>
            <li class="list-group-item">
                <a href="#" class="media">
                    <div class="media-body">
                        <h5><?php echo $riga->Cd_AR;
                            if ($riga->Cd_ARLotto != '') echo '  Lotto: ' . $riga->Cd_ARLotto; ?></h5>
                        <p>Quantita': <?php echo $riga->Qta ?></p>
                    </div>
                </a>
            </li>
            <script type="text/javascript">
                $('#modal_Cd_AR_c_<?php echo $riga->Id_DORig ?>').val('<?php echo $riga->Cd_AR ?>');
                $('#modal_Cd_ARLotto_c_<?php echo $riga->Id_DORig ?>').val('<?php echo $riga->Cd_ARLotto ?>');
                $('#modal_Qta_c_<?php echo $riga->Id_DORig ?>').val('<?php echo $riga->Qta ?>');
                $('#modal_QtaEvasa_c_<?php echo $riga->Id_DORig ?>').val('<?php echo $riga->QtaEvasa ?>');
                $('#modal_QtaEvadibile_c_<?php echo $riga->Id_DORig ?>').val('<?php echo $riga->QtaEvadibile ?>');
            </script>
        <?php }
    }

    public function evadi_articolo($Id_DoRig, $qtadaEvadere, $magazzino, $ubicazione, $lotto, $cd_cf, $documento, $cd_ar, $magazzino_A)
    {
        $cd_ar = str_replace("slash", "/", $cd_ar);
        $cd_ar = str_replace("punto", ";", $cd_ar);
        $Id_DoTes = '0';
        if ($qtadaEvadere == '0') {
            echo 'Impossibile evadere la Quantita a 0';
            exit();
        } else {
            $date = date('d/m/Y', strtotime('today'));
            $controllo = DB::SELECT('SELECT * FROM DORIG WHERE Id_DORig = \'' . $Id_DoRig . '\'')[0]->Id_DOTes;
            $controlli = DB::SELECT('SELECT * FROM DORIG WHERE Id_DOTes = \'' . $controllo . '\'');
            foreach ($controlli as $c) {
                $testata = DB::SELECT('SELECT * FROM DORIG WHERE Id_DORig_Evade = \'' . $c->Id_DORig . '\' and DataDoc = \'' . $date . '\'');
                if ($testata != null)
                    $Id_DoTes = $testata[0]->Id_DOTes;
            }

        }
        if ($Id_DoTes == '0')
            $Id_DoTes = '';
        $Id_DoTes_old = DB::SELECT('SELECT * from DoRig where id_dorig = \'' . $Id_DoRig . '\' ')[0]->Id_DOTes;
        $listino = DB::SELECT('SELECT * from DOTes where Id_DOTes = \'' . $Id_DoTes_old . '\' ');
        $insert_evasione['PrezzoUnitarioV'] = $controlli[0]->PrezzoUnitarioV;
        if ($listino[0]->Cd_LS_1 != null)
            $listino = $listino[0]->Cd_LS_1;
        else
            $listino = '';
        if ($Id_DoTes == '' && $listino != '')
            $Id_DoTes = DB::table('DOTes')->insertGetId(['Cd_CF' => $cd_cf, 'Cd_Do' => $documento, 'Cd_LS_1' => $listino]);
        if ($Id_DoTes == '' && $listino == '')
            $Id_DoTes = DB::table('DOTes')->insertGetId(['Cd_CF' => $cd_cf, 'Cd_Do' => $documento]);
        $pagamento = DB::SELECT('SELECT * FROM DOTes WHERE ID_DOTes = \'' . $controllo . '\'');
        if ($pagamento[0]->Cd_PG != '') {
            $pagamento = $pagamento[0]->Cd_PG;
            DB::update("Update DOTes set Cd_PG = '$pagamento' where ID_DOTes = '$controllo'");
        }
        $agente = DB::SELECT('SELECT * FROM DOTes WHERE ID_DOTes = \'' . $controllo . '\'');
        if ($agente[0]->Cd_Agente_1 != '') {
            $agente = $agente[0]->Cd_Agente_1;
            DB::update("Update DOTes set Cd_Agente_1 = '$agente' where ID_DOTes = '$controllo'");
        }
        if ($magazzino_A != 0)
            $insert_evasione['Cd_MG_A'] = $magazzino_A;
        if ($magazzino != 0)
            $insert_evasione['Cd_MG_P'] = $magazzino;

        if ($lotto != '0')
            $insert_evasione['Cd_ARLotto'] = $lotto;
        $Id_DoTes1 = $Id_DoTes;
        $insert_evasione['Cd_AR'] = $cd_ar;
        $insert_evasione['Id_DORig_Evade'] = $Id_DoRig;
        $insert_evasione['Qta'] = $qtadaEvadere;
        $insert_evasione['QtaEvasa'] = $insert_evasione['Qta'];
        $Riga = DB::SELECT('SELECT * FROM DoRig where Id_DoRig=\'' . $Id_DoRig . '\'');
        $insert_evasione['Cd_Aliquota'] = $Riga[0]->Cd_Aliquota;
        $insert_evasione['PrezzoUnitarioV'] = $Riga[0]->PrezzoUnitarioV;
        if ($Riga[0]->ScontoRiga != '')
            $insert_evasione['ScontoRiga'] = $Riga[0]->ScontoRiga;
        $insert_evasione['Cd_CGConto'] = $Riga[0]->Cd_CGConto;
        $insert_evasione['Id_DoTes'] = $Id_DoTes1;
        $qta_evasa = DB::SELECT('SELECT * FROM DORig WHERE Id_DoRig= \'' . $Id_DoRig . '\' ')[0]->QtaEvasa;
        $qta_evasa = intval($qta_evasa) + intval($qtadaEvadere);
        $qta_evadibile = DB::SELECT('SELECT * FROM DORig WHERE Id_DoRig= \'' . $Id_DoRig . '\' ')[0]->QtaEvadibile;
        $qta_evadibile = intval($qta_evadibile) - intval($qtadaEvadere);
        DB::table('DoRig')->insertGetId($insert_evasione);
        $Id_DoRig_OLD = DB::SELECT('SELECT TOP 1 * FROM DORig ORDER BY Id_DORig DESC')[0]->Id_DORig;

        if ($qtadaEvadere < $Riga[0]->QtaEvadibile) {
            DB::UPDATE('Update DoRig set QtaEvadibile = \'' . $qta_evadibile . '\'WHERE Id_DoRig = \'' . $Id_DoRig . '\'');
            DB::UPDATE('Update DoRig set QtaEvasa = \'' . $qta_evasa . '\'WHERE Id_DoRig = \'' . $Id_DoRig_OLD . '\'');
        } else {
            DB::UPDATE('Update DoRig set QtaEvadibile = \'0\'WHERE Id_DoRig = \'' . $Id_DoRig . '\'');
            DB::update('Update dorig set Evasa = \'1\'   where Id_DoRig = \'' . $Id_DoRig . '\' ');
            DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = '$Id_DoTes_old'");
            DB::statement("exec asp_DO_End '$Id_DoTes_old'");
        }
        DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = '$Id_DoTes1'");
        DB::statement("exec asp_DO_End '$Id_DoTes1'");
    }

    public function conferma_righe($Id_DoRig, $cd_mg_a, $cd_mg_p, $cd_do)
    {
        try {
            $insert = [];
            DB::beginTransaction();

            $Id_DoRig = 0;
            foreach ($_GET as $key => $d) {
                $q = explode(';', $key);
                if (sizeof($q) > 1)
                    $data_scadenza = $q[1];
                else
                    $data_scadenza = 0;
                if (sizeof($q) > 2)
                    $lotto = $q[2];
                else
                    $lotto = 0;

                $id_dorig = $q[0];

                ${$id_dorig . '_qta'} = ($d);
                ${$id_dorig . '_lotto'} = ($lotto);
                ${$id_dorig . '_data_scadenza'} = ($data_scadenza);
                $Id_DoRig .= '\',\'' . $id_dorig;
            }
            $Id_DoTes = '';
            $date = date('Y-m-d H:i:s', strtotime('today'));
            $controllo = DB::SELECT('SELECT * FROM DORIG WHERE Id_DORig in (\'' . $Id_DoRig . '\')')[0]->Id_DOTes;
            $controlli = DB::SELECT('SELECT * FROM DORIG WHERE Id_DOTes = \'' . $controllo . '\'');
            foreach ($controlli as $c) {
                $testata = DB::SELECT('SELECT * FROM DORIG WHERE Cd_DO = \'' . $cd_do . '\' and Id_DORig_Evade = \'' . $c->Id_DORig . '\'');
                if ($testata != null)
                    if ($testata[0]->DataDoc == $date)
                        $Id_DoTes = $testata[0]->Id_DOTes;
            }
            $date = date('d/m/Y', strtotime('today'));

            $righe = DB::select('SELECT * FROM DORIG WHERE ID_DORIG IN (\'' . $Id_DoRig . '\')');
            foreach ($righe as $r) {
                $Id_DoRig = $r->Id_DORig;
                $qtadaEvadere = ${$r->Id_DORig . '_qta'};
                $magazzino = $r->Cd_MG_A;
                $ubicazione = '0';
                $lotto = ${$r->Id_DORig . '_lotto'};
                $cd_cf = $r->Cd_CF;
                $documento = $cd_do;
                $cd_ar = $r->Cd_AR;
                $magazzino_A = $cd_mg_a; //magazzino di default
                $magazzino = $cd_mg_p; //magazzino di default
                $insert_evasione['Cd_MG_P'] = '';
                $insert_evasione['Cd_MG_A'] = '';
                $insert_evasione['TipoPc'] = $r->TipoPC;
                $old_dotes = DB::SELECT('select * from dotes where Id_DOTes = \'' . $r->Id_DOTes . '\'');
                if (sizeof($old_dotes) > 0) {
                    $agente = ($old_dotes[0]->Cd_Agente_1) ? $old_dotes[0]->Cd_Agente_1 : null;
                    $agente_2 = ($old_dotes[0]->Cd_Agente_2) ? $old_dotes[0]->Cd_Agente_2 : null;
                    $notepiede = ($old_dotes[0]->NotePiede) ? $old_dotes[0]->NotePiede : null;
                    $pagamento = ($old_dotes[0]->Cd_PG) ? $old_dotes[0]->Cd_PG : null;
                    $ScontoCassa = ($old_dotes[0]->ScontoCassa) ? $old_dotes[0]->ScontoCassa : null;
                } else {
                    $agente = null;
                    $agente_2 = null;
                    $notepiede = null;
                    $pagamento = null;
                    $ScontoCassa = null;
                }

                if ($Id_DoTes == '') {
                    $Id_DoTes = DB::table('DOTes')->insertGetId(['Cd_CF' => $cd_cf, 'Cd_Do' => $documento, 'Cd_Agente_1' => $agente, 'Cd_Agente_2' => $agente_2, 'NotePiede' => $notepiede, 'Cd_PG' => $pagamento]);
                    if ($ubicazione != '0')
                        $insert_evasione['Cd_MGUbicazione_P'] = $ubicazione;
                    if ($magazzino != '0')
                        $insert_evasione['Cd_MG_P'] = $magazzino;
                    if ($magazzino_A != '0')
                        $insert_evasione['Cd_MG_A'] = $magazzino_A;
                    if ($ScontoCassa != null) DB::UPDATE("Update dotes set dotes.ScontoCassa= '$ScontoCassa' where dotes.id_dotes = '$Id_DoTes'");
                    //DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = '$Id_DoTes'");
                    //DB::statement("exec asp_DO_End $Id_DoTes");

                    $evasione_dorig_spesa = [];
                    $dorig_spesa = DB::SELECT('select * from DORigSpesa where Id_DOTes = \'' . $r->Id_DOTes . '\'');
                    if (sizeof($dorig_spesa) > 0) {
                        $first_row = $dorig_spesa[0];
                        foreach ($first_row as $key => $value) {
                            $evasione_dorig_spesa[$key] = $value;
                        }
                        $evasione_dorig_spesa['Id_DoTes'] = $Id_DoTes;
                        $evasione_dorig_spesa['DataDoc'] = str_replace('-', '', $evasione_dorig_spesa['DataDoc']);
                        unset($evasione_dorig_spesa['TimeIns']);
                        unset($evasione_dorig_spesa['TimeUpd']);
                        unset($evasione_dorig_spesa['UserIns']);
                        unset($evasione_dorig_spesa['UserUpd']);
                        unset($evasione_dorig_spesa['Ts']);
                        unset($evasione_dorig_spesa['ImportoE']);
                        unset($evasione_dorig_spesa['ImportoEvadibileE']);
                        unset($evasione_dorig_spesa['ImportoEvasoE']);
                        unset($evasione_dorig_spesa['ExtraInfoPresent']);
                        unset($evasione_dorig_spesa['Id_DoRigSpesa']);
                        unset($evasione_dorig_spesa['Evasa']);
                        unset($evasione_dorig_spesa['ImportoEvasoV']);
                        DB::table('DORigSpesa')->insertGetId($evasione_dorig_spesa);
                    }

                }

                if ($insert_evasione['Cd_MG_P'] == null || $insert_evasione['Cd_MG_P'] == '0')
                    $insert_evasione['Cd_MG_P'] = $magazzino;
                if ($insert_evasione['Cd_MG_A'] == null || $insert_evasione['Cd_MG_A'] == '0')
                    $insert_evasione['Cd_MG_A'] = $magazzino_A;
                if ($lotto != '0') {
                    $check_lotto = DB::SELECT('select * from arlotto where Cd_AR = \'' . $r->Cd_AR . '\' and  cd_arlotto = \'' . $lotto . '\'');
                    if (sizeof($check_lotto) > 0) {
                        $insert_evasione['Cd_ARLotto'] = $lotto;
                        if (($check_lotto[0]->DataScadenza == null || $check_lotto[0]->DataScadenza == '') && $data_scadenza != 0) {
                            DB::UPDATE('UPDATE ARLotto set DataScadenza = \'' . $data_scadenza . '\' WHERE Cd_AR = \'' . $r->Cd_AR . '\' and Cd_ARLotto = \'' . $lotto . '\'');
                        }
                    }
                } else {
                    if (isset($insert_evasione['Cd_ARLotto'])) unset($insert_evasione['Cd_ARLotto']);
                }
                $check = DB::SELECT('SELECT * from MGCausale where Cd_MGCausale IN (SELECT Cd_MGCausale FROM DO where cd_do = (SELECT TOP 1 Cd_DO FROM DOTes where Id_DOTes = \'' . $Id_DoTes . '\'))');
                if (sizeof($check) > 0) {
                    if ($check[0]->MagPFlag == 0)
                        unset($insert_evasione['Cd_MG_P']);
                    if ($check[0]->MagAFlag == 0)
                        unset($insert_evasione['Cd_MG_A']);
                }
                $Id_DoTes1 = $Id_DoTes;
                $insert_evasione['Cd_AR'] = $cd_ar;
                $insert_evasione['Id_DORig_Evade'] = $Id_DoRig;
                $insert_evasione['PrezzoUnitarioV'] = $r->PrezzoUnitarioV;
                $insert_evasione['Qta'] = $qtadaEvadere;
                $insert_evasione['QtaEvasa'] = $insert_evasione['Qta'];

                $Riga = DB::SELECT('SELECT * FROM DoRig where Id_DoRig=\'' . $Id_DoRig . '\'');
                $insert_evasione['Cd_Aliquota'] = $r->Cd_Aliquota;
                if ($r->ProvvigioneRiga_1 != '')
                    $insert_evasione['ProvvigioneRiga_1'] = $r->ProvvigioneRiga_1;
                if ($r->ProvvigioneRiga_1 != '')
                    $insert_evasione['ProvvigioneRiga_2'] = $r->ProvvigioneRiga_1;
                if ($r->ScontoRiga != '')
                    $insert_evasione['ScontoRiga'] = $r->ScontoRiga;
                $insert_evasione['Cd_CGConto'] = $r->Cd_CGConto;
                $insert_evasione['Id_DoTes'] = $Id_DoTes1;


                $qta_evasa = DB::SELECT('SELECT * FROM DORig WHERE Id_DoRig= \'' . $Id_DoRig . '\' ')[0]->QtaEvasa;
                $qta_evasa = intval($qta_evasa) + intval($qtadaEvadere);
                $qta_evadibile = DB::SELECT('SELECT * FROM DORig WHERE Id_DoRig= \'' . $Id_DoRig . '\' ')[0]->QtaEvadibile;
                $qta_evadibile = intval($qta_evadibile) - intval($qtadaEvadere);

                DB::table('DoRig')->insertGetId($insert_evasione);

                $Id_DoRig_OLD = DB::SELECT('SELECT TOP 1 * FROM DORIG ORDER BY Id_DORig DESC')[0]->Id_DORig;

                if ($qtadaEvadere < $Riga[0]->QtaEvadibile) {
                    DB::UPDATE('Update DoRig set QtaEvadibile = \'' . $qta_evadibile . '\'WHERE Id_DoRig = \'' . $Id_DoRig . '\'');
                    DB::UPDATE('Update DoRig set QtaEvasa = \'' . $qta_evasa . '\'WHERE Id_DoRig = \'' . $Id_DoRig_OLD . '\'');
                } else {
                    DB::UPDATE('Update DoRig set QtaEvadibile = \'0\'WHERE Id_DoRig = \'' . $Id_DoRig . '\'');
                    DB::update('Update dorig set Evasa = \'1\'   where Id_DoRig = \'' . $Id_DoRig . '\' ');
                    $Id_DoTes_old = DB::SELECT('SELECT * from DoRig where id_dorig = \'' . $Id_DoRig . '\' ')[0]->Id_DOTes;
                }
            }
            DB::COMMIT();
            DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = '$Id_DoTes_old'");
            DB::statement("exec asp_DO_End '$Id_DoTes_old'");
            DB::update("Update dotes set dotes.reserved_1= 'RRRRRRRRRR' where dotes.id_dotes = '$Id_DoTes1'");
            DB::statement("exec asp_DO_End '$Id_DoTes1'");
            return $Id_DoTes1;
        } catch (\Exception $e) {
            DB::ROLLBACK();
            return $e->getMessage();
        }
    }

    public
    function crea_documento($cd_cf, $cd_do, $numero, $data)
    {

        $fornitore = DB::SELECT('SELECT * FROM CF WHERE Cd_CF = \'' . $cd_cf . '\' ');
        if (sizeof($listino) > 0)
            $listino = $fornitore[0]->Cd_LS_1;
        else
            $listino = 'BANCO';
        $insert_testata_ordine['Cd_LS_1'] = $listino;
        $insert_testata_ordine['Cd_CF'] = $cd_cf;
        $insert_testata_ordine['Cd_Do'] = $cd_do;
        $insert_testata_ordine['NumeroDoc'] = $numero;
        /*if($cd_do == 'DDT')
            $insert_testata_ordine['Modificabile'] = 0 ;*/
        if ($cd_do == 'DDT') {
            $insert_testata_ordine['Cd_DoSped'] = '02';
            $insert_testata_ordine['Cd_DoPorto'] = '01';
            $insert_testata_ordine['Cd_DoTrasporto'] = '001';
            $insert_testata_ordine['Cd_DoAspBene'] = 'AV';
            date_default_timezone_set('Europe/Rome');
            $ora = date('Y-m-d', strtotime('now'));
            $insert_testata_ordine['TrasportoDataOra'] = $ora;
        }
        if ($fornitore[0]->Cd_CGConto_Banca)
            $insert_testata_ordine['Cd_CGConto_Banca'] = $fornitore[0]->Cd_CGConto_Banca;
        $data = str_replace('-', '', $data);
        $insert_testata_ordine['DataDoc'] = $data;
        $Id_DoTes = DB::table('DOTes')->insertGetId($insert_testata_ordine);
        echo $Id_DoTes;
    }

    public
    function crea_documento_rif($cd_cf, $cd_do, $numero, $data, $numero_rif, $data_rif, $dest)
    {

        $fornitore = DB::SELECT('SELECT * FROM CF WHERE Cd_CF = \'' . $cd_cf . '\' ');
        if (sizeof($fornitore) > 0)
            $listino = $fornitore[0]->Cd_LS_1;
        else
            $listino = 'BANCO';
        if (sizeof($fornitore) > 0)
            if ($fornitore[0]->Cd_PG != null)
                $insert_testata_ordine['Cd_PG'] = $fornitore[0]->Cd_PG;
        $insert_testata_ordine['Cd_LS_1'] = $listino;
        $insert_testata_ordine['Cd_CF'] = $cd_cf;
        $insert_testata_ordine['Cd_Do'] = $cd_do;
        if ($fornitore[0]->Cd_CGConto_Banca)
            $insert_testata_ordine['Cd_CGConto_Banca'] = $fornitore[0]->Cd_CGConto_Banca;
        $insert_testata_ordine['NumeroDoc'] = $numero;
        /*if ($cd_do == 'DDT')
            $insert_testata_ordine['Modificabile'] = 0;*/
        $data = str_replace('-', '', $data);
        $insert_testata_ordine['DataDoc'] = $data;
        if ($numero_rif != '0') {
            $insert_testata_ordine['NumeroDocRif'] = $numero_rif;
            $data_rif = str_replace('-', '', $data_rif);
        }
        if ($data_rif != '0')
            $insert_testata_ordine['DataDocRif'] = $data_rif;
        if ($cd_do == 'DDT') {
            $insert_testata_ordine['Cd_DoSped'] = '02';
            $insert_testata_ordine['Cd_DoPorto'] = '01';
            $insert_testata_ordine['Cd_DoTrasporto'] = '001';
            $insert_testata_ordine['Cd_DoAspBene'] = 'AV';
            date_default_timezone_set('Europe/Rome');
            $ora = date('Y-m-d', strtotime('now'));
            $ora = str_replace('-', '', $ora);
            $insert_testata_ordine['TrasportoDataOra'] = $ora;
            if ($dest != 0)
                $insert_testata_ordine['Cd_CFDest'] = $dest;
        }
        $Id_DoTes = DB::table('DOTes')->insertGetId($insert_testata_ordine);
        echo $Id_DoTes;
    }

    public
    function aggiungi_articolo_ordine($id_ordine, $codice, $quantita, $magazzino_A, $ubicazione_A, $lotto, $magazzino_P, $ubicazione_P)
    {
        $codice = str_replace('slash', '/', $codice);
        $i = 0;
        $magazzini = DB::SELECT('SELECT * FROM MGUbicazione WHERE Cd_MG=\'' . $magazzino_A . '\'');
        foreach ($magazzini as $m) {
            if ($m->Cd_MGUbicazione == $ubicazione_A)
                $i++;
        }
        if ($ubicazione_A == 'ND')
            $i++;
        if ($i > 0) {
            ArcaUtilsController::aggiungi_articolo($id_ordine, $codice, $quantita, $magazzino_A, 1, $ubicazione_A, $lotto, $magazzino_P, $ubicazione_P);

            $ordine = DB::select('SELECT * from DOTes where Id_DOtes = ' . $id_ordine)[0];

            echo 'Articolo Caricato Correttamente ';

        } else {
            echo 'Ubicazione inserita inesistente in quel magazzino';
            exit();
        }
    }

    public
    function cerca_articolo_smart_automatico($q, $cd_cf)
    {
        $q = str_replace("slash", "/", $q);
        $q = str_replace("punto", ";", $q);
        $qta = 'ND';
        $q = explode(';', $q);
        if (sizeof($q) > 1)
            $data_scadenza = $q[1];
        else
            $data_scadenza = 0;
        if (sizeof($q) > 2)
            $lotto = $q[2];
        else
            $lotto = 0;

        $q = $q[0];
        if ($lotto != 0)
            $articoli = DB::select('SELECT AR.[Id_AR],AR.[Cd_AR],AR.[Descrizione],ARLotto.[Cd_ARLotto] FROM AR LEFT JOIN ARLotto ON AR.Cd_AR = ARLotto.Cd_AR LEFT JOIN ARAlias ON ARAlias.Cd_AR = AR.Cd_AR where ARLotto.Cd_ARLotto = \'' . $lotto . '\' and AR.Obsoleto = 0 AND (AR.Cd_AR Like \'' . $q . '%\' or  AR.Descrizione Like \'%' . $q . '%\' or AR.CD_AR IN (SELECT CD_AR from ARAlias where Alias LIKE \'%' . $q . '%\')) Order By AR.Id_AR DESC');
        else
            $articoli = DB::select('SELECT AR.[Id_AR],AR.[Cd_AR],AR.[Descrizione],0 as Cd_ARLotto FROM AR LEFT JOIN ARAlias ON ARAlias.Cd_AR = AR.Cd_AR where AR.Obsoleto = 0 AND (AR.Cd_AR Like \'' . $q . '%\' or  AR.Descrizione Like \'%' . $q . '%\' or AR.CD_AR IN (SELECT CD_AR from ARAlias where Alias LIKE \'%' . $q . '%\')) Order By AR.Id_AR DESC');
        if (sizeof($articoli) > 0) {
            $articolo = $articoli[0];
            ?>
            '<?php echo $cd_cf ?>','<?php echo $q; ?>','<?php if ($articolo->Cd_ARLotto != '') echo $articolo->Cd_ARLotto; else echo '0'; ?>','<?php if ($qta != '') echo $qta; else echo '0'; ?>'
            <?php
        }
    }

    public
    function cerca_articolo_smart_manuale($q, $cd_cf)
    {
        $q = str_replace("slash", "/", $q);
        $q = str_replace("punto", ";", $q);
        $qta = 'ND';/*
            $decoder = new Decoder($delimiter = '');
            $barcode = $decoder->decode($q);
            $where = ' where 1=1 ';
            foreach ($barcode->toArray()['identifiers'] as $field) {

                if ($field['code'] == '01') {
                    $testo = trim($field['content'], '*,');
                    $where .= ' and AR.Cd_AR Like \'%' . $testo . '%\'';
                }
                if ($field['code'] == '310') {
                    $decimali = floatval(substr($field['raw_content'],-2));
                    $qta = floatval(substr($field['raw_content'],0,4))+$decimali/100;
                }
                if ($field['code'] == '10') {
                    $where .= ' and ARLotto.Cd_ARLotto Like \'%' . $field['content'] . '%\'';
                }

            }
            $articoli = DB::select('SELECT AR.[Id_AR],AR.[Cd_AR],AR.[Descrizione],ARLotto.[Cd_ARLotto] FROM AR LEFT JOIN ARLotto on AR.Cd_AR = ARLotto.Cd_AR ' . $where . '  Order By Id_AR DESC');
*/

        $articoli = DB::select('SELECT AR.[Id_AR],AR.[Cd_AR],AR.[Descrizione],ARLotto.[Cd_ARLotto] FROM AR LEFT JOIN ARLotto ON AR.Cd_AR = ARLotto.Cd_ARLotto LEFT JOIN ARAlias ON ARAlias.Cd_AR = AR.Cd_AR where AR.Obsoleto = 0 AND AR.Cd_AR Like \'' . $q . '%\' or  AR.Descrizione Like \'%' . $q . '%\' or AR.CD_AR IN (SELECT CD_AR from ARAlias where Alias LIKE \'%' . $q . '%\') Order By AR.Id_AR DESC');
        if (sizeof($articoli) > 0) {
            foreach ($articoli as $articolo) { ?>

                <li class="list-group-item">
                    <a href="#" onclick="" class="media">
                        <div class="media-body"
                             onclick="cerca_articolo_codice('<?php echo $cd_cf ?>','<?php echo $q ?>','<?php if ($articolo->Cd_ARLotto != '') echo $articolo->Cd_ARLotto; else echo '0'; ?>','<?php if ($qta != '') echo $qta; else echo '0'; ?>')">
                            <h5><?php echo $articolo->Descrizione; ?></h5>
                            <p>Codice: <?php echo $articolo->Cd_AR ?></p>
                        </div>
                    </a>
                </li>

            <?php }
        }
    }


    public
    function controllo_articolo_smart($q, $id_dotes)
    {
        /*
                $decoder = new Decoder($delimiter = '');
                $barcode = $decoder->decode($q);
                $where = ' where 1=1 ';
                foreach ($barcode->toArray()['identifiers'] as $field) {

                    if ($field['code'] == '01') {
                        $contenuto = trim($field['content'],'*,');
                        $where .= ' and Cd_AR Like \'%' . $contenuto . '%\'';

                    }
                    if ($field['code'] == '10') {
                        $where .= ' and Cd_ARLotto Like \'%' . $field['content'] . '%\'';

                    }
                    if ($field['code'] == '310') {
                        $decimali = floatval(substr($field['raw_content'],-2));
                        $qta = floatval(substr($field['raw_content'],0,4))+$decimali/100;
                        $where .= ' and Qta Like \'%' . $qta . '%\'';

                    }

                }*/

        $q = str_replace("slash", "/", $q);
        $q = str_replace("punto", ";", $q);
        $q = explode(';', $q);
        if (sizeof($q) > 1)
            $scadenza = ($q[1] != '0') ? $q[1] : 0;
        else
            $scadenza = 0;
        if (sizeof($q) > 2)
            $lotto_scelto = ($q[2] != '0') ? $q[2] : 0;
        else
            $lotto_scelto = 0;
        $q = $q[0];
        $c = $q;
        $quantita = 1;

        $q = DB::SELECT('SELECT *,(SELECT UMFatt from ARARMisura where Cd_AR = ARAlias.Cd_AR and Cd_ARMisura = ARAlias.Cd_ARMisura) as UMFatt FROM ARALias WHERE Alias = \'' . $q . '\' ');

        if (sizeof($q) != 0)
            $quantita = $q[0]->UMFatt;
        if (sizeof($q) != 0)
            $q = $q[0]->Cd_AR;
        else
            $q = $c;
        //if ($lotto == 0)
        $articoli = DB::select('SELECT * FROM DoRig WHERE /*Cd_ARLotto is null and*/ Cd_AR = \'' . $q . '\' and Id_DoTes in (\'' . $id_dotes . '\') Order By QtaEvadibile DESC');
        /*        else
                    $articoli = DB::select('SELECT * FROM DoRig WHERE Cd_ARLotto = \'' . $lotto . '\' and Cd_AR = \'' . $q . '\' and Id_DoTes in (\'' . $id_dotes . '\') Order By QtaEvadibile DESC');*/

        if (sizeof($articoli) > 0)
            $articoli = $articoli[0];
        else
            return '';
        $lotto = DB::select('SELECT * FROM ARLotto WHERE Cd_AR = \'' . $articoli->Cd_AR . '\' and Cd_ARLotto in (select Cd_ARLotto from MGMov group by Cd_ARLotto having SUM(QuantitaSign) >= 0)');

        ?>
        <script type="text/javascript">

            $('#modal_controllo_articolo').val('<?php echo $articoli->Cd_AR ?>');
            $('#modal_controllo_quantita').val(<?php echo floatval($quantita) ?>);

            $('#modal_controllo_lotto').val(
                <?php if ($lotto_scelto != 0) {
                    echo '\'' . $lotto_scelto . '\'';
                } else {
                    echo '\'Nessun Lotto\'';
                } ?>)
            $('#modal_list_controllo_lotto').html('<option value="Nessun Lotto">Nessun Lotto</option>')
            <?php foreach($lotto as $l){?>
            $('#modal_list_controllo_lotto').append('<option value="<?php echo $l->Cd_ARLotto;?>"><?php echo $l->Cd_ARLotto ?></option>')
            <?php } ?>

            $('#modal_controllo_data_scadenza').html
            <?php if($scadenza != 0){ ?>
            ('<option value="<?php echo date('d/m/Y', strtotime($scadenza)) ?>" lotto="<?php echo $articoli->Cd_ARLotto ?>" selected><?php echo date('d/m/Y', strtotime($scadenza));  ?></option>')
            <?php }else{ ?>
            ('<option lotto="Nessun Lotto" >Nessuna Scadenza</option>')
            <?php }?>
            <?php foreach($lotto as $l){?>
            $('#modal_controllo_data_scadenza').append('<option value="<?php echo date('d/m/Y', strtotime($l->DataScadenza));?>" lotto="<?php echo $l->Cd_ARLotto;?>"><?php echo date('d/m/Y', strtotime($l->DataScadenza)) ?></option>')
            <?php } ?>

            //$('#modal_controllo_lotto').val('<?php echo $articoli->Cd_ARLotto ?>');
            $('#modal_controllo_dorig').val('<?php echo $articoli->Id_DORig ?>');
            change_scad();

        </script>

        <!--        <?php
        /*        $articoli = DB::select('SELECT * FROM DoRig WHERE Cd_AR = \'' . $q . '\' and Id_DoTes in (\'' . $id_dotes . '\') Order By QtaEvadibile DESC');
                if (sizeof($articoli) > 0)
                    $articoli = $articoli[0]; */ ?>

        <script type="text/javascript">

            $('#modal_controllo_articolo').val('<?php /*echo $articoli->Cd_AR */ ?>');
            $('#modal_controllo_quantita').val(<?php /*echo floatval($articoli->Qta) */ ?>);
            $('#modal_controllo_lotto').val('<?php /*echo $articoli->Cd_ARLotto */ ?>');
            $('#modal_controllo_dorig').val('<?php /*echo $articoli->Id_DORig */ ?>');


        </script>

        --><?php
//TODO CAMBAIRE GESTIONE EVASIONE A PZ A SECONDA DEL BARCODE

    }


    /**
     * Sezione Inventario di Magazzino
     * @return mixed
     */


    public
    function cerca_articolo_inventario($barcode)
    {

        $articoli = DB::select('SELECT AR.[Id_AR],AR.[Cd_AR],AR.[Descrizione],ARLotto.[Cd_ARLotto] FROM AR LEFT JOIN ARLotto ON AR.Cd_AR = ARLotto.Cd_AR where AR.Cd_AR = \'' . $barcode . '\' or  AR.Descrizione = \'' . $barcode . '\' or AR.CD_AR IN (SELECT CD_AR from ARAlias where Alias = \'' . $barcode . '\')  Order By AR.Id_AR DESC');


        if (sizeof($articoli) == '0') {
            $decoder = new Decoder($delimiter = '');
            $barcode = $decoder->decode($barcode);
            $where = ' where 1=1  ';

            foreach ($barcode->toArray()['identifiers'] as $field) {

                if ($field['code'] == '01') {
                    $testo = trim($field['content'], '*,');
                    $where .= ' and AR.Cd_AR Like \'%' . $testo . '%\'';

                }
                if ($field['code'] == '10') {
                    $where .= ' and ARLotto.Cd_ARLotto Like \'%' . $field['content'] . '%\'';
                    $Cd_ARLotto = $field['content'];
                }

            }
            $articoli = DB::select('SELECT AR.[Id_AR],AR.[Cd_AR],AR.[Descrizione],ARLotto.[Cd_ARLotto] FROM AR LEFT JOIN ARLotto on AR.Cd_AR = ARLotto.Cd_AR ' . $where . '  Order By Id_AR DESC');

        }
        if (sizeof($articoli) > 0) {
            $articolo = $articoli[0];
            $quantita = 0;
            $disponibilita = DB::select('SELECT ISNULL(sum(QuantitaSign),0) as disponibilita from MGMOV where Cd_MGEsercizio = ' . date('Y') . ' and Cd_AR = \'' . $articolo->Cd_AR . '\'');
            if (sizeof($disponibilita) > 0) {
                $quantita = floatval($disponibilita[0]->disponibilita);
                $prova = DB::SELECT('SELECT ISNULL(sum(QuantitaSign),0) as disponibilita,Cd_ARLotto,Cd_MG from MGMOV where Cd_MGEsercizio = ' . date('Y') . ' and Cd_AR = \'' . $articolo->Cd_AR . '\' and Cd_ARLotto IS NOT NULL group by Cd_ARLotto, Cd_MG HAVING SUM(QuantitaSign)!= 0  ');
            }

            /*  echo '<h3>Disponibilit??: ' . $quantita . '</h3>';*/
            ?>
            <script type="text/javascript">
                $('#modal_Cd_AR').val('<?php echo $articolo->Cd_AR ?>');
                $('#modal_Cd_ARLotto').html('<option value="">Nessun Lotto</option>');
                <?php foreach($prova as $l){?>
                $('#modal_Cd_ARLotto').append('<option quantita="<?php echo floatval($l->disponibilita) ?>" magazzino="<?php echo $l->Cd_MG ?>" <?php echo ($Cd_ARLotto == $l->Cd_ARLotto) ? 'selected' : '' ?>><?php echo $l->Cd_ARLotto . ' - ' . $l->Cd_MG ?></option>')
                <?php } ?>

                cambioLotto();

            </script>
        <?php }
    }


    public
    function rettifica_articolo($codice, $quantita, $lotto, $magazzino)
    {

        try {
            $lotto = str_replace('slash', '/', $lotto);
            $codice = str_replace('slash', '/', $codice);
            DB::beginTransaction();

            $id_MGMovInt = DB::table('MGMovInt')->insertGetId(array('Tipo' => 0, 'DataMov' => date('Ymd'), 'Descrizione' => 'Movimenti Rettifica'));
            if ($lotto != 0) {
                DB::insert('INSERT INTO MGMoV(DataMov,PartenzaArrivo,PadreComponente,Cd_MGEsercizio,Cd_AR,Cd_MG,Quantita,Ret,Id_MgMovInt,Cd_ARLotto) VALUES (\'' . date('Ymd') . '\',\'A\',\'P\',' . date('Y') . ',\'' . $codice . '\',\'' . $magazzino . '\',' . $quantita . ',1,' . $id_MGMovInt . ',\'' . $lotto . '\' )');
            } else {
                DB::insert('INSERT INTO MGMoV(DataMov,PartenzaArrivo,PadreComponente,Cd_MGEsercizio,Cd_AR,Cd_MG,Quantita,Ret,Id_MgMovInt) VALUES (\'' . date('Ymd') . '\',\'A\',\'P\',' . date('Y') . ',\'' . $codice . '\',\'' . $magazzino . '\',' . $quantita . ',1,' . $id_MGMovInt . ' )');
            }
            echo 'Quantità Rettificata con Successo';

            DB::commit();
        } catch (\PDOException $e) {
            // Woopsy
            print_r($e);
            DB::rollBack();
        }


    }

    public
    function cerca_articolo_smart_inventario($q, $tipo)
    {
        $Cd_ARLotto = 'NESSUN LOTTO';
        if ($tipo == 'QRCode') {

            $q = explode(';', $q);

            if (sizeof($q) > 1)
                $data_scadenza = $q[1];
            else
                $data_scadenza = 0;
            if (sizeof($q) > 2 && $q[2] != '')
                $Cd_ARLotto = $q[2];
            else
                $Cd_ARLotto = 'Nessun Lotto';

            $where = ' where 1=1 ';
            $where .= ' and (AR.Cd_AR Like \'%' . $q[0] . '%\' or AR.Cd_AR = (SELECT Cd_AR FROM ARALias WHERE Alias = \'' . $q[0] . '\' ) )';

            $articoli = DB::select('SELECT [Id_AR],[Cd_AR],[Descrizione] FROM AR ' . $where . '  Order By Id_AR DESC');
            if (sizeof($articoli) > 0) {
                foreach ($articoli as $articolo) { ?>

                    <li class="list-group-item">
                        <a href="#" onclick="" class="media">
                            <div class="media-body"
                                 onclick="cerca_articolo_inventario_codice('<?php echo $articolo->Cd_AR ?>','<?php echo $Cd_ARLotto; ?>') ">
                                <h5><?php echo $articolo->Descrizione ?></h5>
                                <p>Codice: <?php echo $articolo->Cd_AR ?></p>
                            </div>
                        </a>
                    </li>

                <?php }
            } else
                echo 'Nessun Articolo Trovato';
        }
        if ($tipo == 'EAN') {
            $articoli = DB::select('SELECT [Id_AR],[Cd_AR],[Descrizione] FROM AR where (Cd_AR Like \'' . $q . '%\' or  Descrizione Like \'%' . $q . '%\' or CD_AR IN (SELECT CD_AR from ARAlias where Alias LIKE \'%' . $q . '%\'))  Order By Id_AR DESC');
            if (sizeof($articoli) > 0) {
                foreach ($articoli as $articolo) { ?>

                    <li class="list-group-item">
                        <a href="#" onclick="" class="media">
                            <div class="media-body"
                                 onclick="cerca_articolo_inventario_codice('<?php echo $articolo->Cd_AR ?>','NESSUNLOTTO')">
                                <h5><?php echo $articolo->Descrizione ?></h5>
                                <p>Codice: <?php echo $articolo->Cd_AR ?></p>
                            </div>
                        </a>
                    </li>

                <?php }
            } else
                echo 'Nessun Articolo Trovato';
        }
    }


    public
    function cerca_articolo_inventario_codice($codice, $Cd_ARLotto)
    {

        $articoli = DB::select('SELECT AR.Cd_AR from AR where Cd_AR = \'' . $codice . '\'');

        if (sizeof($articoli) > 0) {
            $articolo = $articoli[0];
            $quantita = 0;
            $disponibilita = DB::select('SELECT ISNULL(sum(QuantitaSign),0) as disponibilita from MGMOV where Cd_MGEsercizio = ' . date('Y') . ' and Cd_AR = \'' . $articolo->Cd_AR . '\'');
            if (sizeof($disponibilita) > 0) {
                $prova = DB::SELECT('SELECT ISNULL(sum(QuantitaSign),0) as disponibilita,Cd_ARLotto,Cd_MG from MGMOV where Cd_MGEsercizio = ' . date('Y') . ' and Cd_AR = \'' . $articolo->Cd_AR . '\' and Cd_ARLotto IS NOT NULL group by Cd_ARLotto, Cd_MG HAVING SUM(QuantitaSign)!= 0  ');
            }

            /* echo '<h3>Disponibilit??: ' . $quantita . '</h3>';*/
            ?>
            <script type="text/javascript">
                $('#modal_Cd_AR').val('<?php echo $articolo->Cd_AR ?>');
                $('#modal_Cd_ARLotto').html('<option magazzino="00001" value="" quantita="<?php if (sizeof($disponibilita) > 0) {
                    echo $disponibilita[0]->disponibilita;
                } else echo 0;?>">Nessun Lotto</option>');
                <?php foreach($prova as $l){?>
                $('#modal_Cd_ARLotto').append('<option quantita="<?php echo floatval($l->disponibilita) ?>" magazzino="<?php echo $l->Cd_MG ?>" <?php echo ($Cd_ARLotto == $l->Cd_ARLotto) ? 'selected' : '' ?> value="<?php echo $l->Cd_ARLotto;?>"><?php echo $l->Cd_ARLotto . ' - ' . $l->Cd_MG ?></option>')
                <?php } ?>

                cambioLotto();

            </script>
            <?php
        }

    }

    public
    function elimina($id_dotes)
    {
        DB::table('DoRig')->where('Id_DOTes', $id_dotes)->delete();
        DB::table('DOTes')->where('Id_DOTes', $id_dotes)->delete();
        echo 'Eliminato';
    }

    public
    function salva($id_dotes)
    {
        //DB::update("Update dotes set Modificabile = 0 where id_dotes = $id_dotes ");
        DB::statement("exec asp_DO_End $id_dotes");
    }

    public
    function invia_mail($id_dotes, $id_dorig, $testo)
    {
        if ($id_dorig == '1') {
            if (substr($testo, 0, 2) == '01') {
                $decoder = new Decoder($delimiter = '');
                $barcode = $decoder->decode($testo);
                $where = 'Articolo ';
                foreach ($barcode->toArray()['identifiers'] as $field) {

                    if ($field['code'] == '01') {
                        $contenuto = trim($field['content'], '*,');
                        $where .= $contenuto;

                    }
                    if ($field['code'] == '10') {
                        $where .= ' con lotto ' . $field['content'];

                    }
                    /*
                    if ($field['code'] == '310') {
                        $decimali = floatval(substr($field['raw_content'],-2));
                        $qta = floatval(substr($field['raw_content'],0,4))+$decimali/100;
                        $where .= ' and Qta Like \'%' . $qta . '%\'';
                    }*/

                }
                $where .= ' non trovato. ';
            }
        } else {
            if ($id_dorig == '2') {
                $testo = str_replace('*', '', $testo);
                $where = $testo;
            } else {
                $where = trim($testo, '-');
            }


        }

        if ($id_dorig == '3') {
            $documento = DB::SELECT('Select * from dotes where Id_DOTes = \'' . $id_dotes . '\'')[0]->Cd_Do;
            $testo = str_replace('(documento)', $documento, $testo);
            $where = $testo;
        }

    }


}
