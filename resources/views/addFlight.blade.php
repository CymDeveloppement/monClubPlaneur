@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">Ajouter un vol</div>

                <div class="card-body">
                  <div clas="stepAddFlight" id="step1" style="min-height: 400px;">
                    <div class="container">
                      <div class="row">
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div><div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                        <div class="col"><button type="button" class="btn btn-primary">Primary</button></div>
                      </div>
                      <div class="row">
                        <div class="col-8">col-8</div>
                        <div class="col-4">col-4</div>
                      </div>
                    </div>
                  </div>
                  <div clas="stepAddFlight" id="step2" style="min-height: 400px;display: none;"></div>
                  <div clas="stepAddFlight" id="step3" style="min-height: 400px;display: none;"></div>
                  <div clas="stepAddFlight" id="step4" style="min-height: 400px;display: none;"></div>
                  <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                      <li class="page-item active"><a class="page-link" href="#">Appareil</a></li>
                      <li class="page-item"><a class="page-link" href="#">Heure & temps de vol</a></li>
                      <li class="page-item"><a class="page-link" href="#">Moyen de lancement / temps Moteur</a></li>
                      <li class="page-item"><a class="page-link" href="#">Récapitulatif</a></li>
                    </ul>
                  </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
