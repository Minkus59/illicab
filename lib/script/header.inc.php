<header>
    <div id="Center">
    <div id="Logo">   
    <?php if ($PageActu=="/BNS-Direct-Air/") { ?> 
        <a href='<?php echo $Home; ?>'><img src="<?php echo $Home; ?>/lib/header/logoBNS.png"/></a>
        
    <?php } 
    else { ?>
        <a href='<?php echo $Home; ?>'><img src="<?php echo $ParamLogoHeader->logo; ?>"/></a>
    <?php } ?>
    </div>
        <nav>
            <ul>
                <a href="<?php echo $Home; ?>"><li <?php if ($PageActu=="/") { echo "class='Up'"; } ?>>Trajet Particulier</li></a>
                <?php
                while ($Page=$SelectPageActif->fetch(PDO::FETCH_OBJ)) {
                ?>
                    <a href="<?php echo $Home.$Page->lien; ?>"><li <?php if ($PageActu==$Page->lien) { echo "class='Up'"; } ?>><?php echo $Page->libele ?>

                    <?php 
                    $SelectSousMenu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' AND statue='1' ORDER BY position ASC");
                    $SelectSousMenu->bindParam(':parrin', $Page->lien, PDO::PARAM_STR);
                    $SelectSousMenu->execute();
                    $CountSousMenu=$SelectSousMenu->rowCount();

                    if ($CountSousMenu>0) {
                        echo "<ul>";
                        while ($SousMenu=$SelectSousMenu->fetch(PDO::FETCH_OBJ)) { 
                            if ($Page->lien=="/Mon-compte/") { 
                                if ($Cnx_Client===true) { ?>
                                    <a href="<?php echo $Home.$SousMenu->lien; ?>"><li <?php if ($PageActu==$SousMenu->lien) { echo "class='Up'"; } ?>><?php echo $SousMenu->libele ?></li></a>
                                    <?php 
                                }
                            }
                            else { ?>
                                <a href="<?php echo $Home.$SousMenu->lien; ?>"><li <?php if ($PageActu==$SousMenu->lien) { echo "class='Up'"; } ?>><?php echo $SousMenu->libele ?></li></a>
                        <?php
                            }
                        }
                        echo "</ul>";
                    }
                    ?></li></a>
                <?php 
                } ?>
                <a href="<?php echo $Home; ?>/Contact/"><li <?php if ($PageActu=="/Contact/") { echo "class='Up'"; } ?>>Contact</li></a>
                <?php
                if ($Cnx_Chauffeur===true) { ?>
                    <a href="<?php echo $Home; ?>/Mon-compte/Roadmap/"><li <?php if ($PageActu==$Home."/Mon-compte/Roadmap/") { echo "class='Up'"; } ?>>Roadmap</li></a>
                <?php
                } ?>
            </ul>
        </nav>
    </div>
</header>