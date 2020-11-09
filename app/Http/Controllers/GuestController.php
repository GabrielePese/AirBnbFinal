<?php
namespace App\Http\Controllers;
use App\Flat;
use App\Sponsor;
use App\Service;
use App\Photo;
use App\User;
use App\Visit;
use App\Message;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;



use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(){
        $flats = Flat::all();
        $sponsors = Sponsor::all();
        $date = Carbon::now();
        $photos = Photo::all();
        return view('index', compact('flats','sponsors', 'date', 'photos'));
    }


    public function search(){
      $sponsors = Sponsor::all(); 
      $services = Service::all();
      $date = Carbon::now();
      $flats = Flat::all();
      $photos = Photo::all();
      $latitude = $_COOKIE['lat'];    // prendo la latitudine tramite cookie
      $longitude = $_COOKIE['long']; // prendo la longitudine tramite cookie
      $city = '';                    //resetto il valore city
      if (empty($_COOKIE['distance'])) {    // se il cookie della distanza e'vuoto imposta distanza di 20km.
        $distance = 20;
      }
      else {
        $distance = $_COOKIE['distance'];     //altrimenti prendi la distanza scelta e passata dal cookie.
      }
      if (!(empty($latitude))) { // se viene impostato una ricerca 
        $city = $_COOKIE['city'];
        foreach ($flats as $flat) {  //cicla tutti i flat
          $id = $flat-> id;          //id e'id del flat
        $flatsNoSponsor = Flat::with('sponsors')->whereDoesntHave('sponsors', function($query) use ($id) {$query->where('flat_id', '!=', $id);})->get();   //raggruppamento in array degli appartamenti SENZA sponsor usando comando whereDoesntHave
        $flatsSponsor = Flat::with('sponsors')->whereHas('sponsors', function($query) use ($id) {$query->where('flat_id', '!=', $id);})->get();   //raggruppamento in array degli appartamenti CON sponsor usando comando whereHas  //   prendi tutti gli appartamenti con sponsor dalla tabella ponte. E prendi se il flat_id é uguale all'id della mia tabella. 
        }
      }
      else {  //se la ricerca e'vuota
        foreach ($flats as $flat) {
          $id = $flat-> id;
        $flatsNoSponsor = Flat::with('sponsors')->whereDoesntHave('sponsors', function($query) use ($id) {$query->where('flat_id', '!=', $id);})->get();
        $flatsSponsor = Flat::with('sponsors')->whereHas('sponsors', function($query) use ($id) {$query->where('flat_id', '!=', $id);})->get();
        }
      }

        return view('search', compact('flatsNoSponsor','flatsSponsor','sponsors', 'services','city','latitude','longitude','distance','flats', 'date', 'photos'));
    }

    public function searchsort(request $request){  //funzione che viene richiama da JS tramite chiamata AJAX
      if (empty($_COOKIE['nofroom'])) {   // se il numero delle stanza non e'stato filtrato impostalo a 0
        $nofroom = 0;
      }
      else {
        $nofroom = $_COOKIE['nofroom']; // nell'altro caso prendi il valore impostato
      }
      if (empty($_COOKIE['nofbed'])) { // se il numero dei letti non e'stato filtrato impostalo a 0
        $nofbed = 0;
      }
      else {
        $nofbed = $_COOKIE['nofbed'];
      }
        $data = $request -> all(); //prendo tutti i dati
        $srvs = $data['service'];  //prendo lúnico indice-chiave servizi 
        if(isset($srvs)){     // se la veriabile é stat filtrata
            $arraySrvs = explode(',', $srvs);    // crea array e lo divide in base alle ,
        } else {
            $arraySrvs = [];
        }
        $flats = Flat::where([['disactive', '=', '0'],['deleted', '=', '0'],['number_of_bed', '>=', $nofbed],['number_of_room', '>=', $nofroom]]) -> get();  //prende dalla tabella flat solo gli appartamenti che soddisfano queste condizioni.
        foreach ($flats as $flat) {                                                                                                                          
            $services = Flat::findOrFail($flat['id']) -> services() -> get();         // 
            $aptSrvs = [];
            foreach ($services as $service) {
                $aptSrvs[] = $service['id'];
            }
            $containsAllValues = !array_diff($arraySrvs, $aptSrvs);  //viene confrontato l'array $arraySrvs che sono i servizi filtrati con i servizi che abbiamo nei nostri appartamenti.

            $flat['services'] = $containsAllValues;
        }
        $response = $flats -> where('services', '=', true); // restituisce tutti gli appartamenti dove viene trovato un match  cioe'i siervizi sono impostati a true(1)
        return response() -> json($response);   
 
    }

    public function show($id){
        $flats = Flat::all();
        $flat = Flat::findOrFail($id);
        $visits = Visit::all();
        $photo = Photo::all();
        $services = Service::all();
        $messages = Message::all();
        $date = Carbon::now();
        $photos = Photo::all();

        if (empty(Auth::user()-> id) || (Auth::user()-> id != $flat['user_id']) ){  //se utente attuale e' guest O  se non e'il proprietario dell'appartemteno.
          if (!(empty($visits->first() ))) { //se esiste il contenuto nella prima riga della tabella visits
            $data_hour_now = Carbon::now()-> format('YmdH');  //prende l'ora in cui viene visualizzata la show nel formatto ymdh
            $conteggio_aumentato=false;  // inizzializiamo una variabile di appoggio per contare
            foreach ($visits as $visit) { // comincio a ciclare ma non scrivo mai niente
              $from_date2hour = Carbon::parse($visit['date'])-> format('YmdH');  // prendo la data all'interno dellla tabella visit, il parse serve a renderlo leggibile a carbon e la metto nel formato che mi serve
              if ($visit['flat_id'] == $flat['id'] && $data_hour_now == $from_date2hour){  // se trovo un match allora modifico la variabile d'appoggio e scrivo sul DB (quindi sono nella stessa ora)
                $visit2up = $visit['counter'] += 1; 
                $visit -> update(array('counter' => $visit2up)); 
                $conteggio_aumentato=true; // la variabile d'appoggio cambio
                break;
              }
            }
            if($conteggio_aumentato == false){ // se tutto il ciclo è falso e la variabile non è cambiata allora creo un nuova riga. stesso giorno ora diversa
              $data = ['date' => $date,
              'flat_id' => $id,
              'counter' => 1];
            }
          }
          else { // se nella tabella non esiste nulla allora scrivo una nuova riga (prima volta che viene visualizzato un appartamento)
            $data = ['date' => $date,
            'flat_id' => $id,
            'counter' => 1];
            $row_visit = Visit::create($data);
          }
        }
        return view('show', compact('flat','flats','photo','services', 'photos'));
    }

    public function storeMessagesGuest(Request $request, $id){
        $data = $request -> all();
        $data = $request -> validate([
            'name' => ['required', 'min:2', 'max:40'],
            'email' => ['required', 'string', 'email', 'max:255', 'regex:/^(?=.*\.)/'], // dopo string ho email cosi lui controlla che ci sia la @ e la regex mi controlla che ci sia almeno un . Lui controlla quello che c'e'dopo questo scittia "regex:/^(?=.*\"
            'subject' => ['required', 'min:2', 'max:40'],
            'message' => ['required', 'min:2', 'max:255']
            ]);
        $data['flat_id'] = $id;
        $message = Message::create($data);
        return redirect() -> route('index')-> with('status', 'Messaggio Inviato');
    }

}
