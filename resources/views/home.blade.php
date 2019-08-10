@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">Mon Compte Pilote</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">Date</th>
                          <th scope="col">Description</th>
                          <th scope="col">Montant</th>
                          <th scope="col">Solde</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                          $temporySolde = 0;
                        @endphp
                        @foreach ($transactions as $transaction)
                            <tr 
                            @if($transaction['valid'] == 0)
                            class="table-warning"
                            @endif
                            >
                              <th scope="row">{{ $transaction['time'] }}</th>
                              <td style="font-weight: bold;">{{ $transaction['name'] }}
                                @if($transaction['valid'] == 0)
                                <br><span class="badge badge-danger">En attente de validation.</span>
                                  @php
                                    $temporySolde = 1;
                                  @endphp
                                @endif
                                @if($transaction['observation'] != '')
                                <br><small style="font-size: 70%;font-weight: normal;"><i>{{ $transaction['observation'] }}</i></small>
                                @endif
                              </td>
                              <td>{{ $transaction['value'] }}€</td>
                              <td>{{ $transaction['solde'] }}€</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td></td>
                            
                            <th>Solde au 
                            @php
                                echo date('d/m/Y');
                            @endphp
                            @if($temporySolde == 1)
                              <br><span class="badge badge-danger">En attente de validation.</span>
                            @endif
                            </th>
                            <td></td>
                            <th 
                            @if($solde<0)
                             class="table-danger"
                            @elseif($solde>0 && $temporySolde == 1)
                              class="table-warning"
                            @else
                             class="table-success"
                            @endif>
                            {{ $solde }}€
                            </th>
                        </tr>
                      </tbody>
                    </table>
                    <br>
                    @if($solde < 0)
                      <div class="alert alert-warning" role="alert">
                        Le solde de votre compte est négatif. merci d'approvisionner votre compte.
                      </div>         
                    @endif
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#payModal">Approvisionner mon compte</button>
                </div>
            </div>



            <div class="card" style="margin-top: 30px;">
                <div class="card-header">Journée de Vol</div>

                <div class="card-body">
                  <h3>S'inscrire a une journée de vol:</h3>
                  <div class="form-row">
                    <div class="form-group col-md-4">
                      <label>Date</label>
                      <input type="text" id="datepicker-flightDay" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                      <label>Statut</label>
                      <select class="form-control" id="addFlightDayAttributes">
                        @foreach ($userAttributes as $userAttribute)
                            <option value="{{ $userAttribute->attributeName }}">{{ $userAttribute->attributeName }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-md-4">
                      <label>Inscription minimum 24H avant</label>
                      <button class="btn btn-success btn-block" onclick="saveFlightDay();">s'enregistrer</button>
                    </div>
                  </div>
                  <input style="margin-bottom: 20px;" type="text"  class="form-control" id="addFlightDayObservation" placeholder="observation">
                  <div class="alert alert-success" role="alert" id="flightDayRegisterOK" style="display: none;"></div>
                  <div class="alert alert-danger" role="alert" id="flightDayRegisterERROR" style="display: none;"></div>
                  <h3 style="margin-top: 30px;">Les journées de vol à venir:</h3>
                  <div id="flightDayBoardContent"></div>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection
