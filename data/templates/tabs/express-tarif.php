<h2><?php _e('Livraison tarif');?></h2>

<?php
    if( !empty($tarifs_express) )
    {
        ?>
            <form id="wtlv_express">
                <p>
                    <?php echo __('Tarif pour le livraison express') . " : " . $tarifs_express['cost'] . " €."; ?>
                </p>
            </form>
        <?php
    }
    else
    {
        _e('Error occured while obtain data from generic site.');
    }
?>


