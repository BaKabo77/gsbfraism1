"<?php 

class gestion{

    private $pdo;

    public function __construct(){

        $this->pdo = PdoGsb::getPdoGsb();

    }

    public function entrerFrais(){

        try{

        $id = $_REQUEST['numero'];
        $mois = $_REQUEST['mois'];
        $annee = $_REQUEST['annee'];
        $repas = $_REQUEST['repas'];
        $nuit = $_REQUEST['nuit'];
        $etape = $_REQUEST['etape'];
        $km = $_REQUEST['km'];
        $date = strval($annee.$mois);

        $this->pdo->creationFicheFrais($id,$date,$repas,$km,$etape,$nuit);

        }catch(PDOException $e){

            echo "la requete n'a pas abouti";

        }
        
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

    public function AfficherForfait(){

        $forfait = $this->pdo->getFraisForfait();
        include('views/v_sommaire.php');
        include ('views/v_forfait.php');

    }

    public function suppression(){
        $id = $_REQUEST['id'];
        $this->pdo->suppressionForfait($id);

        $this->AfficherForfait();

    }

    public function ajout(){
        $id = $_REQUEST['idforfait'];
        $lib = $_REQUEST['libelleforfait'];
        $montant = $_REQUEST['montantforfait'];

        $this->pdo->ajoutForfait($id,$lib,$montant);
        $this->AfficherForfait();        

    }





}