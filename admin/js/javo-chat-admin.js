(function ($) {
	'use strict';

	// Function to open preview modal with sample content
	$('#preview_button').on('click', function (event) {
		event.preventDefault();
		var selectedSkin = $('#javo_chat_selected_skin').val();
		var title = 'Email Title';
		var content = 'Email Content';
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
				//console.log('email : ' + response);
				$('#preview_content').html(response);
				$('#previewModal').modal('show'); // Open Modal
			}
		});
	});

	$('#test_send_button').on('click', function (event) {
		event.preventDefault();
		var selectedSkin = $('#javo_chat_selected_skin').val();
		var testEmail = $('#test_email').val();
		$.ajax({
			url: javo_chat_ajax_obj.ajax_url,
			type: 'POST',
			data: {
				action: 'send_test_email',
				skin: selectedSkin,
				test_email: testEmail,
				security: javo_chat_ajax_obj.security
			},
			success: function (response) {
				if (response.success) {
					alert('Test email sent successfully.');
				} else {
					alert('Failed to send test email. ' + response.data);
				}
			},
			error: function () {
				alert('Failed to send test email. Please try again later.');
			}
		});
	});

})(jQuery);
