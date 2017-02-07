<!DOCTYPE html>
<html lang="en">
<head>
     <meta name="robots" content="noindex,nofollow"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
    @if(isset($q))
    {{ strtoupper($q) }} - {{ config('app.name', '') }}
    @else
    {{ config('app.name', '') }}
    @endif
    </title>

    <!-- Styles -->
    <link href="/css/style.min.css" rel="stylesheet">
    <link href="/css/dataTables.bootstrap.min_old.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/dataTables.bootstrap.min.js"></script>
    <script src="/js/dataTables.colResize.js"></script>
    <script src="/js/jquery.form.js"></script>
    <script src="/js/bootstrap-select.min.js"></script>
    <script src="/js/fuelux.min.js"></script>
    <script src="/js/bootstrap-filestyle.min.js"></script>
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body style="overflow-y: scroll;">
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                            <!-- <li><a href="{{ url('/register') }}">Register</a></li> -->
                        @else
                            <!-- <li><a href="{{ url('/users') }}">Users</a></li>
                            <li><a href="{{ url('/roles') }}">Roles</a></li> -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    @if (Auth::user()->isAdmin() || Auth::user()->isSuper())
                                    <li>
                                        <a href="{{ url('/register') }}">
                                            Register User
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        <a href="{{ url('/me') }}">
                                            My Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('/logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @if (session()->has('status'))
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="alert alert-success">{{ session()->get('status') }}</div>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
