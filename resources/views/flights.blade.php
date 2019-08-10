@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">Liste des vols</div>

                <div class="card-body">

                  <select class="custom-select" id="filterFlightBoard" onchange="selectFilterFlightBoard();">
                    <option>Choisissez un Filtre</option>
                    @foreach($filters as $filter)
                    <option value="{{ $filter[0] }}" 
                      @if($currentFilter == $filter[0])
                      selected
                      @endif
                    >{{ $filter[1] }}</option>
                    @endforeach
                  </select>

                  @if($currentFilter > 0)
                  <div class="table-responsive">
                    <table class="table table-striped table-sm">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Appareil</th>
                          <th scope="col">Pilote</th>
                          <th scope="col">Décollage</th>
                          <th scope="col">Atterissage</th>
                          <th scope="col">Nombre d'atterissage</th>
                          <th scope="col">Durée</th>
                          <th scope="col">Lancement</th>
                          <th scope="col">Centièmes Moteur</th>
                          <th scope="col">Prix</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($flights as $flight)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $flight['aircraft'] }}</td>
                          <td>{{ $flight['pilot'] }}</td>
                          <td>{{ $flight['startDate'] }}</td>
                          <td>{{ $flight['endDate'] }}</td>
                          <td>{{ $flight['nbLanding'] }}</td>
                          <td>{{ $flight['flighTime'] }}</td>
                          <td>{{ $flight['startType'] }}</td>
                          <td>{{ $flight['motorTime'] }}</td>
                          <td style="text-align: right;">
                            {{ $flight['price'] }}
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
