<?php
require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli('localhost', 'root', '', 'curso_angular4');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
	die();
}


$app->get("/pruebas", function () use ($app, $db) {
	echo "Hola mundo desde slim php";
	var_dump($db);
});

/**
 * Prueba para carga de webservices.
 */
$app->get("/getCountries",  function () use ($app, $db) {

	$countries = array(
		array('code' => 'col', 'description' => 'Colombia'),
		array('code' => 'cad', 'description' => 'Canada'),
		array('code' => 'bra', 'description' => 'Brazil'),
		array('code' => 'ind', 'description' => 'India'),
		array('code' => 'ita', 'description' => 'Italy'),
		array('code' => 'chi', 'description' => 'China')
	);

	$result = array(
		'status' => 'success',
		'code' => 200,
		'data' => $countries
	);

	echo json_encode($result);
});

$app->get("/getLevelEducation",  function () use ($app, $db) {

	$educaLev = array(
		array('code' => 'high', 'description' => 'Grade 12 / High School'),
		array('code' => 'pos1', 'description' => '1-Year Post-Secondary Certificate'),
		array('code' => 'und2', 'description' => '2-Year Undergraduate Diploma'),
		array('code' => 'und3', 'description' => '3-Year Undergraduate Advanced Diploma'),
		array('code' => 'bac3', 'description' => '3-Year Bachelor\'s Degree'),
		array('code' => 'bac4', 'description' => '4-Year Bachelor\'s Degree'),
		array('code' => 'poce', 'description' => 'Postgraduate Certificate/Diploma'),
		array('code' => 'mast', 'description' => 'Master\'s Degree'),
		array('code' => 'doct', 'description' => 'Doctoral Degree')
	);

	$result = array(
		'status' => 'success',
		'code' => 200,
		'data' => $educaLev
	);

	echo json_encode($result);
});

$app->get("/getProvinces/:code",  function ($code) use ($app, $db) {

	switch ($code) {

		case "col":
			$procinces = array(
				array('code' => 'cun', 'description' => 'Cundinamarca'),
				array('code' => 'san', 'description' => 'Santander'),
				array('code' => 'mag', 'description' => 'Magdalena'),
				array('code' => 'ant', 'description' => 'Antioquia')
			);
			break;

		case "can":
			$procinces = array(
				array('code' => 'on', 'description' => 'Ontario'),
				array('code' => 'que', 'description' => 'Quebec'),
				array('code' => 'alb', 'description' => 'Alberta'),
				array('code' => 'man', 'description' => 'Manitoba')
			);
			break;

		case "bra":
			$procinces = array(
				array('code' => 'rio', 'description' => 'Rio de Janeiro'),
				array('code' => 'que', 'description' => 'Quebec'),
				array('code' => 'alb', 'description' => 'Alberta'),
				array('code' => 'man', 'description' => 'Manitoba')
			);
			break;

		default;
			break;
	}

	$result = array(
		'status' => 'success',
		'code' => 200,
		'data' => $procinces
	);

	echo json_encode($result);
});

$app->get("/probando", function () use ($app) {
	echo "Otro texto cualquiera desde slim php";
});

//Listar todos los productos

$app->get('/productos', function () use ($app, $db) {

	$sql = 'select * from productos order by producto_id desc;';
	$query = $db->query($sql);

	$productos = array();
	while ($producto = $query->fetch_assoc()) {
		$productos[] = $producto;
	}

	$result = array(
		'status' => 'success',
		'code' => 200,
		'data' => $productos
	);

	echo json_encode($result);
});

//Devolver un solo producto
$app->get('/productos/:id', function ($id) use ($app, $db) {

	$sql = 'select * from productos where producto_id = ' . $id . ';';
	$query = $db->query($sql);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'Producto no disponible'
	);

	if ($query->num_rows == 1) {
		$producto = $query->fetch_assoc();

		$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $producto
		);
	}

	echo json_encode($result);
});

//Eliminar un producto
$app->get('/delete-producto/:id', function ($id) use ($app, $db) {
	$sql = 'delete from productos where producto_id = ' . $id;

	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'El producto se ha eliminado correctamente'
		);
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'El producto no se ha podido eliminar correctamente'
		);
	}

	echo json_encode($result);
});

//Actualizar un producto
$app->post('/update-producto/:id', function ($id) use ($app, $db) {
	$json = $app->request->post('json');
	$data = json_decode($json, true);
	//var_dump($data);

	$sql = "update productos set " .
		"nombre = '" . $data["nombre"] . "', " .
		"description = '" . $data["description"] . "', ";

	if (isset($data["imagen"])) {
		$sql .= "imagen = '" . $data["imagen"] . "', ";
	}

	$sql .= "precio = '" . $data["precio"] . "' where producto_id = " . $id . " ";

	//var_dump($sql);
	$query = $db->query($sql);

	if ($query) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'El producto se ha actualizado correctamente'
		);
	} else {
		$result = array(
			'status' => 'error',
			'code' => 404,
			'message' => 'El producto no se ha podido actualizar correctamente'
		);
	}

	echo json_encode($result);
});

//Subir imagen a un producto
$app->post('/upload-file', function () use ($app, $db) {

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'El archivo no ha podido subirse'
	);

	if (isset($_FILES['uploads'])) {
		//echo "LLegan los datos";
		$piramideUploader = new PiramideUploader();

		$upload = $piramideUploader->upload('image', "uploads", "uploads", array('image/jpeg', 'image/png', 'image/gif'));

		$file = $piramideUploader->getInfoFile();

		$file_name = $file['complete_name'];

		if (isset($upload) && $upload["uploaded"] == false) {
			$result = array(
				'status' => 'error',
				'code' => 404,
				'message' => 'El archivo no ha podido subirse',
				'filename' => $file_name
			);
		} else {
			$result = array(
				'status' => 'success',
				'code' => 200,
				'message' => 'El archivo se ha subido exitosamente.',
				'filename' => $file_name
			);
		}
	}

	echo json_encode($result);
});

//Guardar Productos
$app->post('/productos', function () use ($app, $db) {
	$json = $app->request->post('json');
	$data = json_decode($json, true);

	//var_dump($data["nombre"]);

	if (!isset($data['imagen']) || $data['imagen'] == "") {
		$data['imagen'] = null;
	}

	if (!isset($data['description'])) {
		$data['description'] = null;
	}

	if (!isset($data['nombre'])) {
		$data['nombre'] = null;
	}

	if (!isset($data['precio'])) {
		$data['precio'] = null;
	}

	$query = "insert into productos values (null," .
		"'{$data['nombre']}'," .
		"'{$data['description']}'," .
		"'{$data['precio']}'," .
		"'{$data['imagen']}'" .
		");";

	$insert = $db->query($query);

	$result = array(
		'status' => 'error',
		'code' => 404,
		'message' => 'El producto no ha podido ser creado'
	);

	if ($insert) {
		$result = array(
			'status' => 'success',
			'code' => 200,
			'message' => 'Producto creado correctamente'
		);
	}

	echo json_encode($result);
});

$app->run();
