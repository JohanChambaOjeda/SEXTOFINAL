<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER["REQUEST_METHOD"];
if ($method == "OPTIONS") {
    die();
}

// Requerir la librería FPDF
require_once('../reports/fpdf/fpdf.php');
require_once('../models/factura.model.php');
error_reporting(0);
$factura = new Factura;

switch ($_GET["op"]) {
    case 'todos': // Procedimiento para cargar todas las facturas
        $datos = array();
        $datos = $factura->todos();
        while ($row = mysqli_fetch_assoc($datos)) {
            $todas[] = $row;
        }
        echo json_encode($todas);
        break;

    case 'uno': // Procedimiento para obtener una factura por ID
        if (!isset($_POST["idFactura"])) {
            echo json_encode(["error" => "Factura ID not specified."]);
            exit();
        }
        $idFactura = intval($_POST["idFactura"]);
        $datos = array();
        $datos = $factura->uno($idFactura);
        $res = mysqli_fetch_assoc($datos);
        echo json_encode($res);
        break;

    case 'insertar': // Procedimiento para insertar una nueva factura
        if (!isset($_POST["Fecha"]) || !isset($_POST["Sub_total"]) || !isset($_POST["Sub_total_iva"]) || !isset($_POST["Valor_IVA"]) || !isset($_POST["Clientes_idClientes"])) {
            echo json_encode(["error" => "Missing required parameters."]);
            exit();
        }

        $Fecha = $_POST["Fecha"];
        $Sub_total = $_POST["Sub_total"];
        $Sub_total_iva = $_POST["Sub_total_iva"];
        $Valor_IVA = $_POST["Valor_IVA"];
        $Clientes_idClientes = intval($_POST["Clientes_idClientes"]);

        $datos = array();
        $datos = $factura->insertar($Fecha, $Sub_total, $Sub_total_iva, $Valor_IVA, $Clientes_idClientes);
        echo json_encode($datos);
        break;

    case 'actualizar': // Procedimiento para actualizar una factura existente
        if (!isset($_POST["idFactura"]) || !isset($_POST["Fecha"]) || !isset($_POST["Sub_total"]) || !isset($_POST["Sub_total_iva"]) || !isset($_POST["Valor_IVA"]) || !isset($_POST["Clientes_idClientes"])) {
            echo json_encode(["error" => "Missing required parameters."]);
            exit();
        }

        $idFactura = intval($_POST["idFactura"]);
        $Fecha = $_POST["Fecha"];
        $Sub_total = $_POST["Sub_total"];
        $Sub_total_iva = $_POST["Sub_total_iva"];
        $Valor_IVA = $_POST["Valor_IVA"];
        $Clientes_idClientes = intval($_POST["Clientes_idClientes"]);

        $datos = array();
        $datos = $factura->actualizar($idFactura, $Fecha, $Sub_total, $Sub_total_iva, $Valor_IVA, $Clientes_idClientes);
        echo json_encode($datos);
        break;

    case 'eliminar': // Procedimiento para eliminar una factura
        if (!isset($_POST["idFactura"])) {
            echo json_encode(["error" => "Factura ID not specified."]);
            exit();
        }
        $idFactura = intval($_POST["idFactura"]);
        $datos = array();
        $datos = $factura->eliminar($idFactura);
        echo json_encode($datos);
        break;

    case 'generar_pdf': // Procedimiento para generar el PDF de la factura
        if (!isset($_POST["idFactura"])) {
            echo json_encode(["error" => "Factura ID not specified."]);
            exit();
        }

        $idFactura = intval($_POST["idFactura"]);
        $datos = $factura->uno($idFactura);
        $facturaData = mysqli_fetch_assoc($datos);

        // Crear el PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        
        $pdf->Cell(0, 10, 'Factura ID: ' . $facturaData['idFactura'], 0, 1);
        $pdf->Cell(0, 10, 'Fecha: ' . $facturaData['Fecha'], 0, 1);
        $pdf->Cell(0, 10, 'Subtotal: ' . $facturaData['Sub_total'], 0, 1);
        $pdf->Cell(0, 10, 'Subtotal IVA: ' . $facturaData['Sub_total_iva'], 0, 1);
        $pdf->Cell(0, 10, 'Valor IVA: ' . $facturaData['Valor_IVA'], 0, 1);
        $pdf->Cell(0, 10, 'Cliente ID: ' . $facturaData['Clientes_idClientes'], 0, 1);

        // Salida del PDF
        $pdf->Output('I', 'factura_' . $idFactura . '.pdf');
        exit();

    default:
        echo json_encode(["error" => "Invalid operation."]);
        break;
}
?>
