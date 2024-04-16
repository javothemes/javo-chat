(function ($) {
	'use strict';

	// Function to open preview modal with sample content
	$('#preview_button').on('click', function (event) {
		event.preventDefault();
		var selectedOption = $('#javo_chat_skin_or_template').val(); // Get selected option value
		var selectedSkin = $('#javo_chat_email_fixed_skin').val();
		var title = 'Sample Title'; // Sample title
		var content = 'Sample Content1 It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed'; // Sample content

		// Include selected option value in AJAX data
		var ajaxData = {
			action: 'load_template_content',
			security: javo_chat_ajax_obj.security,
			skin: selectedSkin,
			skin_or_template: selectedOption // Include selected option value in AJAX data
		};

		// Include title and content only if skin is selected
		if (selectedOption === 'skin') {
			ajaxData.title = title;
			ajaxData.content = content;
		}

		$.ajax({
			url: javo_chat_ajax_obj.ajax_url,
			type: 'POST',
			data: ajaxData, // Use modified AJAX data
			success: function (response) {
				$('#preview_content').html(response);
				$('#previewModal').modal('show'); // Open modal after AJAX request
			}
		});
	});

	// Function to validate email format
	function validateEmail(email) {
		var re = /\S+@\S+\.\S+/;
		return re.test(email);
	}

	// Function to handle test email sending
	$('#test_send_button').on('click', function (event) {
		event.preventDefault();
		console.log('ok');
		var testEmail = $('#test_email').val();
		var isValidEmail = validateEmail(testEmail);
		if (!isValidEmail) {
			alert('Please enter a valid email address.');
			return;
		}
		var selectedSkin = $('#javo_chat_email_fixed_skin').val();
		var selectedOption = $('#javo_chat_skin_or_template').val(); // Get selected option value
		var selectedTemplateId = $('#javo_chat_email_template').val(); // Get selected template ID
		var title = 'Sample Title'; // Sample title
		var content = 'Sample Content2 It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed '; // Sample content
		$.ajax({
			url: javo_chat_ajax_obj.ajax_url,
			type: 'POST',
			data: {
				action: 'send_test_email',
				skin: selectedSkin,
				skin_or_template: selectedOption,
				email_template_id: selectedTemplateId,
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


	// Show/hide skin or template options based on selection
	$('#javo_chat_skin_or_template').change(function () {
		var selectedOption = $(this).val();
		if (selectedOption === 'skin') {
			$('#skin_options').show();
			$('#template_options').hide();
		} else if (selectedOption === 'template') {
			$('#skin_options').hide();
			$('#template_options').show();
		} else {
			$('#skin_options').hide();
			$('#template_options').hide();
		}
	});

	document.addEventListener('DOMContentLoaded', function () {
		var copyButtons = document.querySelectorAll('.copy-btn');
		copyButtons.forEach(function (button) {
			button.addEventListener('click', function (event) {
				event.preventDefault();  // Prevent the default form submit behavior
				navigator.clipboard.writeText(button.getAttribute('data-clipboard'))
					.then(() => alert('Copied!'))
					.catch(err => console.error('Error copying text: ', err));
			});
		});
	});

})(jQuery);
