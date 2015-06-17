<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<h3>Este es un ejemplo {{ $quote->price }}</h3>
	<img src="{{ './storage/img/'.$quote->catalog_car->image }}" alt="">
</body>
</html>