<?php $this->Template_Get('areas/page-header'); ?>

<script type="text/javascript">

    jQuery(window).load(function() {
        window.WTLV.adm_page_load("classic");
    });

    jQuery(document).ready(function(){

        jQuery('body').on('click', 'h2 a', function() {
            jQuery("h2 a.nav-tab-active").removeClass("nav-tab-active");
            jQuery(this).addClass("nav-tab-active");
            window.WTLV.adm_page_load(jQuery(this).data("menutype"));
            return false;
        });

    });

</script>

<h1><?php echo get_admin_page_title(); ?></h1>

<h2 class="nav-tab-wrapper">
    <a href="#" class="nav-tab nav-tab-active" data-menutype="classic"><?php _e('Livraison classique'); ?></a>
    <a href="#" class="nav-tab" data-menutype="express"><?php _e('Livraison express'); ?></a>
    <a href="#" class="nav-tab" data-menutype="settings"><?php _e('Réglages'); ?></a>
</h2>

<div class="wrap">

</div>