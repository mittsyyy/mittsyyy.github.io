<?php session_start();//Habilitar variables de sesión

    include "./conexion.php";
    //VALIDAMOS
    if(isset($_POST['txtEmail']) && isset($_POST['txtPassword'])){
        $email = $_POST['txtEmail'];
        $password = sha1($_POST['txtPassword']);
        $sql ="SELECT * FROM users where  
                email='$email' 
                and 
	            password='$password'";
        //echo $sql;
        $res = $con->query($sql) or die($con->error);//ejecuta la consulta
        if(mysqli_num_rows($res) > 0 ){//cuenta el número de filas del resultado
            //leer fila=> funciona solo con 1 fila
            $fila = mysqli_fetch_row($res); 
            //echo sha1($password);
            echo "Login correcto, Bienvenido ".$fila[1];
            $_SESSION['user_data']=[
                "id"=> $fila[0],
                "name"=> $fila[1],
                "email"=> $fila[2],
                "img"=> $fila[4],
                "level"=> $fila[5],
            ];
            //redireccionar
            header("Location: ../dash.php");
        }else{
            echo "Favor de verificar sus credenciales";
            $_SESSION['Error']= "Favor de verificar sus credenciales"
            header("Location: ../login.php");
        }
    }else{
        echo "Favor de llenar todos los campos";
         $_SESSION['Error']= "Favor de llenar todos los campos"
        header("Location: ../login.php");
    }

?>