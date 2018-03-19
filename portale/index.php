<!DOCTYPE html>
<html>
<head>
    <link type="text/css" href="css/bulma.css" />
    <link type="text/css" href="css/myStyle.css" />
    <script type="text/javascript" src="js/jquery_3.3.1.js"></script>
    <script type="text/javascript" src="js/login.js"></script>
</head>
<body>
    <h1>CIAO!</h1>
    <input type="text" id="username" name="an_username" placeholder="Username"/>
    <input type="text" id="password" name="an_password" placeholder="Password" />
    <button onclick="login();">Login</button>
    <hr>
    <input type="text" id="token" name="an_token" placeholder="Token"/>
    <button onclick="controlloLogin();">Controllo login</button>
    <hr>

    <pre id="resultCall"></pre>
</body>
</html>
