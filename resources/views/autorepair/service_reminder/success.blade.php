@extends('app')
@section('content')
<div class="container">
   <div class="row">
       <div class="col col-md-6 col-md-offset-3"   >
           <div class="panel panel-default">
             <div class="panel-heading"><h3 class="panel-title">Atencion!!!</h3></div>
             <div class="panel-body">
               <h4>Tu mensaje ha sido enviado.</h4>
             </div>
             <div class="panel-footer">
                 <a href="{{ $last_page }}" class="btn btn-primary btn-xs">Volver</a>
               </div>
           </div>
        </div>
   </div>
</div>
@endsection