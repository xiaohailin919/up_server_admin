/**
 * tip
 */

var tip = {
    toggle: function() {
        $("[tip-toggle='tooltip']").each(function() {
            var tipField = '';
            if(typeof($(this).attr("tip-field")) != "undefined") {
                tipField = $(this).attr('tip-field');
            }

            var title = tip.title(tipField);
            if (!$(this).children("a").hasClass('tip')) {
                $(this).append('<a class="tip" href="javascript:void(0);" data-toggle="tooltip" title="' + title + '"><i class="mdi mdi-alert-circle"></i></a>');
            }
        });

        $("a[data-toggle='tooltip']").each(function() {
            $(this).tooltip({
                container: 'body',
                html: true,
                delay: {hide: 1000}

            }).on('shown.bs.tooltip', function (event) {
                var that = this;
                $(".tooltip").on('mouseenter', function () {
                    $(that).attr('in', true);
                }).on('mouseleave', function () {
                    $(that).removeAttr('in');
                    $(that).tooltip('hide');
                });

            }).on('hide.bs.tooltip', function (event) {
                if ($(this).attr('in')) {
                    event.preventDefault();
                }
            });
        });
    },
    title: function(tipField) {

        if (tip.titleMap().hasOwnProperty(tipField)) {
            return tip.titleMap()[tipField];
        }
        return '';
    },
    titleMap: function() {
        return {
            'publisher_tip' : 'Multiple publishers should be separated by English commas.',
        };
    }
};