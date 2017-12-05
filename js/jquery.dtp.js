jQuery(document).ready(function() {
    jQuery("#ecard_send_time").datetimepicker({
        value: new Date(),
        dayOfWeekStart: 1,
        minDate: 0,
        lazyInit: true,
        defaultDate: new Date(),
    });
});
