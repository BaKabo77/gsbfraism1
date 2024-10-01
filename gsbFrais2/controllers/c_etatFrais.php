<?php
/** @var PdoGsb $pdo */
include 'views/v_sommaire.php';
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];
switch($action){
	case 'selectionnerMois':{
		
	}
	case 'voirEtatFrais':{
		$leMois = $_REQUEST['lstMois'];
		$lesMois=$pdo->getLesMoisDisponibles($idVisiteur);
		$moisASelectionner = $leMois;
		include("views/v_listeMois.php");
		$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur,$leMois);
		$lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur,$leMois);
		$numAnnee =substr( $leMois,0,4);
		$numMois =substr( $leMois,4,2);
		$libEtat = $lesInfosFicheFrais['libEtat'];
		$montantValide = $lesInfosFicheFrais['montantValide'];
		$nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
		$dateModif =  $lesInfosFicheFrais['dateModif'];
		
		//Gestion des dates
		@list($annee,$mois,$jour) = explode('-',$dateModif);
		$dateModif = "$jour"."/".$mois."/".$annee;

		//$dateModif =  dateAnglaisVersFrancais($dateModif);
		include("views/v_etatFrais.php");
	}

}

class etatFrais{

	private $pdo;

	public function __construct()
	{
		$this->pdo = PdoGsb::getPdoGsb();
	}

	public function selectionnerMois(){

		$lesMois=$this->pdo->getLesMoisDisponibles($idVisiteur);
		// Afin de sélectionner par défaut le dernier mois dans la zone de liste,
		// on demande toutes les clés, et on prend la première,
		// les mois étant triés décroissants
		$lesCles = array_keys( $lesMois );
		$moisASelectionner = $lesCles[0];
		include("views/v_listeMois.php");
		break;

	}

	public function voirEtatFrais(){

		$id = $_REQUEST['numero'];
        $mois = $_REQUEST['mois'];
        $annee = $_REQUEST['annee'];
        $repas = $_REQUEST['repas'];
        $nuit = $_REQUEST['nuit'];
        $etape = $_REQUEST['etape'];
        $km = $_REQUEST['km'];
        $date = strval($annee.$mois);

        $this->pdo->creationFicheFrais($id,$date,$repas,$km,$etape,$nuit);
        
       // $this->pdo->creationFraisHorsForfait($id,$date);
        $resultat = $this->pdo->getLesInfosFicheFrais($id,$date);
        $libEtat = $resultat['libEtat'];
        $dateModif = $resultat['dateModif'];
        var_dump($_REQUEST);
        //$nbJustificatifs = $_REQUEST[2];
        //$montantValide = $_REQUEST["montantValide"];

       $lesFraisHorsForfait = $this->pdo->getLesFraisHorsForfait($id,$date);

        $visiteur['id'] = $id;

        include('views/v_sommaire.php');
        include('views/v_accueil.php');

	}

}
