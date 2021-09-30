<section>
<div id="Center">
   
<?php
if ($Count>0) {

    while($Actu=$RecupArticle->fetch(PDO::FETCH_OBJ)) { 

        echo '
        <article>';
        $Erreur=$_GET['erreur'];
        $Valid=$_GET['valid'];
        if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
        if (isset($Valid)) { echo "<font color='#00CC00'>".$Valid."</font></p>"; }

        echo $Actu->message;
        if (($Cnx_Admin==true)||($Cnx_Client==true)) { 
            echo '<a href="'.$Home.'/Admin/Article/Nouveau/?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a><a href="'.$Home.'/Admin/Article/supprimer.php?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>';
        } 
        echo '</article>';
    }
}
else {
    echo '
    <article>Aucun article pour le moment !<BR /><BR />';

    echo $Actu->message;
    if ($Cnx_Admin==true) { 
        echo '<a href="'.$Home.'/Admin/Article/Nouveau/"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a><a href="'.$Home.'/Admin/Article/supprimer.php?id='.$Actu->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>';
    } 
    echo '</article>';
}
?>

</div>
</section>