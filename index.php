<?php
session_start();

/**
 * Crea y devuelve una conexión a la base de datos MySQL.
 *
 * @return mysqli Conexión activa a la base de datos
 */
function obtenerConexion(): mysqli {
    $host = "localhost";
    $user = "root";
    $password = "abc123.";
    $database = "login_sistema";

    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    return $conn;
}

/**
 * Intenta autenticar un usuario con sus credenciales.
 *
 * @param mysqli $conn Conexión a la base de datos
 * @param string $usuario Nombre de usuario
 * @param string $password Contraseña
 * @return bool True si el login es correcto, false si falla
 */
function login(mysqli $conn, string $usuario, string $password): bool {
    $sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $_SESSION['usuario'] = $usuario;
        return true;
    }

    return false;
}

/**
 * Obtiene los datos de un usuario por su nombre.
 *
 * @param mysqli $conn Conexión a la base de datos
 * @param string $usuario Nombre de usuario
 * @return mysqli_result|false Resultado de la consulta
 */
function obtenerUsuario(mysqli $conn, string $usuario) {
    return $conn->query("SELECT * FROM usuarios WHERE usuario='$usuario'");
}

/**
 * Cierra la sesión del usuario y redirige al inicio.
 *
 * @return void
 */
function logout(): void {
    session_destroy();
    header("Location: index.php");
    exit;
}

$conn = obtenerConexion();

/* LOGIN */
if (isset($_POST['usuario']) && isset($_POST['password'])) {
    if (!login($conn, $_POST['usuario'], $_POST['password'])) {
        $error = "Usuario o contraseña incorrectos";
    }
}

/* LOGOUT */
if (isset($_GET['logout'])) {
    logout();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Sistema Login</title>

<style>
body{
    font-family: Arial;
    background: linear-gradient(120deg,#4facfe,#00f2fe);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login{
    background:white;
    padding:40px;
    border-radius:10px;
    width:300px;
    box-shadow:0px 10px 20px rgba(0,0,0,0.2);
}

input{
    width:100%;
    padding:10px;
    margin:10px 0;
}

button{
    width:100%;
    padding:10px;
    background:#4facfe;
    border:none;
    color:white;
    font-size:16px;
}

.panel{
    text-align:center;
}

a{
    text-decoration:none;
    color:red;
}
</style>

</head>

<body>

<?php if(!isset($_SESSION['usuario'])){ ?>

<div class="login">

<h2>Login</h2>

<?php if(isset($error)){ echo $error; } ?>

<form method="POST">
<input type="text" name="usuario" placeholder="Usuario" required>
<input type="password" name="password" placeholder="Contraseña" required>
<button type="submit">Entrar</button>
</form>

</div>

<?php } else { ?>

<div class="login panel">

<h1>Bienvenido <?php echo $_SESSION['usuario']; ?></h1>

<p>
<?php
$resultadp = obtenerUsuario($conn, $_SESSION['usuario']);

if ($resultadp) {
    while ($fila = $resultadp->fetch_assoc()) {
        echo "Nombre: " . $fila['usuario'] . " - Contraseña: " . $fila['password'] . "<br>";
    }
}
?>
</p>

<p>Has iniciado sesión correctamente.</p>

<a href="?logout=1">Cerrar sesión</a>

</div>

<?php } ?>

</body>
</html>
