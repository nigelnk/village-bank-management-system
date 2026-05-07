<?php
require_once "../../utils/config.php";

//pdf library
require('../../fpdf186/fpdf.php');

$conn = get_db();

$action = $_GET['action'] ?? '';
$sql = "SELECT t.transaction_date, t.type, t.amount, m.firstname, m.lastname FROM transactions t 
JOIN members m ON t.member_id = m.member_id
ORDER BY t.transaction_date ASC";

$result = $conn -> query($sql);

//creating the pdf
$pdf = new FPDF();
$pdf -> AddPage();

//title
$pdf-> SetFont('Arial', 'B', 16);
$pdf-> Cell(190, 10, 'Nansadi Village Bank Cashbook', 0, 1, 'C');

//header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, 'Date', 1);
$pdf->Cell(50, 10, 'Member', 1);
$pdf->Cell(30, 10, 'Type', 1);
$pdf->Cell(30, 10, 'Amount', 1);
$pdf->Cell(40, 10, 'Balance', 1);
$pdf->Ln();

//table content
$pdf->SetFont('Arial', '', 10);
 $balance = 0; 

if ($result -> num_rows > 0 ){
 while($row = $result->fetch_assoc()){
  $name = $row['firstname'] . ' ' .$row['lastname'];
    // update running balance
        if ($row['type'] == 'share' || ($row['type'] == 'loan')) {
            $balance += $row['amount'];
        } else {
            $balance -= $row['amount'];
        }

   // row output
        $pdf->Cell(30, 10, $row['transaction_date'], 1);
        $pdf->Cell(50, 10, $name, 1);
        $pdf->Cell(30, 10, ucfirst($row['type']), 1);
        $pdf->Cell(30, 10, number_format($row['amount'], 2), 1);
        $pdf->Cell(40, 10, number_format($balance, 2), 1);
        $pdf->Ln();
}
}else{
    $pdf->Cell(190, 10, 'No transactions found', 1, 1, 'C');
}


if($action == 'generate'){
    $pdf->Output('D', 'cashbook.pdf');
}
elseif($action == 'send'){
    $folder = 'treasurer/treasurerreports/';
    if(!file_exists($folder)){
        mkdir($folder, 0777, true);
    }

    //for unique file names
    $filename = 'cashbook_' . date('Ymd_His') . '.pdf';
    $filepath = $folder . $filename;

    //save file
    $pdf->Output('F', $filepath);

    //saving to database
    $relativePath =  $filepath;
    $stmt = $conn->prepare("INSERT INTO reports(title, file_path, report_type, date_generated) VALUES (?, ?, ?, NOW())");

    $title = "Cashbook Report";
    $type = "cashbook";

    $stmt->bind_param("sss", $title, $relativePath, $type);
    $stmt->execute();

    echo "<script>alert('Cashbook sent to chairperson successfully'); window.location.href='dashboard.php';</script>";
}








