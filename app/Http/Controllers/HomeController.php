<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\transaction;
use App\transactionType;
use App\User;
use App\aircraft;
use App\sailplaneStartPrice;
use App\flight;
use App\flightDay;
use App\usersAttributes;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $transactions = array();
        $transactionsData = transaction::where('idUser',  Auth::user()->id)
                                        ->orderBy('time', 'asc')
                                        ->orderBy('id', 'ASC')
                                        ->get();
        foreach ($transactionsData as $key => $value) {
            $transactions[] = ['time'=> date('d/m/Y H:i', $value->time), 'value' => number_format(($value->value/100), 2), 'solde' => number_format(($value->solde/100), 2), 'name' => $value->name, 'quantity' => $value->quantity, 'valid' => $value->valid, 'observation' => $value->observation];
        }

        $attributes = usersAttributes::where('userId', Auth::user()->id)->get();
        //var_dump($transaction);
        return view('home', ['userAttributes' => $attributes, 'transactions' => $transactions, 'solde' => number_format(($this->getSolde(Auth::user()->id)/100), 2)]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function saisie(Request $request)
    {

        $selectedUser = 0;
        $users = User::all();
        $transactions = array();
        if (isset($request->selectUserInTransaction)) {

            if (isset($request->selectTransactionType)) {
                if (!isset($request->valueTransaction)) {
                    $request->valueTransaction = 0;
                }

                if ($request->selectTransactionTypeEnc == 1) {
                    $request->valueTransaction = (0-$request->valueTransaction);
                }
                $this->saveTransaction($request->selectUserInTransaction, $request->selectTransactionType, $request->valueTransaction);
            }

            if (isset($request->nameFreeTransaction))
            {
                $this->saveFreeTransaction($request->selectUserInTransaction, $request->nameFreeTransaction, $request->valueFreeTransaction);
            }

            $selectedUser = $request->selectUserInTransaction;
            $transactionsUser = transaction::where('idUser',  $request->selectUserInTransaction)
                                            ->orderBy('time', 'asc')
                                            ->orderBy('id', 'ASC')
                                            ->get();
            foreach ($transactionsUser as $key => $value) {
                $transactions[] = ['time'=> date('d/m/Y H:i', $value->time), 'value' => number_format(($value->value/100), 2), 'solde' => number_format(($value->solde/100), 2), 'name' => $value->name, 'id' => $value->id, 'observation' => $value->observation];
            }
        }

        $transactionType = array();

        $transactionTypeData = transactionType::all();
        foreach ($transactionTypeData as $key => $value) {
            $value->name = $value->name.' '.date('Y');
            $transactionType[] = $value;
        }

        $aircraft = aircraft::all();
        $sailplaneStartPrice = sailplaneStartPrice::all();

        return view('transaction', ['users' => $users, 'transactions' => $transactions, 'selectedUser' => $selectedUser, 'transactionType' => $transactionType, 'aircrafts' => $aircraft, 'sailplaneStartPrices' => $sailplaneStartPrice]);
    }

    private function getSolde($user)
    {
        $solde = 0;
        $transaction = transaction::where('idUser', $user)->orderBy('time', 'desc')->first();
        if (isset($transaction->solde)) {
            $solde = $transaction->solde;
        }
        return $solde;
    }

    private function saveTransaction($user, $type, $amount)
    {
        $transaction = new transaction();
        $transaction->idUser = $user;
        $transaction->name = $type;
        $transaction->value = intval($amount*100);
        $transaction->solde = ($this->getSolde($user)+$transaction->value);
        $transaction->year = date('Y');
        $transaction->time = time();
        $transaction->save();
    }

    private function saveFreeTransaction($user, $name, $amount)
    {
        $transaction = new transaction();
        $transaction->idUser = $user;
        $transaction->name = $name;
        $transaction->value = intval($amount*100);
        $transaction->solde = ($this->getSolde($user)+$transaction->value);
        $transaction->year = date('Y');
        $transaction->time = time();
        $transaction->save();
    }

    public function deleteLastTransaction(Request $request)
    {
        if (isset($request->deleteLastUserTransaction)) {
            $lastTransaction = transaction::where('idUser', $request->deleteLastUserTransaction)
               ->orderBy('time', 'desc')
               ->limit(1)
               ->delete();
        }
        
        return redirect('saisie?selectUserInTransaction='.$request->deleteLastUserTransaction);
    }

    public function addFlightDay(Request $request)
    {
        $alreadyRegister = flightDay::where('userId', Auth::user()->id)->where('date', $request->date)->get();
        if (count($alreadyRegister) == 0) {
            $flightDay = new flightDay();
            $dateExploded = explode('/', $request->date);
            $flightDay->date = $dateExploded[2].'-'.$dateExploded[1].'-'.$dateExploded[0];
            $flightDay->userId = Auth::user()->id;
            $flightDay->state = $request->attribute;
            $flightDay->observation = $request->observation;
            $flightDay->save();
            return 'OK|Votre inscription le '.$request->date.' en tant que '.$request->attribute.' a été pris en compte.';
        } else {
            return 'ERROR|Vous êtes déjà inscrit le '.$request->date;
        }
    }

    public function getFlightDay()
    {
        $flightDaysDB = flightDay::whereDate('date', '>=', date('Y-m-d'))->orderBy('date', 'asc')->orderBy('state', 'desc')->get();
        $flightDays = array();
        $currentDate = date('Y-m-d');
        foreach ($flightDaysDB as $key => $value) {
            $dateExploded = explode('-', $value->date);
            $newDate = $dateExploded[2].'/'.$dateExploded[1].'/'.$dateExploded[0];
            $user = User::find($value->userId);
            if (Auth::user()->id == $value->userId && $value->date > $currentDate) {
                $deleteButton = '&nbsp;<button class="btn btn-outline-danger btn-sm float-right" onclick="deleteFlightDayRegister('.$value->id.')"><i data-feather="trash" style="width: 16px;
  height: 16px;"></i></button>';
            } else {
                $deleteButton = '';
            }
            $flightDays[$newDate]['USER'][] = $user->name.' ('.$value->state.')'. $deleteButton;
            $flightDays[$newDate]['OBSERVATION'][] = $value->observation;
            $flightDays[$newDate]['DATE'] = $newDate;
        }
        return view('flightDayBoard', ['flightDays' => $flightDays]);
    }

    public function deleteFlightDay(Request $request)
    {
        $flightDay = flightDay::find($request->id);
        if ($flightDay->userId == Auth::user()->id) {
            $flightDay->delete();
        }
    }

    public function addPay(Request $request)
    {
        switch ($request->type) {
            case 'CB':
                $type = 'CB';
                break;
            case 'CH':
                $type = 'Chèque';
                break;
            case 'VI':
                $type = 'Virement';
                break;
        }
        $transaction = new transaction();
        $transaction->idUser = Auth::user()->id;
        $transaction->solde = ($this->getSolde(Auth::user()->id) + intval($request->amount*100));
        $transaction->value = intval($request->amount*100);
        $transaction->name = "Paiement ".$type." ".date('Y');
        $transaction->valid = 0;
        $transaction->quantity = 0;
        $transaction->year = date('Y');
        $transaction->time = time();
        $transaction->observation = $request->observation;
        $transaction->sendEmail = $request->mail;
        $transaction->save();
    }

    private function updateFlightTimestamp()
    {
        $flights = flight::where('flightTimestamp', '')->orWhere('flightTimestamp', NULL)->get();
        foreach ($flights as $key => $value) {
            $value->flightTimestamp = strtotime(str_replace('/', '-', $value->takeOffTime));
            $value->save();
        }

    }

    private function convertMinToHM($minutes)
    {
        if ($minutes < 60) {
            return $minutes." Minutes";
        } else {
            $hourR = intval($minutes/60);
            $minutesR = $minutes-($hourR*60);
            if ($minutesR == 0) {
                return $hourR . " Heures";
            } else {
                return $hourR . " Heures " . $minutesR ." Minutes";
            }
        }
    }

    public function planches(Request $request)
    {
        $this->updateFlightTimestamp();
        $flights = array();
        if (!isset($request->start) || !isset($request->end)) {
            $start = strtotime(date('01-01-Y 00:00'));
            $end = strtotime(date('d-m-Y 23:59'));
            $startInput = date('01/01/Y');
            $endInput = date('d/m/Y');
        } else {
            $start = strtotime(str_replace('/', '-', $request->start).' 00:00');
            $end = strtotime(str_replace('/', '-', $request->end).' 23:59');
            $startInput = $request->start;
            $endInput = $request->end;
        }
        $flightsData = flight::where('flightTimestamp', '>=', $start)->where('flightTimestamp', '<=', $end)->orderBy('flightTimestamp', 'ASC')->get();
        foreach ($flightsData as $key => $value) {
            $flightArray = array();
            $flightArray['aircraft'] = aircraft::find($value->aircraftId)->name;
            $flightArray['pilot'] = User::find($value->idUser)->name;
            $flightArray['startDate'] = $value->takeOffTime;
            $flightArray['endDate'] = $value->landingTime;
            $flightArray['nbLanding'] = $value->landing;
            $flightArray['flighTime'] = $this->convertMinToHM($value->totalTime);
            if (aircraft::find($value->aircraftId)->type == 2) {
                $flightArray['startType'] = sailplaneStartPrice::find($value->startType)->name;
            } else {
                $flightArray['startType'] = 'Autonome';
            }
            

            $flights[] = $flightArray;
        }
        return view('planches', ['flights' => $flights, 'dates' => [$startInput, $endInput]]);
    }

    public function carnet(Request $request)
    {
        $this->updateFlightTimestamp();
        $flights = array();
        if (!isset($request->start) || !isset($request->end)) {
            $start = strtotime(date('01-01-Y 00:00'));
            $end = strtotime(date('d-m-Y 23:59'));
            $startInput = date('01/01/Y');
            $endInput = date('d/m/Y');
        } else {
            $start = strtotime(str_replace('/', '-', $request->start).' 00:00');
            $end = strtotime(str_replace('/', '-', $request->end).' 23:59');
            $startInput = $request->start;
            $endInput = $request->end;
        }
        $flightsData = flight::where('idUser', Auth::user()->id)->where('flightTimestamp', '>=', $start)->where('flightTimestamp', '<=', $end)->orderBy('flightTimestamp', 'ASC')->get();
        foreach ($flightsData as $key => $value) {
            $flightArray = array();
            $flightArray['aircraft'] = aircraft::find($value->aircraftId)->name;
            $flightArray['startDate'] = $value->takeOffTime;
            $flightArray['endDate'] = $value->landingTime;
            $flightArray['nbLanding'] = $value->landing;
            $flightArray['flighTime'] = $this->convertMinToHM($value->totalTime);
            if (aircraft::find($value->aircraftId)->type == 2) {
                $flightArray['startType'] = sailplaneStartPrice::find($value->startType)->name;
            } else {
                $flightArray['startType'] = 'Autonome';
            }
            

            $flights[] = $flightArray;
        }
        return view('carnetVol', ['flights' => $flights, 'dates' => [$startInput, $endInput]]);
    }
}
