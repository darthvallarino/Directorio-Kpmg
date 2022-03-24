<?php

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli( 'localhost', 'root', '', 'Directorio_Telefonico' );

// Configuración de Cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


// Listar Contactos
$app->get('/contactos', function() use($db, $app){
    $sql = 'SELECT * FROM contactos ORDER BY id DESC;';
    $query = $db->query($sql);

    $contactos = array();
    while ($contacto = $query->fetch_assoc() ){
        $contactos[] = $contacto;
    }

    $result = array(
        'status'    => 'success',
        'code'      => 200,
        'data'      => $contactos
    );

    echo json_encode($result);

});

// Devolver Contacto
$app->get('/contacto/:id', function($id) use($db, $app){

    $sql = 'SELECT * FROM contactos WHERE id = '.$id;
    $query = $db->query($sql);

    $result = array(
        'status'    => 'error',
        'code'      => 404,
        'message'   => 'Contacto NO dispónible'
    );
    
    if($query->num_rows == 1){
        $contacto = $query->fetch_assoc();
        $result = array(
            'status' => 'success',
            'code'   => 200,
            'data'   => $contacto
        );


    }

    echo json_encode($result);

});

// Eliminar Contacto
$app->get('/delete-contacto/:id', function($id) use($db, $app){

    $sql = 'DELETE FROM contactos WHERE id ='.$id;
    $query = $db->query($sql);

    if($query){
        $result = array(
            'status'    => 'success',
            'code'      => 200,
            'message'   => 'El contacto se ha eliminado correctamente'
        );
    } else {
        $result = array(
            'status'    => 'error',
            'code'      => 404,
            'message'   => 'El contacto NO se ha eliminado'
        );
    }

    echo json_encode($result);

});

// Actualizar Contacto
$app->post('/update-contacto/:id', function($id) use($db, $app){
    $json = $app->request->post('json');
    $data = json_decode($json, true);
    
    if(!isset($data['nombre'])){
        $data['nombre'] = null;
    }
    
    if(!isset($data['telefono'])){
        $data['telefono'] = null;
    }
    
    if(!isset($data['email'])){
        $data['email'] = null;
    }
    
    if(!isset($data['direccion'])){
        $data['direccion'] = null;
    }
    
    if(!isset($data['imagen'])){
        $data['imagen'] = null;
    }
    
    $sql = "UPDATE contactos SET ".
           "nombre = '{$data["nombre"]}', ".
           "telefono = '{$data["telefono"]}', ".
           "email = '{$data["email"]}', ".
           "direccion = '{$data["direccion"]}', ".
           "imagen = '{$data["imagen"]}' ".
           "WHERE id =".$id;

    $query = $db->query($sql);
    
    if($query){
        $result = array(
            'status'    => 'success',
            'code'      => 200,
            'message'   => 'El contacto se ha actualizdo correctamente'
        );
    } else {
        $result = array(
            'status'    => 'error',
            'code'      => 404,
            'message'   => 'El contacto NO se ha actualizado'
        );
    }
    
    echo json_encode($result);
    
});

// Subir foto de Contacto
$app->post('/upload-file', function() use($db, $app) {
    $result = array(
        'status'    => 'error',
        'code'      => 404,
        'message'   => 'El archivo no ha podido subirse'
    );
    
    if(isset($_FILES['uploads'])){
        $piramideUploader = new PiramideUploader();
        
        $upload = $piramideUploader->upload('image',"uploads", "uploads", array('image/jpeg', 'image/png', 'image/gif'));
        $file = $piramideUploader->getInfoFile();
        $file_name = $file['complete_name'];
        
        if(isset($upload) && $upload["uploaded"] === false){
            $result = array(
                'status'    => 'error',
                'code'      => 404,
                'message'   => 'El archivo no ha podido subirse',
                'fileName'  => $file_name
            );
        } else {
            $result = array(
                'status'    => 'success',
                'code'      => 200,
                'message'   => 'El archivo se subio correctamente',
                'fileName'  => $file_name
            );
        }
    }

    echo json_encode($result);

});

// Guardar Contactos
$app -> post("/contactos", function() use($app, $db) {
    $json = $app->request->post('json');
    $data = json_decode($json, true);

    if(!isset($data['nombre'])){
        $data['nombre'] = null;
    }

    if(!isset($data['telefono'])){
        $data['telefono'] = null;
    }

    if(!isset($data['email'])){
        $data['email'] = null;
    }

    if(!isset($data['direccion'])){
        $data['direccion'] = null;
    }

    if(!isset($data['imagen'])){
        $data['imagen'] = null;
    }
    
    $nombre     = $data['nombre'];
    $telefono   = $data['telefono'];
    $email      = $data['email'];
    $direccion  = $data['direccion'];
    $imagen     = $data['imagen'];

    $query = "INSERT INTO contactos (nombre, telefono, email, direccion, imagen)". 
    "VALUES ('$nombre', '$telefono', '$email', '$direccion', '$imagen');";

    $insert = $db->query($query);

    $result = array(
        'status'    => 'error',
        'code'      => 404,
        'message'   => 'Contacto NO se ha creado'
    );

    if($insert){
        $result = array(
            'status'    => 'success',
            'code'      => 200,
            'message'   => 'Contacto creado correctamente'
        );
    }

    echo json_encode($result);

});

$app->run();