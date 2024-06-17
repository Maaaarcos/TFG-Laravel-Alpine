<!DOCTYPE html>
<html>
<head>
	<title>EasyTPV</title>
   <!--Made with love by Mutiullah Samim -->
   
	<!--Bootsrap 4 CDN-->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    
    <!--Fontawesome CDN-->
	<!-- Font Awesome Free 6.0.0 by @fontawesome - https://fontawesome.com
	License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) -->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

	<!--Custom styles-->
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/styles.css')}}">
</head>
<body>
<div class="container">
	<div class="d-flex justify-content-center h-100">
		<div class="card">
			<div class="card-header text-center">
				<h3>Autentificación</h3>
			</div>
			<div class="card-body">
				<form action="{{route('login')}}" method="post">
                    @csrf
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-user"></i></span>
						</div>
						<input type="text" class="form-control" placeholder="correo" name="email">
						
					</div>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-key"></i></span>
						</div>
						<input type="password" class="form-control" placeholder="contraseña" name="password">
					</div>
					<div class="row align-items-center remember">
						<input type="checkbox">Recuerdame
					</div>
					<div class="form-group">
						<input type="submit" value="Acceder" class="btn float-right login_btn">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<footer class="bg-dark text-white text-center py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p>EasyTPV. 2024</p>
				<p>Iconos por <a href="https://fontawesome.com/license/free" target="_blank">Font Awesome</a></p>
				<p>Bootstrap está licenciado bajo <a href="https://opensource.org/licenses/MIT" target="_blank">Licencia MIT</a></p>
            </div>
            <div class="col-md-6">
                <p>Esta web esta bajo la licencia <a href="https://creativecommons.org/licenses/by-sa/4.0/" target="_blank">CC BY-SA 4.0</a></p>
                <p>Imagen utilizada <a href="https://www.pexels.com/es-es/foto/botella-surtida-dentro-de-la-barra-941864/" target="_blank">Chan Walrus en Pexels</a></p>
                <p>Bajo licencia de <a href="https://www.pexels.com/es-ES/license/" target="_blank">Pexels</a></p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>