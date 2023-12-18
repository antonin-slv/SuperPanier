<?php
include_once('modele/modele.php');
class connexion extends modele {
    private $connected = false;
    private $customerID;
    private $customerName;
    private $customerEmail;
    private $customerAddress;

    public function __construct() {
        $this->customerID = $_SESSION['CustomerID'];
        $this->connected = $_SESSION['Connected'];
        if ($this->connected) {
        
        }
    }
}