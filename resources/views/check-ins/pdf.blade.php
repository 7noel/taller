<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <?php
        // Logo de la empresa (siempre local, nunca HTTP)
        $logoData = null;
        if ($company && $company->logo_path) {
            $candidate = storage_path('app/public/'.$company->logo_path);
            if (is_file($candidate)) {
                $logoData = 'data:image/'.pathinfo($candidate, PATHINFO_EXTENSION).';base64,'.base64_encode((string) file_get_contents($candidate));
            }
        }
        if (!$logoData && $company && $company->favicon_path) {
            $candidate = storage_path('app/public/'.$company->favicon_path);
            if (is_file($candidate)) {
                $logoData = 'data:image/'.pathinfo($candidate, PATHINFO_EXTENSION).';base64,'.base64_encode((string) file_get_contents($candidate));
            }
        }

        // Datos de empresa (fallback al establecimiento)
        $coName = $company?->razon_social ?: $company?->nombre_comercial ?: ($checkIn->establishment?->name ?? '');
        $coRuc = $company?->ruc ?? '';
        $coAddress = $company?->direccion ?: $checkIn->establishment?->address ?? '';
        $coUbigeo = trim(($company?->ubigeo?->departamento ?? '').'-'.($company?->ubigeo?->provincia ?? '').'-'.($company?->ubigeo?->distrito ?? ''));
        if (!$coUbigeo) {
            $coUbigeo = trim(($checkIn->establishment?->ubigeo?->departamento ?? '').'-'.($checkIn->establishment?->ubigeo?->provincia ?? '').'-'.($checkIn->establishment?->ubigeo?->distrito ?? ''));
        }
        $coPhone = $company?->telefono ?: $checkIn->establishment?->phone ?? '';
        $coMobile = $company?->celular ?: $checkIn->establishment?->celular ?? '';
        $coEmail = $company?->email ?: $checkIn->establishment?->email ?? '';

        // Mockup del vehículo por carrocería (prioriza JPG)
        $bodyType = strtolower(trim((string) ($checkIn->vehicle?->body_type ?? '')));
        $bodyType = in_array($bodyType, ['', '--', '-', 'null', 'ninguno']) ? '' : $bodyType;
        $mockupData = null;
        if ($bodyType) {
            foreach (['jpg', 'jpeg', 'png', 'svg'] as $ext) {
                $candidate = public_path('images/mockups/'.$bodyType.'.'.$ext);
                if (is_file($candidate)) {
                    $mime = $ext === 'jpg' ? 'image/jpeg' : ($ext === 'svg' ? 'image/svg+xml' : 'image/'.$ext);
                    $mockupData = 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($candidate));
                    break;
                }
            }
        }
        $img_size = getimagesize(public_path('images/mockups/sedan.jpg'));
        $img_width = 350; // Ancho fijo que quieres usar
        $img_height = round(($img_size[1] / $img_size[0]) * $img_width); // Calcula la altura proporcional

        // Contactos del vehículo por rol
        $relations = $checkIn->vehicle?->relationships ?? collect();
        $driver   = $relations->first(fn ($r) => $r->role === 'driver');
        $approver = $relations->first(fn ($r) => $r->role === 'approver');

        // Íconos SVG (data URI, compatibles con Dompdf)
        $svgTriangle = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><polygon points="7,1 13,13 1,13" fill="none" stroke="#008000" stroke-width="2"/></svg>');
        $svgCircle = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><circle cx="7" cy="7" r="5" fill="none" stroke="#FF0000" stroke-width="2"/></svg>');
        $svgX = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"><line x1="2" y1="2" x2="12" y2="12" stroke="#0000FF" stroke-width="2"/><line x1="12" y1="2" x2="2" y2="12" stroke="#0000FF" stroke-width="2"/></svg>');
        $svgNA = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"><circle cx="6" cy="6" r="4" fill="#9ca3af"/></svg>');

        $svgCheckGood = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"><path d="M2 6.5 L5 9.5 L10 2.5" fill="none" stroke="#008000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>');
        $svgCheckRegular = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"><polygon points="6,1 11,11 1,11" fill="none" stroke="#FFA500" stroke-width="2" stroke-linejoin="round"/></svg>');
        $svgCheckBad = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"><line x1="2" y1="2" x2="10" y2="10" stroke="#FF0000" stroke-width="2" stroke-linecap="round"/><line x1="10" y1="2" x2="2" y2="10" stroke="#FF0000" stroke-width="2" stroke-linecap="round"/></svg>');
        $svgCheckNA = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"><circle cx="6" cy="6" r="4" fill="#000000"/></svg>');

        $damageIcons = ['scratch' => $svgTriangle, 'dent' => $svgCircle, 'crack' => $svgX];

        $emissionDate = $checkIn->created_at?->format('d/m/Y');
        $emissionTime = $checkIn->created_at?->format('h:i a');
        $clientAddress = trim(($checkIn->client?->address ?? '').' '.($checkIn->client?->ubigeo ? $checkIn->client->ubigeo->departamento.'-'.$checkIn->client->ubigeo->provincia.'-'.$checkIn->client->ubigeo->distrito : ''));

        // ===== Checklist: fusionar ítems activos con resultados =====
        $resultsByItem = $checkIn->checklistResults->keyBy('checklist_item_id');
        $checklistRows = $checklistItems->map(fn ($item) => [
            'name' => $item->name,
            'result' => $resultsByItem->get($item->id),
        ])->values();

        $numCols = 3;
        $itemsPerCol = ceil($checklistRows->count() / $numCols);
        $columns = array_chunk($checklistRows->toArray(), $itemsPerCol);
     ?>
	<title>INVENTARIO: {{ $checkIn->document_sn }}</title>
    <style>
		@page { margin-top: 30px; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            white-space: nowrap;
        }
        td {
            /*padding: 4px;*/
            vertical-align: middle;
            border: 1px solid #000;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        .circle {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 3px;
            vertical-align: middle;
        }
        .circle-small {
            width: 6px;
            height: 6px;
            border-radius: 30%;
            display: inline-block;
            margin-right: 5px;
            vertical-align: middle;
        }
        .green { background-color: #008000; }
        .amber { background-color: #FFA500; }
        .red { background-color: #FF0000; }
        .black { background-color: #000000; }
        .blue { background-color: #0000FF; }
        .desc-col { width: 45%; text-align: left; border-right: none; }
        .circle-col { width: 5%; text-align: center; border-left: none; border-right: none; }
        .comment-col { width: 20%; text-align: left; border-left: none; font-style: italic; font-size:8px }
        .desc-col, .circle-col, .comment-col{
        	padding: 1px 1px;
        }
        .legend-table {
            border: 1px solid #000;
            border-collapse: separate;
            text-align: left;
        }
        .legend-table td {
            border: none;
            padding: 2px;
            white-space: nowrap;
        }
        .col_img{
        	width: 270px;
        }
        .table-datos{
        	text-transform: uppercase;
        }
        .table-datos td{
        	border: solid 1px black;
        	font-size: 9px;
        	padding: 1px 2px;
        }
        .table-datos .label2 {
        	font-weight: bold;
        	width: 25%;
        	vertical-align: top;
        }
        .table-datos th {
        	border: solid 1px black;
        	background-color: lightgray;
        	text-align: center;
        	font-weight: bold;
        	text-transform: uppercase;
        }
        .table-datos .label3{
        	font-weight: bold;
        	width: 15%;
        	vertical-align: top;
        	vertical-align: middle;
        }
		.div_img{
			margin-top: 0;
		}
		.inventory-image{
			width: 350px;
/*        	border: solid 1px black;*/
		}
		.table-ingreso{
			border: none;
			font-size: 9px;
		}
		.table-ingreso td{
			border: none;
		}
		.table-ingreso .label{
        	font-weight: bold;
        	width: 25%;
        	vertical-align: top;
		}
		.table-ingreso .col1{
			width: 30%;
		}
		.mt-5{
			margin-top: 5px;
		}
        .header-section {
        	background-color: lightgray;
        	text-align: center;
        	font-weight: bold;
        	text-transform: uppercase;
        }
.legend-icon{
    width: 14px;
    height: 14px;
    vertical-align: middle;
    margin-right: 6px;
}
.icon-dot{
    width: 10px;
    height: 10px;
    vertical-align: middle;
}

    </style>
</head>
<body>
	<script type="text/php">
	if ( isset($pdf) ) {
		$pdf->page_script('
			$font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
			$pdf->text(270, 810, "Página $PAGE_NUM de $PAGE_COUNT", $font, 8);
		');
	}
	</script>
	<div class="">
		<table>
			<tr>
				<td width="20%" align="center" style="border: none;">
	                @if ($logoData)
	                    <img src="{{ $logoData }}" alt="Logo" width="95">
	                @endif
				</td>
				<td width="38%" style="border: none; font-size: 10px;">
					<div class="company-name">{{ $coName }}</div>
					<div width="100%">{{ $coAddress }}</div>
					<div>{{ $coUbigeo }}<</div>
					<div>Central Telefónica: {{ $coPhone }}</div>
	                <div>Cel: {{ $coMobile }}</div>
	                <div>Correo: {{ $coEmail }}</div>
				</td>
				<td width="39" align="center" style="font-size: 18px; font-weight: bold;">
	                <div>INVENTARIO VEHICULAR</div>
	                <div class="hd-sub">{{ $checkIn->document_sn }}</div>
	                <div class="hd-sub">{{ $checkIn->vehicle?->plate }}</div>
	                <div class="hd-sub">{{ $checkIn->insuranceCompany?->display_name }}</div>
				</td>
			</tr>
		</table>
	</div>

<table>
	<tr>
		<td style="width:50%; border: none;">
			<table class="table-ingreso">
				<tr>
					<td class="label">Creado por:</td>
					<td class="">{{ $checkIn->creator?->name }}</td>
				</tr>
				<!-- <tr>
					<td class="label">Asesor:</td>
					<td class="">{{ isset($model->seller->company_name) ? $model->seller->company_name : '' }}</td>
				</tr> -->
				<tr>
					<td class="label">F. Emisión:</td>
					<td>{{ $emissionDate }} {{ $emissionTime }}</td>
				</tr>
			</table>
			<table class="table-datos">
				<tr>
					<th colspan="2">Datos del Cliente</th>
				</tr>
				<tr>
					<td class="label2">Propietario(a):</td>
					<td>{{ $checkIn->client?->display_name }}</td>
				</tr>
				<tr>
                    <td class="label2">{{ $checkIn->client?->document_type_label }}:</td>
                    <td>{{ $checkIn->client?->document_number }}</td>
				</tr>
                <tr>
                    <td class="label2">Dirección:</td>
                    <td>{{ $clientAddress }}</td>
                </tr>
                <tr>
                    <td class="label2">Cia. de seguro:</td>
                    <td>{{ $checkIn->insuranceCompany?->display_name }}</td>
                </tr>
                <tr>
                    <td class="label2">Conductor:</td>
                    <td>{{ $driver?->party?->display_name }} {{ $driver?->party?->mobile }}</td>
                </tr>
                <tr>
                    <td class="label2">Contacto:</td>
                    <td>{{ $approver?->party?->display_name }} {{ $approver?->party?->mobile }}</td>
                </tr>
			</table>
			<table class="table-datos">
                <tr>
                    <th colspan="4">Datos del Vehículo</th>
                </tr>
                <tr>
                    <td class="label2">Placa:</td>
                    <td colspan="3">{{ $checkIn->vehicle?->plate }}</td>
                </tr>
                <tr>
                    <td class="label2">Marca:</td>
                    <td colspan="3">{{ $checkIn->vehicle?->vehicleModel?->brand?->name }}</td>
                </tr>
				<tr>
					<td class="label2">Modelo:</td>
					<td colspan="3">{{ $checkIn->vehicle?->vehicleModel?->name }}</td>
				</tr>
				<tr>
					<td class="label2">VIN:</td>
					<td colspan="3">{{ $checkIn->vehicle?->vin }}</td>
				</tr>
				<tr>
					<td class="label2">Año:</td>
					<td colspan="3">{{ $checkIn->vehicle?->year }}</td>
				</tr>
				<tr>
					<td class="label2">Motor:</td>
					<td colspan="3">{{ $checkIn->vehicle?->engine_number }}</td>
				</tr>
				<tr>
					<td class="label2">Color:</td>
					<td colspan="3">{{ $checkIn->vehicle?->color }}</td>
				</tr>
				<tr>
					<td class="label2">Combustible:</td>
					<td colspan="3">{{ $checkIn->fuel_level_label }}</td>
				</tr>
				<tr>
					<td class="label2">Kilometraje:</td>
					<td colspan="3">{{ number_format((int) $checkIn->mileage) }} km</td>
				</tr>
				<tr>
					<td class="label2">Tj propiedad:</td>
					<td colspan="3">{{ $checkIn->property_card_label }}</td>
				</tr>
				<tr>
					<td class="label2">soat:</td>
					<td colspan="3">{{ $checkIn->soat_expiration?->format('d/m/Y') }}</td>
				</tr>
				<tr>
					<td class="label2">revision Tec:</td>
					<td colspan="3">{{ $checkIn->technical_review_expiration?->format('d/m/Y') }}</td>
				</tr>
				<tr>
					<td class="label2">llaves:</td>
					<td colspan="3">{{ $checkIn->keys_count }}</td>
				</tr>
				<tr>
					<td class="label2">c. remoto:</td>
					<td colspan="3">{{ $checkIn->has_remote_control ? 'Sí' : 'No' }}</td>
				</tr>
			</table>
			
		</td>
<td style="border: none; vertical-align: top;">
    <!-- 1. Leyenda (Separada y con margen) -->
    <table class="legend-table" style="margin: 0 auto 5px auto; border: none; padding-top: 15px; padding-left:30px;">
        <tr>
            <td style="border: none; padding: 2px;"><img src="{{ $svgTriangle }}" class="legend-icon">Rayón</td>
            <td style="border: none; padding: 2px;"><img src="{{ $svgCircle }}" class="legend-icon">Abolladura</td>
            <td style="border: none; padding: 2px;"><img src="{{ $svgX }}" class="legend-icon">Quiñe</td>
        </tr>
    </table>

    <!-- 2. Contenedor con altura FORZADA mediante padding-bottom -->
    <!-- height: 0 y padding-bottom crean el espacio exacto que Dompdf respeta -->
    <div style="position: relative; width: 350px; height: 0; padding-bottom: {{ $img_height }}px; display: block; margin: 0 auto;">
        
        <!-- Imagen del vehículo (Absoluta para que llene el contenedor) -->
        <img src="{{ $mockupData ?? './images/mockups/sedan.jpg' }}" 
             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">

        <!-- 3. Iconos de daños (Absolutos con coordenadas en píxeles) -->
        @if(!empty($checkIn->damages))
            @foreach($checkIn->damages as $damage)
                @php
                    $x_px = ($damage->pos_x / 100) * 350;
                    $y_px = ($damage->pos_y / 100) * $img_height;
                    $icon = $damageIcons[$damage->damage_type] ?? $svgX;
                @endphp
                
                <img src="{{ $icon }}" 
                     style="position: absolute; 
                            left: {{ $x_px }}px; 
                            top: {{ $y_px }}px; 
                            width: 14px; 
                            height: 14px; 
                            z-index: 2;
                            /* Centrado exacto del icono en el punto */
                            margin-left: -7px; 
                            margin-top: -7px;">
            @endforeach
        @endif
    </div>
</td>
	</tr>
</table>
<table class="table-datos mt-5">
	<tr>
		<td class="label3">SOLICITUD DEL CLIENTE:</td>
		<td>{{ $checkIn->client_request }}</td>
	</tr>
</table>
<br>
	<table class="legend-table mt-5">
	    <tr>
	        <th>Leyenda Checklist:</th>
	        <td><img src="{{ $svgCheckGood }}" class="icon-dot"> Bueno</td>
	        <td><img src="{{ $svgCheckRegular }}" class="icon-dot"> Regular</td>
	        <td><img src="{{ $svgCheckBad }}" class="icon-dot"> Malo</td>
	        <td><img src="{{ $svgCheckNA }}" class="icon-dot"> No aplica</td>
	    </tr>
	</table>
    <table class="mt-5" style="font-size: 9px;">
    	@if($checklistItems->isNotEmpty())
            @php
            @endphp
            @for ($i = 0; $i < $itemsPerCol; $i++)
                <tr>
                    @for ($j = 0; $j < $numCols; $j++)
                        @php
                            $item = $columns[$j][$i] ?? null;
                        @endphp
                        <td class="desc-col">{{ $item['name'] ?? '' }}</td>
                        <td class="circle-col">
						    @if($item)
						        @php
						            $icon = ($item['result']?->status == 'good') ? $svgCheckGood
						                  : (($item['result']?->status == 'regular') ? $svgCheckRegular
						                  : (($item['result']?->status == 'bad') ? $svgCheckBad
						                  : $svgCheckNA));
						        @endphp
						        <img src="{{ $icon }}" class="icon-dot">
						    @endif
						</td>

                        <td class="comment-col">{{ $item['comment'] ?? '' }}</td>
                    @endfor
                </tr>
            @endfor
        @endif
    </table>

<table class="table-datos mt-5">
	<tr>
		<td class="label3">OBSERVACIONES:</td>
		<td>{{ $checkIn->observations }}</td>
	</tr>
</table>
<br>
	<table class="mt-5" style="font-size: 9px;">
		<tr>
			<td class="header-section" style="width: 60%;">AUTORIZACIÓN CLIENTES</td>
			<td rowspan="2" style="vertical-align: bottom;">
				<div style="border-top: solid 1px; text-align: center;">AUTORIZADO / DNI</div>
			</td>
			<td rowspan="2" style="vertical-align: bottom;">
				<div style="border-top: solid 1px; text-align: center;">RECEPCIONISTA</div>
			</td>
		</tr>
		<tr>
			<td>Por el presente autorizo las reparaciones autorizadas conjuntamente con el material que sea necesario usar en ellas. También autorizo a ustedes y sus empleados para que operen este vehículo por la calle, carreteras y otros sitios a fin de asegurar las pruebas e inspecciones pertinentes y para asegurar el pago por concepto de reparaciones y materiales este vehículo queda sujeto a las leyes que amparan los derechos de la empresa.</td>
		</tr>
		<tr>
			<td class="header-section" style="width: 60%;">IMPORTANTE</td>
			<td rowspan="2" style="vertical-align: bottom;">
				<div style="border-top: solid 1px; text-align: center;">RECIBI CONFORME / DNI</div>
			</td>
			<td rowspan="2" style="vertical-align: bottom;">
				<div style="border-top: solid 1px; text-align: center;">ENTREGADO POR</div>
			</td>
		</tr>
		<tr>
			<td>En caso que el cliente no abonase los trabajos realizados a la empresa y estando el automóvil a su disposición, la empresa esta facultado a: <br>a) Cobrar la suma de S/ 50.00 diarios por derechos a guardería. <br>b) Cobrar el .....% de intereses por derecho a guardería. <br>c) A cualquier otra acción permitida por la ley.</td>
		</tr>
	</table>

	<footer>
	</footer>
</body>
</html>