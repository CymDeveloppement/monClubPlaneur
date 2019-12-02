@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">Liste des Pilotes</div>

                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Nom</th>
                          <th scope="col">FFVP</th>
                          <th scope="col">E-mail</th>
                          <th scope="col">Solde</th>
                          <th scope="col">Attributs</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($users as $user)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $user->name }}</td>
                          <td>{{ $user->licenceNumber }}</td>
                          <td>{{ $user->email }}</td>
                          <td style="text-align: right;">
                          @if($user->solde < 0)
                            <a href="saisie?selectUserInTransaction={{ $user->id }}" class="badge badge-danger">{{ $user->solde }}€</a>
                          @else
                          <a href="saisie?selectUserInTransaction={{ $user->id }}" class="badge badge-success">{{ $user->solde }}€</a>
                          @endif
                          </td>
                          <td>
                            @foreach($user->userAttributes as $attribute)
                              <span class="badge badge-secondary">{{ $attribute }}</span>
                            @endforeach
                          </td>
                          <td>
                            
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <hr>
                  <h3>Totaux : </h3>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">Type</th>
                          <th scope="col" style="text-align: center;">Valeur</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($totaux as $key => $total)
                        <tr>
                          <td>{{ $key }}</td>
                          <td style="text-align: center;"><span class="badge badge-info" style="width: 30%;color: white;">{{ $total }}</span></td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
