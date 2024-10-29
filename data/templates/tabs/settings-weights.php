<h2><?php _e('Poids'); ?></h2>

<?php
    if( !empty($weights) )
    {
        ?>
            <form id="wtlv_weights">
                <p>
                    <?php echo __('Minimum') . " : " . $weights['from'] . " kg."; ?>
                    <br/>
                    <?php echo __('Maximum') . " : " . $weights['to'] . " kg.";?>
                </p>
            </form>

        <?php
    }
    else
    {
        _e('Error occured while obtain data from generic site.');
    }
?>



