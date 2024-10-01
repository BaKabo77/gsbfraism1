<div id="accueil">
    GESTION DES FRAIS DES VISITEURS

    <h1>VISITEUR :</h1>


    <form action="index.php?uc=gestion&action=entrerFrais" method='post'>

        <div>
            <label for="numero"> Numéro :</label> <br><br>
            <input type="text"  name="numero" id="numero" value="<?= $visiteur['id'] ?>" placeholder="<?= $visiteur['id'] ?>" readonly>
        </div>

        <p>

            <labsel for="periode"> <h3>Periode d'engagement :</h3></label> 

            <div>mois (2 chiffres)<br>
            <input type="text" maxlength="2" name="mois" id="mois"></div>

            <div>année (4 chiffres)<br>
            <input type="text" maxlength="4" name="annee" id="annee"></div>

        </p>
        
        <p>
            <h3>Frais au forfait</h3>
        </p>

        <div>
            <label for="repas"> Repas midi :</label> <br>
            <input type="text" name="repas" id="repas">
        </div>

        <div>
            <label for="nuit"> Nuitées :</label> <br>
            <input type="text" name="nuit" id="nuit">
        </div>

        <div>
            <label for="etape"> Etapes :</label> <br>
            <input type="text" name="etape" id="etape">
        </div>

        <div>
            <label for="km"> Kilometres :</label> <br>
            <input type="text" name="km" id="km">
        </div>
        <br>

        <input type="submit">

    </form>

</div>

