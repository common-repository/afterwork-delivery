<ul class="woocommerce-error">
    <?php
        if ( 1 == $data )
        {
            echo "<li>" . __('Le poids d\'un des produits de votre ordre est supérieur au poids accessible de service de livraison Afterwork') . ".</li>";
        }
        else
        {
            echo "<li>" . __('Les poids de plusieurs produits de votre ordre sont supérieurs au poids accessible de service de livraison Afterwork') . ".</li>";
        }
    ?>
</ul>