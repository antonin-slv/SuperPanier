




<?php 


/* Il y a un tuto dans le dossier fpdf
 *
 * Penser à n'envoyer que et rien que le pdf si on veut bien faire les choses 
 * ON PEUT UTILISER 
 * <a href="https://www.google.com" target="_blank">Google</a>
*/
ob_end_clean(); 
require('../fpdf/fpdf.php'); 
  
// Instantiate and use the FPDF class  
$pdf = new FPDF(); 
  
//Add a new page 
$pdf->AddPage(); 
  
// Set the font for the text 
$pdf->SetFont('Arial', 'B', 18); 
  
// Prints a cell with given text  
$pdf->Cell(60,20,'Je sais faire des PDF!'); 
  
// return the generated output 
$pdf->Output(); 
  
?>