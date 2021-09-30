<footer>
    <div id="BandeLien"> 
    <div id="Center">
        <li><a href="<?php echo $Home; ?>">Trajet particulier</a></li>
        <li><a href="<?php echo $Home; ?>/Trajet-professionnel/">Trajet professionnel</a></li>
        <li><a href="<?php echo $Home; ?>/BNS-Direct-Air/">BNS DIRECT - AIR</a></li>
    </div>
    </div>
    <div id="Center">

        <div id="Cadre1">  
        <?php if ($PageActu=="/BNS-Direct-Air/") { ?> 
            <a href='<?php echo $Home; ?>'><img src="<?php echo $Home; ?>/lib/header/logoBNS.png"/></a>
        <?php } 
        else { ?>
            <a href='<?php echo $Home; ?>'><img src="<?php echo $ParamLogoHeader->logo; ?>"/></a>
        <?php } ?>
            <BR /><BR />
        <ul>
            <?php if ($PageActu=="/BNS-Direct-Air/") { ?> 
                <li><img src='<?php echo $Home; ?>/lib/img/tel2.png'/> <?php echo $Telephone; ?></li>
            <?php } 
            else { ?>
                <li><img src='<?php echo $Home; ?>/lib/img/tel.png'/> <?php echo $Telephone; ?></li>
            <?php } ?>
            
        </ul>
        </div>
    
        <div id="Cadre2"> 
            <H3>Nos Services</H3><BR />
        <ul>
            <a href="<?php echo $Home; ?>"><li>Trajet particulier</li></a>
            <?php
            while ($PageFooter=$SelectPageActifFooter->fetch(PDO::FETCH_OBJ)) {
            ?>
                <a href="<?php echo $Home.$PageFooter->lien ?>"><li><?php echo $PageFooter->libele ?>

                <?php 
                $SelectSousMenuFooter=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' ORDER BY position ASC");
                $SelectSousMenuFooter->bindParam(':parrin', $PageFooter->lien, PDO::PARAM_STR);
                $SelectSousMenuFooter->execute();
                $CountSousMenu=$SelectSousMenuFooter->rowCount();

                if ($CountSousMenu>0) {
                    echo "<ul>";
                    while ($SousMenu=$SelectSousMenu->fetch(PDO::FETCH_OBJ)) { 
                        if ($Page->lien=="/Mon-compte/") { 
                            if ($Cnx_Client===true) { ?>
                                <a href="<?php echo $Home.$SousMenu->lien ?>"><li <?php if ($PageActu==$SousMenu->lien) { echo "class='Up'"; } ?>><?php echo $SousMenu->libele ?></li></a>
                    <?php 
                            }
                        }
                        else { ?>
                            <a href="<?php echo $Home.$SousMenu->lien ?>"><li <?php if ($PageActu==$SousMenu->lien) { echo "class='Up'"; } ?>><?php echo $SousMenu->libele ?></li></a>
                    <?php
                        }
                    }
                    echo "</ul>";
                }
            } ?>
            <a href="<?php echo $Home; ?>/Mentions-legales/"><li>Mentions-légales</li></a>
            <a href="<?php echo $Home; ?>/Contact/"><li>Contact</li></a>
            <a href="<?php echo $Home; ?>/FAQ/"><li>FAQ</li></a>
        </ul>
        </div>

        <div id="Cadre3">  
            <H3>Informations & reservations</H3> <BR />
        <ul>
            <?php if ($PageActu=="/BNS-Direct-Air/") { ?> 
                <li><img src='<?php echo $Home; ?>/lib/img/tel2.png'/> <?php echo $Telephone; ?></li>
            <?php } 
            else { ?>
                <li><img src='<?php echo $Home; ?>/lib/img/tel.png'/> <?php echo $Telephone; ?></li>
            <?php } ?>
            <?php if ($PageActu=="/BNS-Direct-Air/") { ?> 
                <li><img src='<?php echo $Home; ?>/lib/img/mail2.png'/> <?php echo $Destinataire; ?></li>
            <?php } 
            else { ?>
                <li><img src='<?php echo $Home; ?>/lib/img/mail.png'/> <?php echo $Destinataire; ?></li>
            <?php } ?>
        </ul>
        </div>
    </div>
    <div id="NeuroSoft">
    <a href="http://neuro-soft.fr/" target="_blank" title="NeuroSoft Team - Agence de communication"><img src="http://neuro-soft.fr/lib/img/En-tete.png" alt="NeuroSoft Team - Agence de communication"/></a>
    </div>
    <div id="panier"><a href="<?php echo $Home; ?>/Reservation/"><img src="<?php echo $Home; ?>/lib/img/panier.png"/></div>
</footer>