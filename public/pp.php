<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<style>

.div-left{
	vertical-align: middle;
	line-height: normal;
	display: inline-block;
	background: yellow;
}
.div-right{
	vertical-align: middle;
	line-height: normal;
	background: green;
	display: inline-block;
	width:349px;
}
.group{
	line-height: 100%;
}

	</style>
</head>
<body>
	<div class="group">
		<div class="div-left">ESTE ES UN DIV DE UNA LINEA</div>
		<div class="div-right">
			<div>ESTE ES UN DIV DE VARIAS LINEAS</div>
			<div>ESTE ES UN DIV DE VARIAS LINEAS</div>
			<div>ESTE ES UN DIV DE VARIAS LINEAS</div>
			<div>ESTE ES UN DIV DE VARIAS LINEAS</div>
			<div>ESTE ES UN DIV DE VARIAS LINEAS</div>
			<div>ESTE ES UN DIV DE VARIAS LINEAS</div>
		</div>
	</div>
</body>
</html>