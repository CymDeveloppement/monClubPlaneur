@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Saisie Transaction
                  @if($selectedUser > 0)
                  <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#adminAddFlight">Enregistrer un vol</button>
                  @endif
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="GET">
                      <div class="input-group">
                        <select  onchange="$('#userBoard').fadeOut();" class="custom-select" id="selectUserInTransaction" name="selectUserInTransaction" aria-label="Liste des utilisateurs">
                          @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                              @if($user->id == $selectedUser)
                              selected
                              @php
                                $currentUserName = $user->name;
                              @endphp
                              @endif
                              >{{ $user->name }}</option>
                          @endforeach
                        </select>
                        <div class="input-group-append">
                          <button class="btn btn-outline-secondary" type="submit">Afficher</button>
                        </div>
                      </div>
                    </form>


                    <div id="userBoard">
                      
                    

                      <hr>
                      @if (count($transactions) > 0)
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
                          @foreach ($transactions as $transaction)
                              <tr>
                                <th scope="row">
                                  <div id="currentTrDateBlock-{{ $transaction['id'] }}">
                                  <button class="btn btn-link" style="font-weight: bold; text-decoration: none; color: black;" onclick="displayNewTrDate({{ $transaction['id'] }})">{{ $transaction['time'] }}</button>
                                  </div>
                                  <div id="newTrDateBlock-{{ $transaction['id'] }}" style="display: none;">
                                    <div class="input-group mb-3">
                                      <input type="text" value="{{ $transaction['time'] }}" class="form-control form-control-sm newTrDateBlock-datePicker" id="newTrDateInput-{{ $transaction['id'] }}">
                                      <div class="input-group-append">
                                        <button class="btn btn-success btn-sm" type="button" onclick="validNewTrDate({{ $transaction['id'] }});">
                                          <i data-feather="check" style="width: 16px;height: 16px;"></i>
                                        </button>
                                      </div>
                                    </div>
                                  </div>
                                </th>
                                <td>{{ $transaction['name'] }}
                                @if($transaction['observation'] != '')
                                  <br><small style="font-size: 70%;"><i>{{ $transaction['observation'] }}</i></small>
                                @endif
                                </td>
                                <td>{{ $transaction['value'] }}€</td>
                                <td
                                @if($transaction['solde']<0)
                                  class="table-danger"
                                @endif
                                 >{{ $transaction['solde'] }}€</td>
                              </tr>
                          @endforeach
                        </tbody>
                      </table>
                      <div class="row">
                        <div class="col-md-6" style="text-align: center;">
                          <a href="updateSolde?selectUserInTransaction={{ $selectedUser }}" class="btn btn-warning btn-block">Recalculer le solde</a>
                        </div>
                        <div class="col-md-6" style="text-align: center;">
                          <a href="saisie/deleteLast/?deleteLastUserTransaction={{ $selectedUser }}" class="btn btn-danger btn-block">Supprimer la derniére Transaction</a>
                        </div>
                      </div>
                      @endif


                      @if($selectedUser > 0)
                        <hr>
                        <h3>Saisie Rapide</h3>
                        <br>
                        <form method="POST">
                          {{ csrf_field() }}
                          <div class="input-group">
                            <select class="custom-select" id="selectTransactionTypeEnc" name="selectTransactionTypeEnc" aria-label="Type de transaction">
                              <option value="0">Encaissement (+)</option>
                              <option value="1">Vente (-)</option>
                            </select>
                            <select onchange="changeTransactionType();" class="custom-select" id="selectTransactionType" name="selectTransactionType" aria-label="Type de transaction">
                              @foreach($transactionType as $type)
                              <option value="{{ $type->name }}" data-type="{{ $type->defaultType }}" data-amount="{{ $type->defaultAmount }}">{{ $type->name }}</option>
                              @endforeach
                            </select>
                            <input type="number" aria-label="Valeur" name="valueTransaction" id="valueTransaction" step="0.01" placeholder="Montant" class="form-control">
                            <div class="input-group-append">
                              <button class="btn btn-outline-secondary" type="submit">Ajouter</button>
                            </div>
                          </div>
                        </form>
                        <hr>
                        <h3>Saisie Compléte</h3>
                        <form method="POST">
                          {{ csrf_field() }}
                          <div class="input-group">
                            <input type="text" aria-label="Valeur" name="nameFreeTransaction" id="nameFreeTransaction" placeholder="Intitulé" class="form-control">
                            <input type="number" aria-label="Valeur" name="valueFreeTransaction" id="valueTransaction" step="0.01" placeholder="Montant" class="form-control">
                            <div class="input-group-append">
                              <button class="btn btn-outline-secondary" type="submit">Ajouter</button>
                            </div>
                          </div>
                        </form>
                      @endif

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@isset($selectedUser)
@if($selectedUser > 0)
        <!-- Modal adminAddFlight -->
        <div class="modal fade" id="adminAddFlight" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ajouter un vol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <input type="hidden" id="userAdminAddFlight" value="{{ $selectedUser }}">
                <div class="form-group">
                  <label for="userPayAdminAddFlight">Utilisateur a facturer</label>
                  <select class="custom-select" id="userPayAdminAddFlight">
                      @foreach($users as $user)
                    <option value="{{ $user->id }}" 
                        @if($selectedUser == $user->id)
                        selected
                        @endif
                      >{{ $user->name }}</option>
                      @endforeach
                  </select>
                </div>
                <select class="custom-select" id="adminAddFlightAircraft" onchange="adminAddFlightSelectType();">
                  <option selected value="0">Séléctionnez l'appareil</option>
                  @foreach($aircrafts as $aircraft)
                  <option value="{{ $aircraft->id }}" data-aircrafttype="{{ $aircraft->type }}" data-motorprice="{{ $aircraft->motorPrice }}" data-price="{{ $aircraft->basePrice }}">{{ $aircraft->name }} ({{ $aircraft->register }})</option>
                  @endforeach
                </select>
                <br><br>
                <div class="form-group">
                  <label for="adminAddFlightsTakeOff">Heure de Décollage</label>
                  <input type="text" onchange="adminAddFlightTimeCalc();" class="form-control addFlightDatePicker" id="adminAddFlightsTakeOff" placeholder="">
                </div>
                <div class="form-group">
                  <label for="adminAddFlightsLanding">Heure d'atterrissage</label>
                  <input type="text" onchange="adminAddFlightTimeCalc();" class="form-control addFlightDatePicker" id="adminAddFlightsLanding" placeholder="">
                </div>
                <div class="form-group">
                  <label for="adminAddFlightsTime">Temps de vol.</label>
                  <input type="number" onkeyup="priceAdminFlight();" onchange="priceAdminFlight();" min="3" step="1" value="0" class="form-control" id="adminAddFlightsTime2" aria-describedby="adminAddFlightsTimeHelp">
                  <small id="adminAddFlightsTimeHelp" class="form-text text-muted">Temps de vol en minutes</small>
                </div>
                <div class="form-group">
                  <label for="adminAddFlightsTakeOff2">Nombre de décollage</label>
                  <input type="number" onkeyup="priceAdminFlight();" onchange="priceAdminFlight();" step="1" min="1" value="1" class="form-control" id="adminAddFlightsTakeOff2" placeholder="">
                </div>
                <hr>
                <div id="flightSelectType2" class="aircraftTypeBlock" style="display: none;">
                  <h3>Planeur : </h3>
                  <div class="form-group">
                    <select class="custom-select" onchange="priceAdminFlight();" id="adminAddFlightsTakeOffType2">
                      @foreach($sailplaneStartPrices as $sailplaneStartPrice)
                      <option value="{{ $sailplaneStartPrice->id }}" data-price="{{ $sailplaneStartPrice->basePrice }}">{{ $sailplaneStartPrice->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div id="flightSelectType1" class="aircraftTypeBlock" style="display: none;">
                  <h3>Avion / TMG / ULM : </h3>
                  <div class="form-group">
                    <label for="adminAddFlightsMotorStart">Compteur moteur au départ</label>
                    <input type="number" onkeyup="priceAdminFlight();" onchange="priceAdminFlight();" step="0.01" min="1" class="form-control" id="adminAddFlightsMotorStart" placeholder="">
                  </div>
                  <div class="form-group">
                    <label for="adminAddFlightsMotorEnd">Compteur moteur a l'arrivé</label>
                    <input type="number" onkeyup="priceAdminFlight();" onchange="priceAdminFlight();" step="0.01" min="1" class="form-control" id="adminAddFlightsMotorEnd" placeholder="">
                  </div>

                </div>
                <div id="adminAddFlightTotalPrice" style="text-align: center;font-weight: bold;font-size: 2em;font-style: italic;"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="resetAdminAddFlightForm();">Annuler</button>
                <button type="button" class="btn btn-success" onclick="priceAdminFlight();">Calculer</button>
                <button type="button" class="btn btn-primary validNewAdminFlight" disabled onclick="validNewAdminFlight(false);">Enregistrer</button>
                <button type="button" class="btn btn-primary validNewAdminFlight" disabled onclick="validNewAdminFlight(true);">Enregistrer & fermer</button>
              </div>
            </div>
          </div>
        </div>
@endif
@endisset


<script type="text/javascript">
  function changeTransactionType()
  {
    var type = $('#selectTransactionType option:selected').attr('data-type');
    var amount = $('#selectTransactionType option:selected').attr('data-amount');
    $("#valueTransaction").val((amount/100));
    $("#selectTransactionTypeEnc").val(type);
  }
</script>
@endsection

