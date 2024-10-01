<?php //var_dump($forfait); ?>

<div class="container">
<table class="table">
<thead>
    <tr>
        <th>ID</th>
        <th>libelle</th>
        <th>montant</th>
    </tr>
</thead>
<tbody>
    <?php foreach($forfait as $f){ ?>
        <tr>
            <td><?=$f['id']?></td> 
            <td><?=$f['libelle']?></td>
            <td><?=$f['montant']?></td>
            <td><a href="index.php?uc=gestion&action=suppression&id=<?=$f['id']?>">Supprimer le forfait</a></td>
        </tr>
        <?php } ?>

</tbody>
</table>
</div>

<div>

<h2>Ajout de forfait</h1>
    <form action="index.php?uc=gestion&action=ajout" method="post">

    <div>
        <label for="id">ID (max 3 charcteres)</label>
        <input type="text" name="idforfait" id="idforfait" maxlength="3">
    </div><br>
    <div>
        <label for="id">libellé</label>
        <input type="text" name="libelleforfait" id="libelleforfait">
    </div><br>
    <div>
        <label for="id">montant</label>
        <input type="text" name="montantforfait" id="montantforfait">
    <br><br>
        <input type="submit" value="envoyer">
    </div>    



    </form>
</div>