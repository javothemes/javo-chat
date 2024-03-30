(function ($) {
	'use strict';

	// Function to open preview modal with sample content
	$('#preview_button').on('click', function (event) {
		event.preventDefault();
		var selectedSkin = $('#javo_chat_selected_skin').val();
		var title = 'Sample Title'; // Sample title
		var content = 'Sample Content1 It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed'; // Sample content
		$.ajax({
			url: javo_chat_ajax_obj.ajax_url,
			type: 'POST',
			data: {
				action: 'load_template_content',
				skin: selectedSkin,
				title: title,
				content: content,
				security: javo_chat_ajax_obj.security
			},
			success: function (response) {
				$('#preview_content').html(response);
				$('#previewModal').modal('show'); // Open modal
			}
		});
	});

	// Function to validate email format
	function validateEmail(email) {
		var re = /\S+@\S+\.\S+/;
		return re.test(email);
	}

	// Function to handle test email sending
	$('#test_send_button').on('click', function () {
		var testEmail = $('#test_email').val();
		var isValidEmail = validateEmail(testEmail);
		if (!isValidEmail) {
			alert('Please enter a valid email address.');
			return;
		}
		var selectedSkin = $('#javo_chat_selected_skin').val();
		var title = 'Sample Title'; // Sample title
		var content = 'Sample Content2 It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed '; // Sample content
		$.ajax({
			url: javo_chat_ajax_obj.ajax_url,
			type: 'POST',
			data: {
				action: 'send_test_email',
				skin: selectedSkin,
				test_email: testEmail,
				title: title,
				content: content,
				security: javo_chat_ajax_obj.security
			},
			success: function (response) {
				if (response.success) {
					alert(response.data);
				} else {
					alert('Failed to send test email. Please try again later.');
				}
			},
			error: function () {
				alert('Failed to send test email. Please try again later.');
			}
		});
	});

})(jQuery);
