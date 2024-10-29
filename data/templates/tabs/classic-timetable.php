<h2><?php _e('Calendrier de livraison'); ?></h2>

<?php
    if( !empty($timetable_data) )
    {
        ?>
            <form id="wtlv_timetable">

                <table class="wp-list-table widefat fixed striped pages">

                    <thead>
                    <th width="150px"><?php _e('Jour'); ?></th>
                    <th><?php _e('De'); ?></th>
                    <th><?php _e('A'); ?></th>
                    </thead>

                    <tbody>
                    <?php
                    foreach($timetable_data as $k => $v)
                    {
                        ?>
                        <tr data-id="<?php echo $v['day']; ?>">
                            <td><?php echo $days[$k]; ?></td>
                            <td><?php echo $v['from']; ?></td>
                            <td><?php echo $v['to']; ?></td>
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



