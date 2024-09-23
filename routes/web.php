<?php

use Illuminate\Support\Facades\Route;

//use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::any('', 'HomeController@index');
Route::any('login', 'HomeController@login');
Route::any('logout', 'HomeController@logout');

Route::any('qrcode', 'HomeController@qrcode');
Route::any('resultqrcode/{alias}/{scadenza}/{lotto}', 'HomeController@resultqrcode');

Route::any('articoli', 'HomeController@articoli');
Route::any('modifica_articolo/{id}', 'HomeController@modifica_articolo');
Route::any('magazzino', 'HomeController@magazzino');
Route::any('ordini', 'HomeController@ordini');
Route::any('magazzino/attivo', 'HomeController@attivo');
Route::any('magazzino/passivi', 'HomeController@passivi');
Route::any('magazzino/altri', 'HomeController@altri');

Route::any('ajax/conferma_righe/{dorig}/{cd_mg_a}/{cd_mg_p}/{cd_do}', 'AjaxController@conferma_righe');
Route::any('ajax/inserisci_peso/{id_dotes}/{peso}', 'AjaxController@inserisci_peso');
Route::any('ajax/inserisci_numero_colli/{id_dotes}', 'AjaxController@inserisci_numero_colli');
Route::any('ajax/crea_doc_riordino/{id_dotes}', 'AjaxController@crea_doc_riordino');

Route::any('magazzino/carico', 'HomeController@carico_magazzino');
Route::any('magazzino/carico2/{cd_do}', 'HomeController@carico_magazzino2');
Route::any('magazzino/carico3/{id_fornitore}/{cd_do}', 'HomeController@carico_magazzino3');
Route::any('magazzino/carico3_tot/{id_fornitore}/{cd_do}', 'HomeController@carico_magazzino3_tot');
Route::any('magazzino/carico4/{id_fornitore}/{id_dotes}', 'HomeController@carico_magazzino4');
Route::any('magazzino/carico1/{cd_do}', 'HomeController@carico_magazzino1');
Route::any('magazzino/carico02/{cd_do}', 'HomeController@carico_magazzino02');
Route::any('magazzino/carico03/{id_fornitore}/{cd_do}', 'HomeController@carico_magazzino03');
Route::any('magazzino/carico03_tot/{id_fornitore}/{cd_do}', 'HomeController@carico_magazzino03_tot');
Route::any('magazzino/carico04/{id_fornitore}/{id_dotes}', 'HomeController@carico_magazzino04');

Route::any('magazzino/inventario', 'HomeController@inventario_magazzino');
Route::any('calcola_totali_ordine', 'HomeController@calcola_totali_ordine');

Route::any('ajax/cerca_articolo/{q}', 'AjaxController@cerca_articolo');
Route::any('ajax/cerca_fornitore_new/{q}/{dest}', 'AjaxController@cerca_fornitore_new');
Route::any('ajax/cerca_cliente_new/{q}/{dest}', 'AjaxController@cerca_cliente_new');

Route::any('ajax/cerca_articolo_inventario/{barcode}', 'AjaxController@cerca_articolo_inventario');
Route::any('ajax/cerca_articolo_inventario_codice/{codice}/{arlotto}', 'AjaxController@cerca_articolo_inventario_codice');
Route::any('ajax/rettifica_articolo/{codice}/{quantita}/{lotto}/{magazzino}', 'AjaxController@rettifica_articolo');
Route::any('ajax/cerca_articolo_smart_inventario/{q}/{tipo}', 'AjaxController@cerca_articolo_smart_inventario');


Route::any('ajax/elimina/{id_dotes}', 'AjaxController@elimina');
Route::any('ajax/salva/{id_dotes}', 'AjaxController@salva');
Route::any('ajax/barcode_add/{codice}/{scadenza}/{lotto}', 'AjaxController@barcode_add');
Route::any('ajax/segnalazione/{dotes}/{dorig}/{testo}', 'AjaxController@segnalazione');
Route::any('ajax/invia_mail/{dotes}/{dorig}/{testo}', 'AjaxController@invia_mail');
Route::any('ajax/segnalazione_salva/{dotes}/{dorig}/{testo}', 'AjaxController@segnalazione_salva');
Route::any('ajax/salva_documento1/{dotes}/{cd_do}/{magazzino_A}', 'AjaxController@salva_documento1');
Route::any('ajax/evadi_articolo/{dorig}/{qtaevasa}/{magazzino}/{ubicazione}/{lotto}/{cd_cf}/{documento}/{cd_ar}/{magazzino_A}', 'AjaxController@evadi_articolo');
Route::any('ajax/conferma_righe/{dorig}/{qtaevasa}/{magazzino}/{ubicazione}/{lotto}/{cd_cf}/{documento}/{cd_ar}/{magazzino_A}', 'AjaxController@conferma_righe');
Route::any('ajax/cerca_articolo_codice/{cd_cf}/{codice}/{Cd_ARLotto}/{qta}', 'AjaxController@cerca_articolo_codice');
Route::any('ajax/aggiungi_articolo_ordine/{id_ordine}/{codice}/{quantita}/{magazzino_A}/{ubicazione_A}/{lotto}/{magazzino_P}/{ubicazione_P}', 'AjaxController@aggiungi_articolo_ordine');
Route::any('ajax/modifica_articolo_ordine/{id_ordine}/{codice}/{quantita}/{magazzino_A}/{ubicazione_A}/{lotto}/{magazzino_P}/{ubicazione_P}', 'AjaxController@modifica_articolo_ordine');
Route::any('ajax/crea_documento/{cd_cf}/{cd_do}/{numero}/{data}', 'AjaxController@crea_documento');
Route::any('ajax/crea_documento_rif/{cd_cf}/{cd_do}/{numero}/{data}/{numero_rif}/{data_rif}/{destinazione}', 'AjaxController@crea_documento_rif');
Route::any('ajax/cerca_articolo_smart_automatico/{q}/{cd_cf}', 'AjaxController@cerca_articolo_smart_automatico');
Route::any('ajax/cerca_articolo_smart_manuale/{q}/{cd_cf}', 'AjaxController@cerca_articolo_smart_manuale');
Route::any('ajax/controllo_articolo_smart/{q}/{id_dotes}', 'AjaxController@controllo_articolo_smart');
Route::any('ajax/stampe/{id_dotes}', 'AjaxController@stampe');
Route::any('ajax/inserisci_scatolone/{id_dotes}/{ar}/{qta}', 'AjaxController@inserisci_scatolone');
Route::any('ajax/inserisci_scatolone_in_doc_evaso/{id_dotes}/{ar}/{qta}', 'AjaxController@inserisci_scatolone_in_doc_evaso');

Route::any('ajax/inserisci_lotto/{lotto}/{cd_ar}/{fornitore}/{descrizione}/{fornitore_pallet}/{pallet}', 'AjaxController@inserisci_lotto');
Route::any('ajax/visualizza_lotti/{cd_ar}', 'AjaxController@visualizza_lotti');
