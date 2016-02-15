(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $(function () {
        // Maintain array of dates
        var dates = new Array();


        var hidDates = $('.addnew-dates');
        var todayDate = new Date();

        //booked dates
        var bookedDates = new Array();

        //booked dates for current user
        var currentUserDates = new Array();


        function addDate(date) {
            if ($.inArray(date, dates) < 0)
                dates.push(date);
        }

        function removeDate(index) {
            dates.splice(index, 1);
        }

        // Adds a date if we don't have it yet, else remove it
        function addOrRemoveDate(date) {

            var index = $.inArray(date, dates);
            if (index >= 0)
                removeDate(index);
            else
                addDate(date);

        }

        function formatDateISO(date) {

            var dd = date.getDate();
            var mm = date.getMonth() + 1; //January is 0!
            var yyyy = date.getFullYear();


            if (dd < 10) {
                dd = '0' + dd
            }

            if (mm < 10) {
                mm = '0' + mm
            }

            return yyyy + '-' + mm + '-' + dd;

        }

        // data for ajax request
        var postData = {
            _ajax_nonce: ajax_booked_dates.nonce,
            id: ajax_booked_dates.id,
            get_action: ajax_booked_dates.get_action,
            action: "booked_dates"

        };


        $.post(ajax_booked_dates.ajax_url, postData, function (data) {


            $.each(data, function (key, value) {

                if (key == 'booked_dates') {
                    $.each(value, function (key1, value1) {
                        bookedDates.push(value1);

                    });
                }

                if (key == 'current_user_dates') {
                    $.each(value, function (key1, value1) {

                        currentUserDates.push(value1);
                        dates.push(value1);

                    });
                }

            });


            $("#booking-dates").datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: todayDate,
                onSelect: function (dateText, inst) {

                    //choose more than one date
                    addOrRemoveDate(dateText);
                    hidDates.val(dates);
                },
                beforeShowDay: function (date) {

                    date = $.datepicker.formatDate('yy-mm-dd', date);


                    var bookedDisabled = $.inArray(date, bookedDates);

                    var currentDate = -1;
                    if (currentUserDates.length > 0) {
                        currentDate = $.inArray(date, currentUserDates);
                    }


                    var gotDate = $.inArray(date, dates);


                    if (bookedDisabled >= 0 && (date >= formatDateISO(todayDate))) {
                        return [false, "", ''];
                    }
                    else if (currentDate >= 0 && (date >= formatDateISO(todayDate))) {
                        if (gotDate >= 0) {
                            return [true, "ui-state-highlight", ''];
                        }
                        else return [true, '', ''];
                    }
                    else {

                        if (gotDate >= 0) {
                            // Enable date so it can be deselected. Set style to be highlighted
                            return [true, "ui-state-highlight"];
                        }
                        else
                            return [true, '', ''];
                    }
                }
            });

        });

    });

})(jQuery);