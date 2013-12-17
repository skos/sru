$( document ).ready(function() {
    $(function () {
        $("input[type='Calender']").datepicker({
            constrainInput: true,
            showOn: 'button',
            buttonImage: "/i/img/calendar.png",
            buttonImageOnly: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
        $("input[type='TimeCalender']").datetimepicker({
            constrainInput: true,
            showOn: 'button',
            buttonImage: "/i/img/calendar.png",
            buttonImageOnly: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
        $(".ui-datepicker-trigger").removeAttr("title");
    });
});