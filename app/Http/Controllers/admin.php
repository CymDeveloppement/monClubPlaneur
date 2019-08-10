<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\transaction;
use App\transactionType;
use App\User;
use App\aircraft;
use App\sailplaneStartPrice;
use App\flight;
use App\usersAttributes;

class admin extends Controller
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

    public function addUser(Request $request)
    {
    	$userExist = User::where('email', $request->mail)->get();
    	if (count($userExist) > 0) {
    		return 'ERROR|Cette adresse e-mail est dèjà utilisé';
    	}

    	$user = new User();
		//$user->password = Hash::make(uniqid());
		$user->password = Hash::make('TEST');
		$user->email = $request->mail;
		$user->name = $request->name;
		$user->licenceNumber = $request->licence;
		$user->save();

		$newTrAccount = new transaction();
		$newTrAccount->idUser = $user->id;
		$newTrAccount->name = 'Ouverture de compte';
		$newTrAccount->value = 0;
		$newTrAccount->solde = 0;
		$newTrAccount->year = date('Y');
        $newTrAccount->time = time();
        $newTrAccount->save();

        foreach ($request->state as $key => $value) {
        	$attributes = new usersAttributes();
        	$attributes->userId = $user->id;
        	$attributes->attributeName = $value;
        	$attributes->save();
        }

		return 'OK|L\'utilisateur a été ajouté.';
    }

    private function getSolde($user)
    {
        $solde = 0;
        $transaction = transaction::where('idUser', $user)->orderBy('time', 'desc')->orderBy('id', 'desc')->first();
        if (isset($transaction->solde)) {
            $solde = $transaction->solde;
        }
       
        return $solde;
    }

    public function usersList()
    {
    	$users = User::all();
    	$attribute = usersAttributes::all();
    	$allDataUsers = array();
    	$allAttributes = array();
    	foreach ($attribute as $key => $value) {
    		$allAttributes[$value->userId][] = $value->attributeName; 
    	}
    	foreach ($users as $key => $value) {
    		$allDataUsers[$value->id] = $value;
    		$allDataUsers[$value->id]->solde = number_format(($this->getSolde($value->id)/100), 2);
    		if (isset($allAttributes[$value->id])) {
    			$allDataUsers[$value->id]->userAttributes = $allAttributes[$value->id];
    		} else {
    			$allDataUsers[$value->id]->userAttributes = array();
    		}
    	}
    	
        return view('usersList', ['users' => $allDataUsers]);
    }

    public function getValidTransactions()
    {
    	$transactions = transaction::where('valid', 0)->get();
    	$allDataTransactions = array();
    	foreach ($transactions as $key => $value) {
    		$user = User::find($value->idUser);
    		$value->CompleteName = $user->name;
    		$allDataTransactions[] = $value;
    	}
    	return view('validTransactions', ['transactions' => $allDataTransactions]);
    }

    public function ValidTransactions(Request $request)
    {
    	$transaction = transaction::find($request->id);
    	$transaction->valid = 1;
    	$transaction->save();
    	if ($transaction->sendEmail == 1) {
    		# code...
    	}
    }

    public function validNewTrDate(Request $request)
    {
    	$newTime = strtotime(str_replace('/', '-', $request->date));
    	$transaction = transaction::find($request->id);
    	$transaction->year = date('Y', $newTime);
    	$transaction->time = $newTime;
    	$userId = $transaction->idUser;
    	$transaction->save();
    	
    	/*
    	$transactions = transaction::where('idUser', $request->selectUserInTransaction)->orderBy('time', 'asc')->orderBy('id', 'ASC')->get();
    	$solde = 0;
    	foreach ($transactions as $key => $value) {
    		$transactions[$key]->solde = $solde+$value->value;
    		$solde = $transactions[$key]->solde;
    		$transactions[$key]->save();
    	}
		*/
    	return redirect('updateSolde?selectUserInTransaction='.$userId);
    	
    }

    public function updateSolde(Request $request)
    {
    	$transactions = transaction::where('idUser', $request->selectUserInTransaction)->orderBy('time', 'asc')->orderBy('id', 'ASC')->get();
    	$solde = 0;
    	foreach ($transactions as $key => $value) {
    		$transactions[$key]->solde = $solde+$value->value;
    		$solde = $transactions[$key]->solde;
    		$transactions[$key]->save();
    	}

    	return redirect('saisie?selectUserInTransaction='.$request->selectUserInTransaction);
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

    public function validNewAdminFlight(Request $request)
    {
    	$flight = new flight();
    	$flight->idUser = $request->user;
    	$flight->totalTime = $request->flightTime;
    	$flight->takeOffTime = $request->takeOffDate;
    	$flight->landingTime = $request->landingDate;
    	$flight->landing = $request->nbTakeOff;
    	$flight->aircraftId = $request->aircraft;
    	$flight->motorStartTime = $request->startMotor;
    	$flight->motorEndTime = $request->endMotor;
    	$flight->airPortStartCode = 'LFCT';
    	$flight->airPortEndCode = 'LFCT';
    	$flight->startType = $request->startType;

    	$aircraft = aircraft::find($request->aircraft);
    	echo $request->endMotor;
    	echo $request->startMotor;
    	$price = 0;
    	switch ($request->aircraftType) {
    		case 1:
    			$price = ((($request->endMotor*100)-($request->startMotor*100))*$aircraft->motorPrice)
    						+($request->flightTime*($aircraft->basePrice/60)*100);
    			$price = intval($price);
    			$transacObservation = $aircraft->name." (".$aircraft->register.") - "
    									.$this->convertMinToHM($request->flightTime)." - Moteur : ".((floatval($request->endMotor)*100)-(floatval($request->startMotor)*100))." centièmes";

    			break;
    		case 2:
    			$startType = sailplaneStartPrice::find($request->startType);
    			$price = ($request->nbTakeOff*$startType->basePrice)+($request->flightTime*($aircraft->basePrice/60)*100);
    			$transacObservation = $aircraft->name." (".$aircraft->register.") - "
    									.$this->convertMinToHM($request->flightTime)." - Lancement : ".$request->nbTakeOff.' X '.$startType->name;
    			break;
    		default:
    			return;
    			break;
    	}

    	$flight->value = $price;
    	$transacTitle = "HDV : ".$aircraft->name;

    	$transaction = new transaction();
    	$transaction->idUser = $flight->idUser;
    	$transaction->name = $transacTitle;
    	$transaction->value = 0-($flight->value);
    	$transaction->quantity = 1;
    	$transaction->valid = 1;
    	$transaction->solde = 0.0;
    	$transaction->time = strtotime(str_replace('/', '-', $flight->takeOffTime));
    	$transaction->year = date('Y', $transaction->time);
    	$transaction->observation = $transacObservation;

    	$flight->save();
    	$transaction->save();

    	$transactions = transaction::where('idUser', $flight->idUser)->orderBy('time', 'asc')->orderBy('id', 'ASC')->get();
    	$solde = 0;
    	foreach ($transactions as $key => $value) {
    		$transactions[$key]->solde = $solde+$value->value;
    		$solde = $transactions[$key]->solde;
    		$transactions[$key]->save();
    	}

    }

    public function flightList(Request $request)
    {

    	$flights = array();
    	$currentFilter = 0;
    	if (isset($request->filterID)) {
    		$currentFilter = $request->filterID;
    	}

    	$filterType = Route::currentRouteName();
    	$filterList = array();

    	if ($filterType == 'aircraftFlights') {
    		$filterListData = aircraft::all();
    		foreach ($filterListData as $key => $value) {
    			$filterList[] = [$value->id, $value->name];
    		}
    		if (isset($request->filterID)) {
    			$flightsData = flight::where('aircraftId', $currentFilter)->get();
    		}
    	}

    	if ($filterType == 'pilotFlights') {
    		$filterListData = User::all();
    		foreach ($filterListData as $key => $value) {
    			$filterList[] = [$value->id, $value->name];
    		}
    		if (isset($request->filterID)) {
    			$flightsData = flight::where('idUser', $currentFilter)->get();
    		}
    	}

    	if (isset($flightsData)) {
    		$flight = array();
    		foreach ($flightsData as $key => $value) {
    			$aircraft = aircraft::find($value->aircraftId);
    			$user = User::find($value->idUser);
    			$flight['aircraft'] = $aircraft->name;
    			$flight['pilot'] = $user->name;
    			$flight['startDate'] = $value->takeOffTime;
    			$flight['endDate'] = $value->landingTime;
    			$flight['nbLanding'] = $value->landing;
    			$flight['flighTime'] = $this->convertMinToHM($value->totalTime);
    			if ($aircraft->type == 1) {
    				$flight['startType'] = ' A ';
    			} elseif ($aircraft->type == 2) {
    				$startType = sailplaneStartPrice::find($value->startType);
    				$flight['startType'] = $startType->name;
    			}

    			if ($aircraft->type == 2) {
    				$flight['motorTime'] = '';
    			} elseif ($aircraft->type == 1) {
    				$flight['motorTime'] = intval(($value->motorEndTime-$value->motorStartTime)*100);
    			}
    			$flight['price'] = number_format(($value->value/100), 2)." €";
    			$flights[] = $flight;
    		}
    	}
    	//var_dump($flights);
    	return view('flights', ['filters' => $filterList, 'currentFilter' => $currentFilter, 'flights' => $flights]);
    }
}
