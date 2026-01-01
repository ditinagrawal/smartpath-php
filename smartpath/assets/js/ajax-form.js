$(function() {

	// Get the form.
	var form = $('#contact-form');

	// Get the messages div.
	var formMessages = $('.ajax-response');

	// Set up an event listener for the contact form.
	$(form).submit(function(e) {
		// Stop the browser from submitting the form.
		e.preventDefault();

		// Get CSRF token from meta tag or form
		var csrfToken = $('meta[name="csrf-token"]').attr('content') || 
		                $('input[name="_token"]').val() ||
		                getCookie('XSRF-TOKEN');

		// Serialize the form data.
		var formData = $(form).serialize();
		
		// Add CSRF token if not already in form
		if (csrfToken && !formData.includes('_token')) {
			formData += '&_token=' + encodeURIComponent(csrfToken);
		}

		// Show loading state
		var submitBtn = $(form).find('button[type="submit"]');
		var originalText = submitBtn.html();
		submitBtn.prop('disabled', true);
		submitBtn.html('Sending...');
		$(formMessages).hide();

		// Submit the form using AJAX.
		$.ajax({
			type: 'POST',
			url: $(form).attr('action'),
			data: formData,
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Accept': 'application/json'
			}
		})
		.done(function(response) {
			// Handle JSON response
			if (typeof response === 'object' && response.message) {
				$(formMessages).removeClass('error').addClass('success');
				$(formMessages).text(response.message || 'Thank you! Your message has been sent.');
			} else {
				// Handle plain text response
				$(formMessages).removeClass('error').addClass('success');
				$(formMessages).text(response);
			}
			$(formMessages).show();

			// Clear the form.
			$('#contact-form input,#contact-form textarea').val('');
			
			// Reset button
			submitBtn.prop('disabled', false);
			submitBtn.html(originalText);
		})
		.fail(function(xhr) {
			// Make sure that the formMessages div has the 'error' class.
			$(formMessages).removeClass('success').addClass('error');

			// Set the message text.
			var errorMessage = 'Oops! An error occurred and your message could not be sent.';
			if (xhr.responseJSON && xhr.responseJSON.message) {
				errorMessage = xhr.responseJSON.message;
			} else if (xhr.responseText) {
				try {
					var jsonResponse = JSON.parse(xhr.responseText);
					if (jsonResponse.message) {
						errorMessage = jsonResponse.message;
					}
				} catch(e) {
					errorMessage = xhr.responseText;
				}
			}
			$(formMessages).text(errorMessage);
			$(formMessages).show();
			
			// Reset button
			submitBtn.prop('disabled', false);
			submitBtn.html(originalText);
		});
	});
	
	// Helper function to get cookie value
	function getCookie(name) {
		var value = "; " + document.cookie;
		var parts = value.split("; " + name + "=");
		if (parts.length == 2) return parts.pop().split(";").shift();
		return null;
	}

});
