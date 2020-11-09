<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Flat;
use App\Sponsor;
use App\Service;
use App\Photo;
use App\User;
use App\Visit;
use App\Message;
use Carbon\Carbon;



class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
// Home->Login/id(user)->BecomeHost->Form(Host)->


    public function becomeHost(){   // Mostra il form per create diventa Host.
        $flats = Flat::all();
        $services = Service::all();
        return view('become_host', compact('flats', 'services'));
    }

    public function update($id){   // Mostra il form per fare il modifica 
        $flats = Flat::all();
        $flat = Flat::findOrFail($id);
        $services = Service::all();
        return view('edit_flat', compact('flat', 'services','flats'));
    }

    public function editFlat(Request $request, $id){   

        $services = Service::all();
        $data = $request -> all();

        $data = $request -> validate([
            'title' => ['required', 'string', 'min:5', 'max:80'],
            'description' => ['required', 'string', 'min:5', 'max:1000'],
            'type' => 'required',
            'photo_url' => 'required|image|mimes:JPG,jpeg,png,jpg,webp',
            'price_at_night' => 'required|integer|gte:1',
            'mq' => 'required|integer|gte:5',
            'number_of_bed' => 'required',
            'number_of_bathroom' => 'required',
            'number_of_room' => 'required',
            'WiFi' => 'integer',
            'Parking_Spot' => 'integer',
            'Pool' => 'integer',
            'Reception' => 'integer',
            'Sauna' => 'integer',
            'Sea_View' => 'integer',
            'latitude' => 'string',
            'longitude' => 'string',
            'address' => ['required', 'regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9 \-_\,ìùàòèé]+)$/']  // ci deve essere un numero, e ci deve essere almeno una lettera Mausciola e una minuscola e PUO esserci un caratettere  speciale cioe' -_\,ìùàòèé
            ]);




        // prendo tutta l'array dalla request
        $imagePath = $request-> photo_url;
        // prendo solo in nome originale
        $imageName = $imagePath->getClientOriginalName();
        // creo una variabile con dentro le info per per il savataggio e faccio il prepend della data attuale in secondi per evitare conflitti nel nome
        $filePath = $request-> photo_url ->storeAs('images', time().$imageName, 'public');
        // aggiungo la stringa del percorso /storage/ da aggiungere al DB
        $data['photo_url'] = '/storage/'.$filePath;




        $userid = Auth::user()-> id;
      
        $data['user_id'] = $userid;   // prendo da data l'user_id della tabella e gli assegno l'id dell'Utente attuale 
       


        $flat = Flat::where('id', $id)->update($data);
       
    return redirect() -> route('index') -> with('status', 'Appartamento Modificato!!!');
    }

   
    public function storehost(Request $request, $id ){   //$id => $id(user)     // nel form al momento de click su submit per far diventare un User->Host

        $services = Service::all();
        $data = $request -> all();

        $data = $request -> validate([
            'title' => ['required', 'string', 'min:5', 'max:80'],
            'description' => ['required', 'string', 'min:5', 'max:1000'],
            'type' => 'required',
            'photo_url' => 'required|image|mimes:JPG,jpeg,png,jpg,webp',
            'price_at_night' => 'required|integer|gte:1',
            'mq' => 'required|integer|gte:5',
            'number_of_bed' => 'required',
            'number_of_bathroom' => 'required',
            'number_of_room' => 'required',
            'WiFi' => 'integer',
            'Parking_Spot' => 'integer',
            'Pool' => 'integer',
            'Reception' => 'integer',
            'Sauna' => 'integer',
            'Sea_View' => 'integer',
            'latitude' => 'string',
            'longitude' => 'string',
            'address' => ['required', 'regex:/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9 \-_\,ìùàòèé]+)$/']
            ]);


        // prendo tutta l'array dalla request
        $imagePath = $request-> photo_url;
        // prendo solo in nome originale
        $imageName = $imagePath->getClientOriginalName();
        // creo una variabile con dentro le info per per il savataggio e faccio il prepend della data attuale in secondi per evitare conflitti nel nome
        $filePath = $request-> photo_url ->storeAs('images', time().$imageName, 'public');
        // aggiungo la stringa del percorso /storage/ da aggiungere al DB
        $data['photo_url'] = '/storage/'.$filePath;







        // prendo da data l'user_id della tabella e gli assegno l'id dell'Utente attuale
        $data['user_id'] = $id;
        // $user = User::findOrFail($id);

        $flat = Flat::create($data);


        if (isset($data['WiFi'])) {  //se  wifi e'stato checckato nel form
          $wifi_id = $data['WiFi'];
          $service = Service::findOrFail($wifi_id); 
          $service -> flats() -> attach($flat); // scrivo all'intero della tabella ponte collegando il servizio con il flat tramite l'uso dell funzione flats() del model service
        }
        if (isset($data['Parking_Spot'])) {
          $park_id = $data['Parking_Spot'];
          $service = Service::findOrFail($park_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Pool'])) {
          $pool_id = $data['Pool'];
          $service = Service::findOrFail($pool_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Reception'])) {
          $rece_id = $data['Reception'];
          $service = Service::findOrFail($rece_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Sauna'])) {
          $sauna_id = $data['Sauna'];
          $service = Service::findOrFail($sauna_id);
          $service -> flats() -> attach($flat);
        }
        if (isset($data['Sea_View'])) {
          $sea_id = $data['Sea_View'];
          $service = Service::findOrFail($sea_id);
          $service -> flats() -> attach($flat);
        }

        return redirect() -> route('index') -> with('status', 'Nuovo Appartamento Creato!!!');
    }

    public function storeMessagesUser(Request $request, $id){ //e'una create, creo una nuova riga allínterno della tabella messaggi
        $usermail = Auth::user()-> email;
        $data = $data = $request -> all();
        $data = $request -> validate([
            'name' => ['required', 'min:2', 'max:40'],
            'subject' => ['required', 'min:2', 'max:40'],
            'message' => ['required', 'min:2', 'max:255']
            ]);
        $data['flat_id'] = $id;
        $data['email'] = $usermail; //questa prende direttamente la mail del guest per cosi no la devo inserire nel form del messaggio.
        $message = Message::create($data);

        return redirect() -> route('show',$id)-> with('status', 'Messaggio Inviato');
    }

    public function showProfile(){ //mostra profilo utente
        $flats = Flat::all();
        $sponsors = Sponsor::all();
        $photos = Photo::all();
        $date = Carbon::now();  // passo anche la data per fare le statischitiche delle visite della show.

        return view('profile', compact('flats','sponsors', 'date', 'photos'));
    }

    public function sponsorForm($id){  //for
        $flats = Flat::all();
        $flat = Flat::findOrFail($id);
        
        $sponsors = Sponsor::all();
        return view('sponsor_form', compact('sponsors', 'flat','flats'));
    }

    public function sponsorPayment(Request $request, $id){ //id del Flat
        $data = $request -> all();
        $flat = Flat::findOrFail($id);
        $data = $request -> validate([
            'sponsor' => ['required']     
            ]);
        $sponsor_array = explode('/', $data['sponsor']);  //esplodiamo lárray con gli / 
        $sponsorId = $sponsor_array[0];   // qui mi dice id dello sponsor che ho selezioneato 1,2, o 3 visto che ne ho solo 3
        $sponsorDur = $sponsor_array[1];    // qui seleziona la durata dello sponsor
        $sponsor = Sponsor::findOrFail($sponsorId); 
        $flat = Flat::findOrFail($id);


        $date = Carbon::now(); 
        $carbon_date = Carbon::parse($date); 
        $carbon_date->addHours($sponsorDur); // aggiunge all'orario attuale le ore scelte dalla sponsor.

        $var1= Carbon::parse($carbon_date);



        
        $sponsor-> flats() -> attach($flat, ['date_end'=> $carbon_date]);  // prende la riga del DB di "Sponsor" e la associa alla riga del DB di Flat. serve per scrivere nella tabella ponte.
        return redirect() -> route('profile') -> with('status', 'Pagamento approvato!!!');
    }

    public function sponsorFormUpdate($id){  //uguale allo sponsorForm solo che e'una rotta diversa puoi cliccarlo solo quando e' scaduto.
        $flats = Flat::all();
        $flat = Flat::findOrFail($id);
        $sponsors = Sponsor::all();
        return view('sponsor_form_update', compact('sponsors', 'flat','flats'));
    }

    public function sponsorPaymentUpdate(Request $request, $id){ //id del Flat
        $data = $request -> all();
        $data = $request -> validate([
            'sponsor' => ['required']
            ]);
        $sponsor_array = explode('/', $data['sponsor']);  //qui fa explode quindi stringa lo fa diventare array.
        $sponsorId = $sponsor_array[0]; // qui definisce lo sponsorID che e' l'elemento dell'array con indice 0 
        $sponsorDur = $sponsor_array[1]; // qui definisce lo sponsor duration che e' l'elemento dell'array con indice 1 (24h, 72, 144h)
        $sponsor = Sponsor::findOrFail($sponsorId);
        $flat = Flat::findOrFail($id);


        $date = Carbon::now();
        $carbon_date = Carbon::parse($date);
        $carbon_date->addHours($sponsorDur); // aggiunge le ore che prende la sponsor duration e le aggiunge all'ora attuale

        $var1= Carbon::parse($carbon_date);

    
        $flat_sponsorID = $flat-> sponsors-> first()-> pivot-> id; //prende il primo id dalla tabella ponte tra flats_sponsors, ci serve per fare update della sponsorizzata
        $spoID = $flat-> sponsors-> first()-> pivot-> sponsor_id; // prende lo sponsor_id della tabella ponte

        $flat-> sponsors()->wherePivot('id',$flat_sponsorID)->updateExistingPivot($spoID, ['sponsor_id' => $sponsorId,'date_end' => $carbon_date]);  // prende la riga del DB di "Sponsor" e la associa alla riga del DB di Flat ed esegue lúpdate senza creare nuovi elementi



        return redirect() -> route('profile') -> with('status', 'Pagamento approvato!!!');
    }

    public function disable($id){  //per disabilitare appartamento
        $flat = Flat::findOrFail($id);
        $flat -> update(array('disactive' => 1));
        return redirect() -> route('profile');
    }
    public function enable($id){ //per abilitarlo
        $flat = Flat::findOrFail($id);
        $flat -> update(array('disactive' => 0));
        return redirect() -> route('profile');
    }
    public function delete($id){ //esergue un delete logico, cioe' lo toglie dal sito e lo trovi solo sul DB. (viene nascosto)
        $flat = Flat::findOrFail($id);
        $flat -> update(array('deleted' => 1));
        return redirect() -> route('profile');
    }
    public function showMessage($id){ //mostra mex
        $flat = Flat::findOrFail($id);
        $flats = Flat::all();
        $messages = Message::all();
        $photos = Photo::all();
        return view('message', compact('flat', 'messages','flats', 'photos'));
    }

    public function showStats($id){  //parte dedicata alle statistiche dell'appartamento

      $flats = Flat::all();
      $flat = Flat::findOrFail($id);
      $visits = Visit::all();
      $messages = Message::all();
      $visitTOT = 0;
      $visitTOTtoday = 0;
      $messageTOT = 0;
      $massageTOTtoday = 0;
      $dataVisits = [  //abbiamo creato un array delle visite con le varie ore del giorno inizializzate a 0. La chiave e'la data esperessa in ore 
        0=>0,
        1=>0,
        2=>0,
        3=>0,
        4=>0,
        5=>0,
        6=>0,
        7=>0,
        8=>0,
        9=>0,
        10=>0,
        11=>0,
        12=>0,
        13=>0,
        14=>0,
        15=>0,
        16=>0,
        17=>0,
        18=>0,
        19=>0,
        20=>0,
        21=>0,
        22=>0,
        23=>0
      ];
      $dataMessages= [ //abbiamo creato un array dei messaggi con le varie ore del giorno inizializzate a 0. La chiave e'la data esperessa in ore.
        0=>0,
        1=>0,
        2=>0,
        3=>0,
        4=>0,
        5=>0,
        6=>0,
        7=>0,
        8=>0,
        9=>0,
        10=>0,
        11=>0,
        12=>0,
        13=>0,
        14=>0,
        15=>0,
        16=>0,
        17=>0,
        18=>0,
        19=>0,
        20=>0,
        21=>0,
        22=>0,
        23=>0
      ];

      foreach ($visits as $visit) { 
        $from_date2hour = Carbon::parse($visit['date'])-> format('Ymd'); // prendo dal singolo visit la data e la converto in formato anno-mese-giorno
        $data_hour_now = Carbon::now()-> format('Ymd');
        $counter = $visit['counter']; 

        if ($visit['flat_id'] == $id) { //se il flat_id di visit e'uguale alL'ID che ho passato come argomento, quindi all'ID del flat che sto guardando io
          $visitTOT += $counter; //prendi il valore nella colonna counter (cioe'le visite) e salvalo in visitTOT
          if ( $data_hour_now == $from_date2hour) { // se il giorno attuale e'uguale al giorno della visita 
            $visitTOTtoday += $counter; 
            $hour = intval(Carbon::parse($visit['date'])-> format('H')); //converto a intero con INTVAL   al formato hour.
            $dataVisits[$hour] = $counter; //salva nell'array datavist all'indice [ ora attuale ] il valore del counter . Esempio: se sono le 14   lui salvera'in $dataVisits[14] il valore di coubter
          }
        }
      }

      foreach ($messages as $message) {
        $from_day = Carbon::parse($message['created_at'])-> format('Ymd');
        $data_day_now = Carbon::now()-> format('Ymd');
        if ($message['flat_id'] == $id) {
            $messageTOT += 1;
            if ( $data_day_now == $from_day) {
              $massageTOTtoday += 1;
              $from_date2hour = Carbon::parse($message['created_at'])-> format('H');
              $data_hour_now = Carbon::now()-> format('H');
              if ($from_date2hour == $data_hour_now) {
                $hour = intval(Carbon::parse($message['created_at'])-> format('H'));
                $dataMessages[$hour] +=1;
              }
            }
        }
      }

      $datamessage = implode(' ' , $dataMessages); // qui trasformiamo da array a stringa. alla fine di ogni valore lui ci mette uno spazio
      $datavisit = implode(' ' , $dataVisits);
      return view('stats', compact('visitTOT','visitTOTtoday', 'messageTOT','massageTOTtoday','datavisit','datamessage','flats'));
}

public function photo($id){
  $flats = Flat::all();
    $photos = Photo::all();
    $flat = Flat::findOrFail($id);
    return view('photo', compact('photos','flat','flats'));
}

public function storePhoto(Request $request , $id){
  $data = $request -> all();
  $data = $request -> validate(['photo_url' => 'required|image|mimes:JPG,jpeg,png,jpg,webp']);
  $data['flat_id'] = $id;
  // prendo tutta l'array dalla request
  $imagePath = $request-> photo_url;
  // prendo solo in nome originale
  $imageName = $imagePath->getClientOriginalName();
  // creo una variabile con dentro le info per per il savataggio e faccio il prepend della data attuale in secondi per evitare conflitti nel nome
  $filePath = $request-> photo_url ->storeAs('images', time().$imageName, 'public');
  // aggiungo la stringa del percorso /storage/ da aggiungere al DB
  $data['photo_url'] = '/storage/'.$filePath;
  $photo = Photo::create($data);
    return redirect() -> route('profile');
}

}
