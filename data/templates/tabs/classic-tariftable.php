<h2><?php _e('Tarifs'); ?></h2>

<?php
    if( !empty($tarifs_classic) )
    {
        ?>
            <form id="wtlv_classic">

                <table class="wp-list-table widefat fixed striped pages">

                    <thead>
                    <th width="150px">#</th>
                    <th><?php _e('Département de livraison'); ?></th>
                    <th><?php _e('Tarif'); ?></th>
                    </thead>

                    <tbody>
                    <?php
                    $i = 1;

                    foreach( $tarifs_classic as $k => $v )
                    {
                        ?>
                        <tr data-id="<?php echo $v['id']; ?>">
                            <td><?php echo $i; $i++; ?></td>
                            <td><?php echo $v['arrival']; ?></td>
                            <td><?php echo $v['cost']; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>

                </table>

            </form>

        <?php
    }
    else
    {
        _e('Error occured while obtain data from generic site.');
    }
?>



