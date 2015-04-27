<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Stylish Portfolio - Start Bootstrap Theme</title>

    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/stylish-portfolio.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/map_style.css') }}"/>

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

	<script src="http://maps.googleapis.com/maps/api/js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <a id="menu-toggle" href="#" class="btn btn-dark btn-lg toggle"><i class="fa fa-bars"></i></a>
    <nav id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <a id="menu-close" href="#" class="btn btn-light btn-lg pull-right toggle"><i class="fa fa-times"></i></a>
            <li class="sidebar-brand">
                <a href="#top">LDAW</a>
            </li>
            <li>
                <a href="#top">Inicio</a>
            </li>
            <li>
                <a href="#rutas">Rutas</a>
            </li>
            <li>
                <a href="#stats">Stats</a>
            </li>
            <li>
                <a href="#scenarios">Scenarios</a>
            </li>
            <li>
                <a href="#suggestions">Suggestions</a>
            </li>
            <li>
            	<a href="#newbusiness"> New Business</a>
            </li>
          
        </ul>
    </nav>

        <!-- Header -->
    <header id="top" class="header">
        <div class="text-vertical-center">
            <h1>LDAW</h1>
            <h3>Navegación Primer Avance</h3>
            <br>
            <a href="#about" class="btn btn-dark btn-lg">Erick, Susan, Victor</a>
        </div>
    </header>

	@yield('content')
 <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1 text-center">
                    <h4><strong>LDAW</strong>
                    </h4>
                    <p>Av. Carlos Lazo <br>Ciudad de México, 0</p>
                    <!--
                    <ul class="list-unstyled">
                      <li><i class="fa fa-phone fa-fw"></i> (123) 456-7890</li>
                        <li><i class="fa fa-envelope-o fa-fw"></i>  <a href="mailto:name@example.com">A01017984@itesm.mx</a>
                        </li>
                    </ul>-->
                    <br>
                    <ul class="list-inline">
                        <li>
                            <a href="#"><i class="fa fa-facebook fa-fw fa-3x"></i></a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-twitter fa-fw fa-3x"></i></a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-dribbble fa-fw fa-3x"></i></a>
                        </li>
                    </ul>
                    <hr class="small">
                    <p class="text-muted">Susana, Erick, Victor</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery.js') }}"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>

    <!-- Script que permite la aparición del menu -->
    <script src="{{ asset('js/menu.js') }}"> </script>
    <script src="{{ asset('js/maps.js') }}"> </script>
    <script src="{{ asset('js/init_map_bk.js') }}"> </script>
</body>

</html>
