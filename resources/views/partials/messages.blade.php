					@if($errors->any())
					<div class='alert alert-danger message-errors' role='alert'>
						<p>Por favor corrige los errores:</p>

						<ul>
						@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
						</ul>
					</div>
					@endif