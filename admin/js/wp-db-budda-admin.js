(function( $ ) {
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





	$(function(){
		// Maintain array of dates
		var dates = new Array();
		var hidDates = $('.addnew-dates');
		var todayDate = new Date();

		var bookedDates = new Array();
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

		// Takes a 1-digit number and inserts a zero before it
		function padNumber(number) {
			var ret = new String(number);
			if (ret.length == 1)
				ret = "0" + ret;
			return ret;
		}

		function stringToDate(dateStr) {
			var parts = dateStr.split("-");
			return new Date(parts[0], parts[1] - 1, parts[2]);
		}

		var postData = {
			_ajax_nonce: ajax_booked_dates.nonce,
			id: ajax_booked_dates.id,
			get_action: ajax_booked_dates.get_action,
			action: "booked_dates"

		};


	//	console.log(postData);


		$.post(ajax_booked_dates.ajax_url,postData, function(data) {                //callback


			$.each(data, function( key, value ) {

				if(key == 'booked_dates'){
					$.each(value, function( key1, value1 ) {

						value1.toString();
						bookedDates.push(value1);

					});
				}

				if(key == 'current_user_dates'){
					$.each(value, function( key1, value1 ) {

						value1.toString();
						currentUserDates.push( value1 );
						dates.push( value1);

					});
				}

			});
			//console.log(currentUserDates);

			$("#booking-dates").datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: todayDate,
				onSelect: function (dateText, inst) {
					addOrRemoveDate(dateText);
					hidDates.val(dates);
				},
				beforeShowDay: function (date) {

					var year = date.getFullYear();
					// months and days are inserted into the array in the form, e.g "01/01/2009", but here the format is "1/1/2009"
					var month = padNumber(date.getMonth() + 1);
					var day = padNumber(date.getDate());
					// This depends on the datepicker's date format
					var dateString = year + "-" + month + "-" + day;


					var today_date = todayDate.getDate();
					var today_month = todayDate.getMonth() + 1; //Months are zero based
					var today_year = todayDate.getFullYear();
					var todayDate2 = today_year + "-" + today_month + "-" + today_date;

					var currentDate = -1;
					var bookedDisabled = $.inArray( dateString,bookedDates );

					if(currentUserDates.length>0){
						currentDate = $.inArray(dateString,currentUserDates );

					}
					//console.log(date);

					var gotDate = $.inArray(dateString, dates);

					if (bookedDisabled>=0 && (stringToDate(dateString)>=stringToDate(todayDate2))) {
						//console.log(highlight);
						return [false, "", ''];
					}
					else if(currentDate>=0 && (stringToDate(dateString)>=stringToDate(todayDate2))){
						if (gotDate >= 0) {
							return [true, "ui-state-highlight", ''];
						}
						else return [true, '', ''];


					}
					else {
						//if(new Date(dateString)<new Date(todayDate2)) return [true, 'greyClass', ''];
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









		/*.done(function() {
				//alert( "second success" );
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				console.log(arguments);
				alert( errorThrown);
			});*/

		/*var options = {
			url: "/blocks/calendar/calendarData.php",
			type:'POST',
			data:'action=bookedDates',
			dataType:'json',
			success: function (data){
				console.log(data);

				$.each(data, function( key, value ) {
					value.toString();
					value=value.replace(/\-/g, "/");
					console.log(value);
					var x=new Date( value );
					eventDates[x] = x;
				});
				console.log(eventDates);



				$('.ui-datepicker-current-day').click();

			},
			error: function(data) {}
		};
		$.ajax(options);*/






	});

})( jQuery );
