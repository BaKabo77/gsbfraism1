<?php
/**
 * Classe d'accès aux données.

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoGsb{
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=gsbfrais';
      	private static $user='root' ;
      	private static $mdp='' ;
		private static $monPdo;
		private static $monPdoGsb=null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */
	private function __construct(){
    	PdoGsb::$monPdo = new PDO(PdoGsb::$serveur.';'.PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp);
		PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoGsb::$monPdo = null;
	}

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     * @return null L'unique objet de la classe PdoGsb
     */
	public  static function getPdoGsb(){
		if(PdoGsb::$monPdoGsb==null){
			PdoGsb::$monPdoGsb= new PdoGsb();
		}
		return PdoGsb::$monPdoGsb;
	}

    /**
     * Retourne les informations d'un visiteur
     * @param $login
     * @param $mdp
     * @return mixed L'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp){
        $req = "select id, nom, prenom from visiteur where login='$login' and mdp='$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }

    public function getInfosComptable($login, $mdp){
        $req = "select id, nom, prenom from comptable where login='$login' and mdp='$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }

    /**
    * Transforme une date au format français jj/mm/aaaa vers le format anglais aaaa-mm-jj
     
    * @param $madate au format  jj/mm/aaaa
    * @return la date au format anglais aaaa-mm-jj
    */
    public function dateAnglaisVersFrancais($maDate){
        @list($annee,$mois,$jour)=explode('-',$maDate);
        $date="$jour"."/".$mois."/".$annee;
        return $date;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments
     * La boucle foreach ne peut être utilisée ici, car on procède
     * à une modification de la structure itérée - transformation du champ date-
     * @param $idVisiteur
     * @param $mois 'sous la forme aaaamm
     * @return array 'Tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur,$mois){
        $req = "select * from lignefraishorsforfait where idvisiteur ='$idVisiteur' 
		and mois = '$mois' ";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i=0; $i<$nbLignes; $i++){
            $date = $lesLignes[$i]['date'];
            //Gestion des dates
            @list($annee,$mois,$jour) = explode('-',$date);
            $dateStr = "$jour"."/".$mois."/".$annee;
            $lesLignes[$i]['date'] = $dateStr;
        }
        return $lesLignes;
    }

    /**
     * Retourne les mois pour lesquels, un visiteur a une fiche de frais
     * @param $idVisiteur
     * @return array 'Un tableau associatif de clé un mois - aaaamm - et de valeurs l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur){
        $req = "select mois from  fichefrais where idvisiteur ='$idVisiteur' order by mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois =array();
        $laLigne = $res->fetch();
        while($laLigne != null)	{
            $mois = $laLigne['mois'];
            $numAnnee =substr( $mois,0,4);
            $numMois =substr( $mois,4,2);
            $lesMois["$mois"]=array(
                "mois"=>"$mois",
                "numAnnee"  => "$numAnnee",
                "numMois"  => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donn�
     * @param $idVisiteur
     * @param $mois 'sous la forme aaaamm
     * @return mixed 'Un tableau avec des champs de jointure entre une fiche de frais et la ligne d'�tat
     */
    public function getLesInfosFicheFrais($idVisiteur,$mois){
        $req = "select fichefrais.idEtat as idEtat, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs, 
			fichefrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join etat on fichefrais.idEtat = etat.id 
			where fichefrais.idVisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }

    public function creationFicheFrais($id,$date,$repas,$km,$etape,$nuit){

        $nb = $repas+$nuit+$km+$etape;

        $sql = "insert into fichefrais values (:id,:date,:nb,NULL,NULL,'CR')";
        $rec = PdoGsb::$monPdo->prepare($sql);
        $rec->bindValue(':id',$id,PDO::PARAM_STR);
        $rec->bindValue(':nb',$nb,PDO::PARAM_STR);
        $rec->bindValue(':date',$date,PDO::PARAM_STR);
        $rec->execute();

        $sql = 'INSERT INTO lignefraisforfait VALUES (:id,:date,"REP",:repas)';
        $rec = PdoGsb::$monPdo->prepare($sql);
        $rec->bindValue(':id',$id,PDO::PARAM_STR);
        $rec->bindValue(':date',$date,PDO::PARAM_STR);
        $rec->bindValue(':repas',$repas,PDO::PARAM_INT);
        $rec->execute();

        $sql = 'INSERT INTO lignefraisforfait VALUES (:id,:date,"NUI",:nuit)';
        $rec = PdoGsb::$monPdo->prepare($sql);
        $rec->bindValue(':id',$id,PDO::PARAM_STR);
        $rec->bindValue(':date',$date,PDO::PARAM_STR);
        $rec->bindValue(':nuit',$nuit,PDO::PARAM_INT);
        $rec->execute();

        $sql = 'INSERT INTO lignefraisforfait VALUES (:id,:date,"ETP",:etape)';
        $rec = PdoGsb::$monPdo->prepare($sql);
        $rec->bindValue(":id",$id,PDO::PARAM_STR);
        $rec->bindValue(":date",$date,PDO::PARAM_STR);
        $rec->bindValue(":etape",$etape,PDO::PARAM_INT);
        $rec->execute();

        $sql = 'INSERT INTO lignefraisforfait VALUES (:id,:date,"KM",:km)';
        $rec = PdoGsb::$monPdo->prepare($sql);
        $rec->bindValue(':id',$id,PDO::PARAM_STR);
        $rec->bindValue(':date',$date,PDO::PARAM_STR);
        $rec->bindValue(':km',$km,PDO::PARAM_INT);
        $rec->execute();
        
    }

    public function getFraisForfait(){
        $sql = 'SELECT * FROM `fraisforfait`';
        $req = PdoGsb::$monPdo->query($sql);
        $resultat = $req->fetchAll();
        return $resultat;
    }


    public function suppressionForfait($id){
        $sql = "DELETE FROM fraisforfait where fraisforfait.id = :id";
        $req = PdoGsb::$monPdo->prepare($sql);
        $req->bindValue(':id',$id,PDO::PARAM_STR);
        $req->execute();
    }

    public function ajoutForfait($id,$lib,$montant){
        $sql='insert into fraisforfait values (?,?,?)';
        $req = PdoGsb::$monPdo->prepare($sql);
        $req->bindValue(1,$id,PDO::PARAM_STR);
        $req->bindValue(2,$lib,PDO::PARAM_STR);
        $req->bindValue(3,$montant,PDO::PARAM_STR);
        $req->execute();



    }

    

}

/**public function creationFraisHorsForfait($id,$date){

        $sql = 'insert into lignefraishorsforfait (idVisiteur,mois) values (:id,:mois)';
        $rec = PdoGsb::$monPdo->prepare($sql);
        $rec->bindValue(':id',$id,PDO::PARAM_STR);
        $rec->bindValue(':mois',$date,PDO::PARAM_STR);
        $rec->execute();

                $sql = "Update fichefrais set nbJustificatifs = :nb where fichefrais.nbJustificatifs = null and fichefrais.idVisiteur = :id ";
        $rec = PdoGsb::$monPdo->prepare($sql);
        $rec->bindValue(':nb',$nb,PDO::PARAM_STR);
        $rec->bindValue(':id',$id,PDO::PARAM_STR);
        $rec->execute();
}
        */