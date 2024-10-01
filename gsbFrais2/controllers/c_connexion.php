<?php

class connexion{

    private $pdo;

    public function __construct(){
        $this->pdo = PdoGsb::getPdoGsb();
    }

    public function demandeConnexion(){
        include ('views/v_menu.php');
        include("views/v_connexion.php");
    }

    public function valideConnexion(){
        
            $login = $_REQUEST['login'];
            $mdp = $_REQUEST['mdp'];
            /** @var PdoGsb $pdo */

        
            $visiteur = $this->pdo->getInfosVisiteur($login, $mdp);
            $etat = 'visiteur';
            if($visiteur == null){

            $visiteur = $this->pdo->getInfosComptable($login, $mdp);
            $etat = 'comptable';
            }

            if (!is_array($visiteur)) {
                ajouterErreur("Login ou mot de passe incorrect");
                include("views/v_erreurs.php");
                include("views/v_connexion.php");
            } else {
                $id = $visiteur['id'];
                $nom = $visiteur['nom'];
                $prenom = $visiteur['prenom'];
                connecter($id, $nom, $prenom);
                
                include 'views/v_sommaire.php';
                include 'views/v_accueil.php';
            }
    }

    public function deconnexion(){
        deconnecter();
        include 'views/v_menu.php';
        include("views/v_connexion.php");
    }

    public function defaut(){
        include("views/v_connexion.php");
    }


}


