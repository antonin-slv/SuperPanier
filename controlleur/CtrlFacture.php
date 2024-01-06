<?php
require_once('fpdf/fpdf.php');

class CtrlFacture extends FPDF
{
    private $product_info;
    private $total_price;
    private $user_info;

    public function __construct ($panier_id)
    {
        /* on cré un panier pour avoir toute les données neccesairent, on l'abandonne apres ...*/
        $panier = new panier(intval($panier_id));

        // on donne les noms corrects à chaque produit
        foreach ($panier->getProducts() as $key => $value) {
            //on récupère les infos du produit
            $this->product_info[$key] = Array('quantity'=> $value) + $panier->getProductInfo($key);
        }
        $panier->updatePrice();
        $this->total_price = $panier->total_price;
        $this->user_info = $panier->getUserInfoFromIdPanier($panier_id);

    }

    public function monFooter($pdf)
    {
        $pdf->SetY(-15);$pdf->SetX(89.8);$pdf->SetFont('Arial', 'I', 6);$pdf->Cell(1,1,'-');
        $pdf->SetY(-15);$pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(1,3,'ISIWEB4SHOP by Antonin Sylvestre & Thomas Blanche',0,0,'L');
        $pdf->Cell(0,3,'Page '.$pdf->PageNo().'/{nb}',0,1,'R');
        $pdf->Cell(0,0,'',1,1);
        return $pdf;
    }

    public function afficherFacture()
    {
            // INIT OF PDF
        
        $pdf = new FPDF();
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetCreator('ISIWEB4SHOP by Antonin Sylvestre & Thomas Blanché');
        $pdf->SetAuthor('ISIWEB4SHOP by Antonin Sylvestre & Thomas Blanché');
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetTitle('Facture');
        $pdf->SetSubject('Facture');
        $pdf->SetKeywords('ISIWEB4SHOP, Facture');


        $pdf->AliasNbPages();
        $pdf->AddPage();
        

            // TITLE

        $pdf->SetFont('Arial','B',40);$pdf->SetTextColor(57, 89, 162);
        $pdf->Cell(40,10,'Facture');
        $pdf->Cell(40,10,'');


            // DATE

        $pdf->SetFont('Arial','',12);$pdf->SetTextColor(0,0,0);
        $pdf->Cell(0,10,'Date : '.date("d/m/Y"),0,1,'R'); // Right aligned
        $pdf->Ln(20); 


            // INFO seller and buyer 

        $pdf->Cell(40,5,'Vendeur :',0,0);
        $pdf->Cell(0,5,'ISIWEB4SHOP',0,2);
        $pdf->Cell(0,2,'',0,2);
        $pdf->Cell(0,5,'15 Bd Andre Latarjet,',0,2);
        $pdf->Cell(0,5,'69100 Villeurbanne France',0,1);
        $pdf->Ln(8);

        $pdf->Cell(40,5,'Client :',0,0,'B');
        $pdf->Cell(0,5,$this->user_info['forname']." ".$this->user_info['surname'],0,2);
        $pdf->Cell(0,2,'',0,2);
        $pdf->Cell(0,5,$this->user_info['numero']." ".$this->user_info['rue'],0,2);
        $pdf->Cell(0,5,$this->user_info['code_postal']." ".$this->user_info['ville']." ".$this->user_info['Pays'],0,2);
        $pdf->Cell(0,5,$this->user_info['info_supp'],0,1);
        $pdf->Ln(12);


            // INFO products

        // on definit les largeurs des colonnes
        $margeD = 5; $name = 65; $quantity = 30; $price = 35; $total = 40;

        // on affiche les titres des colonnes

        $pdf->SetFont('Arial','B',12);$pdf->SetFillColor(57, 89, 162);$pdf->SetTextColor(255,255,255);
        $pdf->Cell($margeD,10,'',0,0,'L',true);
        $pdf->Cell($name,10,'Produit',0,0,'L',true);
        $pdf->Cell($quantity,10,'Quantite',0,0,'L',true);
        $pdf->Cell($price,10,'Prix (en euro)',0,0,'L',true);
        $pdf->Cell($total,10,'Total',0,1,'L',true); 

        // on affiche les produits
        $pdf->SetFont('Arial','',12);$pdf->SetFillColor(224,235,255);$pdf->SetTextColor(0,0,0);$fill = false;
        foreach ($this->product_info as $key => $value) {
            $pdf->Cell($margeD,10,'',0,0,'L',$fill);
            $pdf->Cell($name,10,$value['name'],0,0,'L',$fill);
            $pdf->Cell($quantity,10,$value['quantity'],0,0,'L',$fill);
            $pdf->Cell($price,10,$value['price'],0,0,'L',$fill);
            $pdf->Cell($total,10,$value['price']*$value['quantity'],0,1,'L',$fill);
            $fill = !$fill;
            if($pdf->GetY() > 220){
                $pdf = $this->monFooter($pdf);
                $pdf->AddPage();
                $pdf->SetFont('Arial','',12);$pdf->SetFillColor(224,235,255);$pdf->SetTextColor(0,0,0);
            }
        }
        $pdf->Ln(12);


            // INFO total price

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell($margeD + $name + $quantity + $price + $total,10,'Total :  '.$this->total_price.' euros',0,1,'R'); // Right aligned


            // FOOTER

        $pdf = $this->monFooter($pdf);


            // OUTPUT

        $pdf->Close();
        $pdf->Output();

    }
}