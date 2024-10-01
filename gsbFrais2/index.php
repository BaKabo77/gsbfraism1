<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once 'model/class.pdogsb.php';
include 'views/layout/vue_entete.php';


require_once 'doc/fct.inc.php';
// connexion à la base de données
$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();
$action = 'demandeConnexion';

// Routeur--------------------------------
if (!isset($_REQUEST['uc'])|| !$estConnecte)
    $uc = 'connexion';
else
    $uc = $_REQUEST['uc'];

if (isset($_REQUEST['action'])) {
    $action=$_REQUEST['action'];
}

include "controllers/c_$uc.php";

var_dump($_REQUEST);
$controleur = new $uc();
$controleur->$action();

include 'views/layout/vue_pied.php';

