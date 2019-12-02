<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style type="text/css">
      .datepicker.active {
        z-index: 100000;
      }
      body {
        background-image: url('img/back.jpg');
        background-repeat: no-repeat;
        background-attachment: fixed;
      }
      .stepAddFlight{
        height: 800px;
      }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->

                        @guest
                            <!--
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                          -->
                        @else
                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="home"
                                       onclick="">
                                        Mon Compte Pilote
                                    </a>
                                    <a class="dropdown-item" href="carnet">
                                        Mon carnet de vol
                                    </a>
                                    <a class="dropdown-item" href="planches">
                                        Planches de vol
                                    </a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Se déconnecter') }}
                                    </a>

                                    
                                    @can('admin')
                                    <hr>
                                    <a class="dropdown-item" href="saisie"
                                       onclick="">
                                        Saisie
                                    </a>
                                    <a class="dropdown-item" href="#"
                                       data-toggle="modal" data-target="#addUserModal">
                                        Nouvelle Utilisateur
                                    </a>
                                    <a class="dropdown-item" href="usersList">
                                        Liste des utilisateurs
                                    </a>
                                    <a class="dropdown-item" href="validTransactions">
                                        Transactions a valider
                                    </a>
                                    <a class="dropdown-item" href="route">
                                        Carnet de route Appareil
                                    </a>
                                    <a class="dropdown-item" href="vol">
                                        Carnet de vol Pilote
                                    </a>

                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#controlData" data-backdrop="static" onclick="controlBDDData();">
                                        Controle des données
                                    </a>
                                    
                                    @endcan

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @can('admin')
       <!-- Modal add user -->
        <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                    <label for="addUserMailInput">Adresse e-mail</label>
                    <input type="email" class="form-control" id="addUserMailInput" aria-describedby="emailHelp" placeholder="email">
                  </div>
                  <div class="form-group">
                    <label for="addUserNameInput">Nom Complet</label>
                    <input type="text" class="form-control" id="addUserNameInput" placeholder="Nom Prénom">
                  </div>
                  <div class="form-group">
                    <label for="addUserLicNumberInput">Numéro Licence</label>
                    <input type="text" class="form-control" id="addUserLicNumberInput" placeholder="XXXXX">
                  </div>
                  <div class="alert alert-danger" role="alert" id="addUserHelpName" style="display: none;">
                      Merci de remplir tout les champs ci-dessus!
                  </div>
                  <hr>
                  <h3>Statut du membre</h3>
                  <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="addUserStateaccomp">
                    <label class="form-check-label" for="addUserStateaccomp">Licence associative</label>
                  </div>
                  <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="addUserStateeleve">
                    <label class="form-check-label" for="addUserStateeleve">Elève</label>
                  </div>
                  <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="addUserStatepilote">
                    <label class="form-check-label" for="addUserStatepilote">Pilote</label>
                  </div>
                  <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="addUserStateinstructeurplaneur">
                    <label class="form-check-label" for="addUserStateinstructeurplaneur">Instructeur (Planeur)</label>
                  </div>
                  <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="addUserStateinstructeurULM">
                    <label class="form-check-label" for="addUserStateinstructeurULM">Instructeur (ULM)</label>
                  </div>
                  <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="addUserStateremorqueur">
                    <label class="form-check-label" for="addUserStateremorqueur">Remorqueur</label>
                  </div>
                  <div class="alert alert-danger" role="alert" id="addUserHelpState" style="display: none;">
                      Merci de selectionner au moins une case!
                  </div>
                  <div class="alert alert-danger" role="alert" id="addUserHelpServerError" style="display: none;"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveNewUser();">Enregistrer</button>
              </div>
            </div>
          </div>
        </div>

      <!-- Modal Control Data -->
      <div class="modal fade" id="controlData" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" >Controle & mise à jour de la base de données </h5>
            </div>
            <div class="modal-body">
              <div id="controlDataResult">
                <div class="text-center">
                  <h5>Controle de la base de données en cours ...</h5>
                  <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeControlDataModal" onclick="window.location = window.location.href.split('#')[0];" disabled>Fermer</button>
            </div>
          </div>
        </div>
      </div>
    @endcan

    <!-- Modal add payments-->
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="payModalLabel">Approvisionner mon compte</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="form-group">
                <label for="payModalAmount">Montant</label>
                <input type="number" min="10" max="3000" class="form-control" id="payModalAmount" aria-describedby="payModalAmountHelp" placeholder="20,00">
                <small id="payModalAmountHelp" class="form-text text-muted">Montant minimum 10€.</small>
              </div>
                <div class="alert alert-danger" id="payModalErrorAmount" role="alert" style="display: none;">
                    Veuillez indiquer un montant correct (ex:150.00).
                </div>
              <div class="form-group">
                <label for="payModalType">Type de paiement</label>
                <select class="form-control" id="payModalType" aria-describedby="payModalTypeHelp">
                    <option value="CB">Carte Bancaire</option>
                    <option value="CH">Chèque</option>
                    <option value="VI">Virement</option>
                </select>
                <small id="payModalTypeHelp" class="form-text text-muted">Pour les chèques et les virements, la transaction sera validé par le trésorerier.<br>Les paiement Carte Bancaire sont validés immédiatement.</small>
              </div>
              <div class="form-group">
                <label for="payModalText">Observation</label>
                <input type="text" class="form-control" id="payModalText">
              </div>
              <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="payModalSendMail" checked>
                <label class="form-check-label" for="payModalSendMail">Recevoir un reçu par e-mail</label>
              </div>
              <button type="submit" class="btn btn-primary float-right" onclick="pay();">Payer</button>
          </div>
        </div>
      </div>
    </div>

    <script src="js/function.js"></script>
    

</body>
</html>
