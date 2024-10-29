jQuery(document).ready(function(){

    window.WTLV = {
        attr: {},

        adm_page_load: function(menutype){
            var data = {
                _handler: window.WTLV.attr.prefix,
                _action: 'change_adm_tabs',
                _subaction: 'change_tab',
                menutype: menutype
            };

            jQuery.ajax(
                {
                    url: '',
                    type: 'POST',
                    data: data,
                    dataType: 'JSON',
                    beforeSend: function(){
                        wtlv_preloader(true);
                    },
                    success: function (data) {
                        if (data.status) {
                            jQuery('.wrap').html(data.html);
                        }
                    },
                    complete: function(){
                        wtlv_preloader(false);
                    }
                }
            );
        },
    }

    jQuery(document).bind('ready ajaxStop', function() {
        if (!jQuery(".wtlv-logo").length) {
            var logo = '<span class="wtlv-logo"></span>';
            if (!jQuery("ul#shipping_method").length) {
                jQuery("input[value='wtlv_shipping_classic']").prev().prev().after(logo);
                jQuery("input[value='wtlv_shipping_express']").prev().prev().after(logo);
            }
            else {
                jQuery("input[value='wtlv_shipping_classic']").parent().find('label').prepend(logo);
                jQuery("input[value='wtlv_shipping_express']").parent().find('label').prepend(logo);
            }
        }


    });

});

function wtlv_preloader(show)
{
    var id = 'wtlv-preloader';
    var speed = 200;
    var preloader = jQuery('#' + id);

    if (show)
    {
        if (preloader.length)
        {
            preloader.stop().fadeIn(speed).unbind();
        }
        else
        {
            preloader = jQuery('<div>').attr({id: id})
                .append(
                    jQuery('<div>').addClass("cssload-rect1"),
                    jQuery('<div>').addClass("cssload-rect2"),
                    jQuery('<div>').addClass("cssload-rect3")
                ).hide();
            mask = jQuery('<div>').attr({id: id + "-mask"})

            jQuery('body').append(preloader, mask);

            preloader = jQuery('#' + id);
            preloader.fadeIn(speed).unbind();
        }

        setTimeout(function(){
            preloader.click(function(){
                wtlv_preloader(false);
            });
        }, 5000);
    }
    else
    {
        preloader.fadeOut(speed, function(){
            jQuery(this).remove();
            jQuery("#" +  id + "-mask").remove();
        });
    }
}