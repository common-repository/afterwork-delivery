<style>
    .prd-info {
        width: 100%;
        font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;
    }

    .prd-info th {
        text-align: center;
        vertical-align: middle;
        border: 1px solid #eee;
        word-wrap: break-word;
        width: 33%;
    }

    .prd-info td {
        text-align: left;
        vertical-align: middle;
        border: 1px solid #eee;
        word-wrap: break-word;
    }
</style>

<h2><?php _e('L\'information sur les produits : '); ?></h2>
<table class="td prd-info" cellspacing="0" cellpadding="6">
    <thead>
    <tr>
        <th><?php _e('Produit'); ?></th>
        <th><?php _e('Poids'); ?></th>
        <th><?php _e('Dimensions longueur x largeur x hauteur)'); ?></th>
    </tr>
    </thead>
    <?php
    foreach( $order->get_items() as $k => $v )
    {
        $_product = apply_filters('woocommerce_order_item_product', $this->object->get_product_from_item($v), $v);
        $item_meta = new WC_Order_Item_Meta($v, $_product);

        ?>
        <tr>
            <td class="td">
                <?php
                    // Product name
                    echo apply_filters('woocommerce_order_item_name', $v['name'], $v, false);

                    // Variation
                    if ( !empty($item_meta->meta) )
                    {
                        echo '<br/><small>' . nl2br($item_meta->display(true, true, '_', "\n")) . '</small>';
                    }
                ?>
            </td>
            <td class="td">
                <?php
                if( $_product->get_weight() )
                {
                    if( esc_attr(get_option('woocommerce_weight_unit')) == "kg" )
                    {
                        echo (float) $_product->get_weight() . esc_attr(get_option('woocommerce_weight_unit'));
                    }
                    else
                    {
                        switch( esc_attr(get_option('woocommerce_weight_unit')) )
                        {
                            case "g":
                                $weight = (float) $_product->get_weight() / 1000.0;
                            break;

                            case "lbs":
                                $weight = (float) $_product->get_weight() * 0.45;
                            break;

                            case "oz":
                                $weight = (float) $_product->get_weight() * 0.03;
                            break;

                            default:
                                $weight = "can not be calculated in ";
                            break;
                        }
                        echo $_product->get_weight() . ' ' .  esc_attr(get_option('woocommerce_weight_unit')) . '<br /> (' . $weight . ' kg)';
                    }
                }
                ?>
            </td>
            <td class="td"><?php echo $_product->get_dimensions(); ?></td>
        </tr>
        <?php
    }
    ?>
</table>
<h2><?php _e('L\'information sur le site e-commerce : '); ?></h2>

<?php
    $site_info = implode( '<br/>', array_filter(
        array(
            __('Name: ') . get_bloginfo('name'),
            __('Link: ') . home_url(),
            __('Description : ') . get_bloginfo('description'),
            __('Admin email : ') . get_bloginfo('admin_email'),
        ) ) );

    echo $site_info;
?>
