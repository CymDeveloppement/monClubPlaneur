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

    private function getUserData($id)
    {
        $userDataSql = usersData::where('userID', $id)->get();
        $userData = array();
        foreach ($userDataSql as $key => $value) {
            $userData[$value->dataName] = $value->dataValue;
        }

        return $userData;
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
        $totaux = array();
        $totaux['Adhérents (nb)'] = 0;
        $totaux['Total des soldes (€)'] = 0;
        $totaux['Total des comptes positifs (€)'] = 0;
        $totaux['Total des comptes négatifs (€)'] = 0;

    	foreach ($users as $key => $value) {
            $totaux['Adhérents (nb)'] ++;
    		$allDataUsers[$value->id] = $value;
    		$allDataUsers[$value->id]->solde = number_format(($this->getSolde($value->id)/100), 2);
            $totaux['Total des soldes (€)'] += floatval($allDataUsers[$value->id]->solde);
            if ($allDataUsers[$value->id]->solde > 0) {
                $totaux['Total des comptes positifs (€)'] += floatval($allDataUsers[$value->id]->solde);
            } else {
                $totaux['Total des comptes négatifs (€)'] += floatval($allDataUsers[$value->id]->solde);
            }
    		if (isset($allAttributes[$value->id])) {
    			$allDataUsers[$value->id]->userAttributes = $allAttributes[$value->id];
                foreach ($allDataUsers[$value->id]->userAttributes as $idUser => $attributes) {
                    if (isset($totaux[$attributes . ' (nb)'])) {
                        $totaux[$attributes . ' (nb)'] ++;
                    } else {
                        $totaux[$attributes . ' (nb)'] = 1;
                    }
                }
    		} else {
    			$allDataUsers[$value->id]->userAttributes = array();
    		}
    	}


    	
        return view('usersList', ['users' => $allDataUsers, 'totaux' => $totaux]);
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
        $flight->flightTimestamp = strtotime(str_replace('/', '-', $flight->takeOffTime));
        $flight->userPayId = $request->userPay;
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
    	$transaction->idUser = $flight->userPayId;
    	$transaction->name = $transacTitle;
    	$transaction->value = 0-($flight->value);
    	$transaction->quantity = 1;
    	$transaction->valid = 1;
    	$transaction->solde = 0.0;
    	$transaction->time = $flight->flightTimestamp;
    	$transaction->year = date('Y', $transaction->time);
    	$transaction->observation = $transacObservation;
        $transaction->save();
        $flight->transactionID = $transaction->id;
        $flight->save();

    	$transactions = transaction::where('idUser', $flight->userPayId)->where('year', '>=', $transaction->year)->orderBy('time', 'asc')->orderBy('id', 'ASC')->get();
    	$solde = 0;
        $first = 1;
    	foreach ($transactions as $key => $value) {
            if ($first == 1) {
                $solde = $value->solde;
                $first = 0;
            } else {
                $transactions[$key]->solde = $solde+$value->value;
                $solde = $transactions[$key]->solde;
                $transactions[$key]->save();
            }
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
    			$flightsData = flight::where('aircraftId', $currentFilter)->orderBy('flightTimestamp')->get();
    		}
    	}

    	if ($filterType == 'pilotFlights') {
    		$filterListData = User::all();
    		foreach ($filterListData as $key => $value) {
    			$filterList[] = [$value->id, $value->name];
    		}
    		if (isset($request->filterID)) {
    			$flightsData = flight::where('idUser', $currentFilter)->orderBy('flightTimestamp')->get();
    		}
    	}
        $totalTime = 0;
        $totalPrice = 0;
        $totalDayTime = 0;
        $totalLanding = 0;
        $previousDay = 0;
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
                $totalTime += $value->totalTime;
                $totalLanding += $value->landing;
                $totalPrice += $value->value;
                if ($previousDay <> $value->takeOffTime) {
                    $totalDayTime ++;
                }
                $previousDay = $value->takeOffTime;
    		}
    	}
        $flights[] = ['aircraft' => 'TOTAL', 'pilot' => '', 'startDate' => $totalDayTime.' Jour(s)', 'endDate' => '', 'nbLanding' => $totalLanding, 'flighTime' => $this->convertMinToHM($totalTime), 'startType' => '', 'motorTime' => '', 'price' => number_format(($totalPrice/100), 2)." €"];
    	//var_dump($flights);
    	return view('flights', ['filters' => $filterList, 'currentFilter' => $currentFilter, 'flights' => $flights]);
    }

    public function updateAndControlData()
    {
        echo 'Controle de la base de données<br>';
        $fault = 0;
        $flights = flight::where('flightTimestamp', '')->orWhere('flightTimestamp', NULL)->get();
        foreach ($flights as $key => $value) {
            echo 'Mise a jour TimeStamp Flight : '.$value->id.'<br>';
            $value->flightTimestamp = strtotime(str_replace('/', '-', $value->takeOffTime));
            $value->save();
            $fault++;
        }

        $flights = flight::where('userPayId', NULL)->orWhere('userPayId', '')->orWhere('userPayId', 0)->get();
        foreach ($flights as $key => $value) {
            echo 'Mise a jour Utilisateur Facturé Flight : '.$value->id.'<br>';
            $value->userPayId = $value->idUser;
            $value->save();
            $fault++;
        }


        $flights = flight::where('transactionID', NULL)->orWhere('transactionID', '')->get();
        foreach ($flights as $key => $value) {
            echo 'Mise a jour Transaction associé Flight : '.$value->id.'<br>';
            $transaction = transaction::where('idUser', $value->userPayId)->where('time', $value->flightTimestamp)->first();
            if ($transaction->value == (0-$value->value)) {
                $value->transactionID = $transaction->id;
                $value->save();
            }
            $fault++;
        }

        $users = User::all();

        foreach ($users as $keyUsers => $user) {
            $transactions = transaction::where('idUser', $user->id)->orderBy('time', 'asc')->orderBy('id', 'ASC')->get();
            $solde = 0;
            foreach ($transactions as $key => $value) {
                $transactions[$key]->solde = $solde+$value->value;
                $solde = $transactions[$key]->solde;
                $transactions[$key]->save();
            }
            echo 'Solde compte :'.$user->name.' = '.($solde/100).'<br>';
        }

        

        echo '<br>Controle de la base de données terminée '.$fault.' défauts corrigés.';
    }
}
