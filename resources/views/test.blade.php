<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: bold;
                height: 100vh;
                margin: 0;
            }
            .full-height {height: 100vh;}
            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }
            .position-ref {position: relative;}
            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }
            .content {text-align: center;}
            .title {font-size: 84px;}
            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }
            .m-b-md {  margin-bottom: 30px;}
        </style>
    </head>
    <body>
      <?php //session_start(); ?>
        <div class="flex-center position-ref full-height">
            @if (!isset($_SESSION['user_id']))
            <?php //var_dump($_SESSION); //var_dump(Session::has('user_id'));
            ?>
            <!-- <form action="" method="POST"> -->
              <label>Email <input type="email" name="email" /></label>
              <label>Password <input type="password" name="password" /></label>
              <button id="login" name="login">Login</button>
            <!-- </form> -->
            @else
            <button id="logout" type="submit" name="logout">Logout</button>
            <?php var_dump($_SESSION); //var_dump(Session::has('user_id'));
            ?>
            @endif
            <button id="logout" type="submit" name="logout">Logout</button>

            @if (Route::has('login'))
                <div class="top-right links">
                    <a href="{{ url('/login') }}">Login</a>
                    <a href="{{ url('/register') }}">Register</a>
                </div>
            @endif
        </div>

        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
        <script>
          $(function(){
            $("#login").on("click", function(e){
              e.preventDefault();
              var email = $("input[name='email']").val(),
                  password = $("input[name='password']").val();

              $.ajax({
                url: "http://localhost:8000/auth/login",
                method: "POST",
                data: {email: email, password: password},
                success: function(msg) {
                  //var response = JSON.parse(msg);
                  console.log(msg.token);
                  //location.reload();
                }
              });
            });

            $("#logout").on("click", function(){
              $.ajax({
                url: "http://localhost:8000/auth/logout",
                method: "GET",
                success: function(msg) {
                  console.log(msg);
                  //location.reload();
                }
              });
            });
          });
        </script>
    </body>
</html>
