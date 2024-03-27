/*
Chat Messaging Script:

This script handles various functionalities related to chat messaging, including:
- Retrieving messages
- Sending messages
- Marking read/unread messages
- Handling typing indicator
- UI interactions and AJAX requests

1. Define initial and additional message counts.
2. Initialize variables for sender ID, receiver ID, and intervals.
3. Hide chat elements initially.
4. Define functions for message retrieval, scrolling, loading messages, and showing/hiding loading message.
5. Start message retrieval and define variables for tracking all messages loaded and scroll position.
6. Define event handlers for sending messages, typing status, and input field.
7. Define functions for scrolling to bottom, sending messages, and updating chat messages.
8. Define functions for collecting and marking unread messages as read.
9. Define event handler for scroll/click events in the chat window.
10. Define functions for showing and hiding the typing indicator.
11. Define AJAX request function to handle typing status.

design
https://www.uplabs.com/posts/chat-ui-design-0b930711-4cfd-4ab4-b686-6e7785624b16?rel=muzli
https://www.uplabs.com/posts/chat-application-ui-cad863ef-96b3-4485-b05c-f75e9b7d7aeb?rel=muzli
https://themes.getbootstrap.com/preview/?theme_id=38342
https://dribbble.com/shots/14953087-KeyVue-chat/attachments/6670421?mode=media


1. Avatar
2. Hide buttons for visitors or no login
3. Test from No data. ( favorite msg )

Chat available - Widget

search : close
favorite, block : profile
block : block messages
Ref link

chat : Receiver ID? 
- Non login
- Admin
- My Chat ( if sender id = receiver id )
- 

Move to the message ( when you click the message : search or saved )


*/

(function ($) {
	$(document).ready(function () {
		if ($('#javo-chat-wrap').length) { // if chat element shows

			let chatSettings = window.chatSettings || {};

			const $wrap = $('#javo-chat-wrap');
			const jv_chat_mode = $wrap.data('jv-chat-mode');

			// Define initial and additional message counts
			initMsgAmount = 20; // Initial number of messages to retrieve
			var readPrevMsgAmount = 10; // Additional messages to retrieve on "Read previous conversation" click
			var loadMsgAmount = initMsgAmount; // Variable to store the number of messages to load
			var senderId = $('#javo-interface-inner').data('sender-id'); // Get sender_id from data attribute
			//var senderId;
			var receiverId; // Variable to store the receiver ID
			var intervalId; // Variable to store the interval ID

			// Maybe not using anymore
			var messageRetrievalInterval = 5000; // Interval for message retrieval (milliseconds)

			var sendMessageInterval = 5000; // Interval for sending messages after button click (milliseconds)    

			var activityInterval = 50000; // Online, Offline ( Activity ) Check
			var checkForNewMsgInterval = 5000; // check new message

			// Access the chatSettings object directly without redefining it
			// console.log(chatSettings.ajax_url);
			// console.log(chatSettings.nonce);
			// console.log('mode=' + jv_chat_mode);
			// console.log(chatSettings.is_logged_in);
			// console.log(chatSettings.chat_user_id);

			// Check if it's single mode and the user is not logged in
			if (jv_chat_mode === 'chat_single_mode') {

				// receiverId = 1; // Assuming 1 is the receiverId for the admin or target chat. It's for temp.
				receiverId = $wrap.data('jv-chat-receiver-id');

				console.log('single-senderId : ' + senderId);

				if (getCookie('visitor_email') || chatSettings.is_logged_in === 'true') {
					$('#chat-interface').show();
					initializeChatInterface(receiverId); // Init by the receiverId

					// console.log('single visitor-senderId2 : ' + senderId);
					// console.log('single visitor-receiverId :', receiverId);
					// console.log('single visitor-initMsgAmount :', initMsgAmount);
					// Existing logic to retrieve and display messages
					loadMsgAmount = initMsgAmount; // Reset the load msg amount if it's not the 1st click
					getChatMessages(loadMsgAmount, function () {
						scrollToBottom(); // Scroll to bottom after loading messages
					});
					intervalId = setInterval(checkForNewMessages, 5000);

					// // Automatically open the chat interface for single mode
					// openChatInterface(receiverId, senderId);
					$('.start-chat[data-receiver-id="' + receiverId + '"]').trigger('click');
				} else {
					$('#email-input-container').show(); // Email input
					$('#chat-interface').hide();
					$('#visitor-start-chat-button').on('click', function () {
						event.preventDefault();
						var email = $('#visitor-email-input').val();
						if (email === '') {
							alert('Please enter your email address.');
							return;
						}
						var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
						if (!emailRegex.test(email)) {
							alert('Please enter a valid email address.');
							return;
						}

						// AJAX request
						$.ajax({
							url: chatSettings.ajax_url, // URL to handle the AJAX request
							type: 'POST', // Send the request using POST method
							data: {
								action: 'visitor_start_chat', // Action to be handled by the server
								email: email, // Data to be sent (email address)
								nonce: chatSettings.nonce // Nonce value for security
							},
							success: function (response) {
								// console.log('Response:', response); // Add this line to log the response
								// Handle the response from the server
								if (response.success) {
									// Check if sender_id exists in the response
									if (response.data.sender_id) {
										console.log("ok-start chat");
										$('#javo-interface-inner').attr('data-sender-id', response.data.sender_id);
										var email = response.data.sender_id;
										document.cookie = "visitor_email=" + email + ";path=/;max-age=3600";
										chatSettings.chat_user_id = email;

										$('#email-input-container').hide(); // Hide Email Input

										// Active Chat
										$('#chat-interface').show();
										// Add the sender_id to the HTML
										initializeChatInterface(receiverId); // Init by the receiverId

										// console.log('single visitor-senderId2 : ' + senderId);
										// console.log('single visitor-receiverId :', receiverId);
										// console.log('single visitor-initMsgAmount :', initMsgAmount);
										// Existing logic to retrieve and display messages
										loadMsgAmount = initMsgAmount; // Reset the load msg amount if it's not the 1st click
										getChatMessages(loadMsgAmount, function () {
											scrollToBottom(); // Scroll to bottom after loading messages
										});
										intervalId = setInterval(checkForNewMessages, 5000);

										// // Automatically open the chat interface for single mode
										// openChatInterface(receiverId, senderId);
										$('.start-chat[data-receiver-id="' + receiverId + '"]').trigger('click');
									} else if (response.data.message) {
										console.log('email registered');
										// Display a message to prompt the user to log in
										$('#message-container').html('<p>' + response.data.message + '</p>');
									} else {
										console.log('Wrong access');
										// Sender ID exists, it means the email is already registered
										// Display a message to prompt the user to log in
										$('#message-container').html('<p>Something Wrong</p>');
									}
								} else {
									// Handle errors if any
									console.error('Error:', response.data.message);
								}
							},
							error: function (xhr, status, error) {
								// Handle AJAX errors
								console.error('AJAX Error:', error);
							}
						});
					});

				}

				if ($('#javo-chat-wrap').hasClass('chat_single_mode')) {
					$('#javo-chat-wrap').draggable();
				}

				$('#jv-floating-chat-button').click(function () {
					console.log('btn clicked');
					// Toggle chat interface display
					$('#javo-chat-wrap').toggle();
				});


			}

			// Get Cookie info
			function getCookie(name) {
				var value = "; " + document.cookie;
				var parts = value.split("; " + name + "=");
				if (parts.length == 2) return parts.pop().split(";").shift();
			}

			// End Chat
			$('#end-chat-button').click(function () {
				// Close the chat interface
				$('#javo-chat-wrap').hide();

				// Clear the cookie
				document.cookie = "visitor_email=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

				// Refresh the page to reflect changes
				window.location.reload();

				// Additional clean-up could be added here, like clearing local storage or sending a logout signal to your server
			});

			// Stop Interval
			function stopCheckingForMessages() {
				if (intervalId) {
					clearInterval(intervalId);
					intervalId = null; // Reset Interval
					// console.log("Stop Checking New Message");
				}
			}

			// Function to check for new messages
			function checkForNewMessages() {
				// console.log('Checking for new messages. Receiver ID:', receiverId); // Log the receiver ID being checked
				// Collect IDs of unread messages
				var unreadMessageIds = [];

				$.ajax({
					type: 'POST',
					url: chatSettings.ajax_url,
					data: {
						action: 'check_for_new_messages', // This action should match the one registered in your PHP code.
						receiver_id: receiverId, // The ID of the receiver for whom to check new messages.
						last_message_id: getLastMessageId(), // Retrieves the ID of the last message received to fetch only new messages.
						nonce: chatSettings.nonce // Nonce for security.
					},
					success: function (response) {
						if (response.success && response.data.new_messages && response.data.new_messages.length > 0) {
							console.log('New messages found:', response.data.new_messages); // Log new messages if found.
							// New messages received, update UI
							response.data.new_messages.forEach(function (message) {
								// Append each new message to the chat window
								appendNewMessageToUI(message); // Implement this function to append new messages to the UI.
							});
						} else {
							// console.log('No new messages found.'); // Log when no new messages are found.
						}

						// Collect IDs of unread messages
						$('#chat-messages .message.unread').each(function () {
							// Get the message ID
							var messageId = $(this).data('message-id');
							// Add the message ID to the array
							unreadMessageIds.push(messageId);
						});


						// console.log('unread ids : ', unreadMessageIds);

						// Mark unread messages as read
						updateUnreadMessagesInPartnerChat(unreadMessageIds);

					},
					error: function (xhr, status, error) {
						console.error('Error checking for new messages:', error); // Log any errors encountered during the request.
					}
				});
			}

			// Function to update unread messages in the partner's chat window
			function updateUnreadMessagesInPartnerChat(unreadMessageIds) {
				if (unreadMessageIds.length === 0) {
					return; // No unread messages to update
				}

				// AJAX request to update unread messages
				$.ajax({
					type: 'POST',
					url: chatSettings.ajax_url,
					data: {
						action: 'update_unread_messages_in_partner_chat', // Action to update unread messages in partner's chat window
						unread_message_ids: unreadMessageIds, // IDs of unread messages
						nonce: chatSettings.nonce // Nonce for security
					},
					success: function (response) {
						// console.log('Unread messages in partner chat updated:', response);
						// Check if read_message_ids exist in the response
						if (response.data && response.data.read_message_ids) {
							// Update UI to mark messages as read
							response.data.read_message_ids.forEach(function (messageId) {
								var messageElement = $('.message[data-message-id="' + messageId + '"]');
								messageElement.removeClass('unread').addClass('read');
								messageElement.find('.read-status').remove();
							});
						}
					},
					error: function (xhr, status, error) {
						console.error('Error updating unread messages in partner chat:', error);
					}
				});
			}

			// Function to get the ID of the last message received
			function getLastMessageId() {
				// This is a placeholder. Implement the actual logic to retrieve the last message ID from the UI.
				var lastMessage = $('#chat-messages .message:last-child');
				return lastMessage.data('message-id') || 0;
			}

			// Function to append a new message to the chat UI
			function appendNewMessageToUI(message) {
				const chatMessagesContainer = $('#chat-messages');

				// Log the message object to check its properties
				// console.log("Message object:", message);

				const messageHTML = createMessageHTML(message);

				// Log the generated message HTML to check its content
				// console.log("Generated message HTML:", messageHTML);

				// Append the new message
				chatMessagesContainer.append(messageHTML);

				// Optionally, scroll to the bottom of the chat container
				chatMessagesContainer.scrollTop(chatMessagesContainer.prop("scrollHeight"));
			}

			// Function to retrieve messages
			function getChatMessages(msgAmount, callback) {
				console.log("------ Retrieving Chat Messages ------");
				console.log("Sender ID: " + senderId);
				console.log("Receiver ID: " + receiverId);
				//console.log("Is First Page Load: " + isFirstPageLoad);
				//console.log("Has User Attempted To Load More: " + hasUserAttemptedToLoadMore);


				// AJAX call to fetch messages from the server
				$.ajax({
					type: 'POST',
					url: chatSettings.ajax_url,
					data: {
						action: 'get_chat_messages',
						msgAmount: msgAmount, // Send message count to retrieve
						receiver_id: receiverId, // Send receiver ID to retrieve messages with
						sender_id: senderId, // Send sender ID to retrieve messages with
						nonce: chatSettings.nonce
					},
					success: function (response) {
						// console.log("------ Response from Server ------");
						// Make sure to check for the 'success' status in the response
						if (response.success && Array.isArray(response.data.messages) && response.data.messages.length > 0) {
							// console.log("Messages Received: " + response.data.messages.length);
							updateChatMessages(response.data.messages);
							// displaychatOwnerNotice Only for the 1st loading.
							if (hasUserAttemptedToLoadMore) {
								isFirstPageLoad = false;
							}
							if (isFirstPageLoad) {
								displaychatOwnerNotice();
							}
						} else {
							console.log('No messages retrieved. Never Talked Before!');
							displayGreetingMsg();
						}

						// Execute the callback function if it's provided
						if (typeof callback === 'function') {
							callback(response.data.messages, response.data.hasMoreData);
						}
					},
					error: function (xhr, status, error) {
						console.error('Error retrieving messages:', error);
						// Execute the callback function with the error if it's provided
						if (typeof callback === 'function') {
							callback(error);
						}
					}
				});
			}

			// Display Greeding Message
			function displayGreetingMsg() {
				$.ajax({
					url: chatSettings.ajax_url,
					type: 'POST',
					data: {
						action: 'get_greeting_message',
						receiver_id: receiverId,
						nonce: chatSettings.nonce
					},
					success: function (response) {
						if (response.success) {
							console.log(response);
							const chatGreetingMsg = `
                            <div class="message d-flex gap-2 justify-content-start ps-3 mt-3 pe-3 read">
                                <div class="d-flex flex-row-reverse gap-2">
                                    <div class="message-details d-flex flex-column justify-content-end mb-1">
                                        <div class="message-content position-relative">
                                            <div class="content py-3 px-3 rounded shadow-sm hstack gap-2">
                                                <i class="feather feather-message-square"></i>${response.data.greeting_message}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
							$("#chat-messages").prepend(chatGreetingMsg);
						}
					}
				});
			}


			// Function to display "No more data" message at the top of chat messages
			function displayNoMoreDataAtTop() {
				var noMoreDataMessage = $("#no-more-data-top");
				if (noMoreDataMessage.length === 0) {
					// Create and prepend the "No more data" message if it doesn't exist
					noMoreDataMessage = $('<div id="no-more-data-top" class="text-center my-2">No more data</div>');
					$("#chat-messages").prepend(noMoreDataMessage);
				}
			}

			// Function to hide "No more data" message
			function hideNoMoreDataAtTop() {
				$("#no-more-data-top").remove(); // Remove the message if it exists
			}

			// All message loaded
			var allMessagesLoaded = false;
			var hasUserAttemptedToLoadMore = false;

			// Function to load previous messages
			function loadPreviousMessages() {

				if (allMessagesLoaded) {
					return; // If all messages are loaded, do nothing
				}
				hasUserAttemptedToLoadMore = true;

				// Show loading message
				showLoadingMessage();

				var chatMessagesContainer = $('#chat-messages');
				var oldScrollHeight = chatMessagesContainer[0].scrollHeight; // 스크롤 높이를 불러온 메시지 처리 전에 기록합니다.

				loadMsgAmount += readPrevMsgAmount;

				// Retrieve additional messages
				getChatMessages(loadMsgAmount, function (messages, hasMoreData) {
					if (!hasMoreData && hasUserAttemptedToLoadMore) {
						allMessagesLoaded = true; // No more data to load
						displayNoMoreDataAtTop();
					}

					// Hide loading message
					hideLoadingMessage();

					// Adjust scroll position to maintain the view
					var newScrollHeight = chatMessagesContainer[0].scrollHeight;
					var scrollDifference = newScrollHeight - oldScrollHeight; // 새로 불러온 메시지들의 높이를 계산합니다.
					chatMessagesContainer.scrollTop(chatMessagesContainer.scrollTop() + scrollDifference); // 스크롤 위치를 조정하여 뷰를 유지합니다.
				});
			}


			// Event handler for the 'load previous' button
			$('#load-previous').on('click', function () {
				loadPreviousMessages();
			});

			// Throttling function to limit the rate of function execution
			function throttle(func, limit) {
				let inThrottle;
				return function () {
					const args = arguments;
					const context = this;
					if (!inThrottle) {
						func.apply(context, args);
						inThrottle = true;
						setTimeout(() => inThrottle = false, limit);
					}
				};
			}

			// Scroll event handler
			$('#chat-messages').on('scroll', function () {
				console.log("isStartingChat : " + isStartingChat);
				if (isStartingChat) return;
				// Get the current scroll position
				var scrollTop = $(this).scrollTop();
				// Define the threshold for triggering the loading
				var scrollThreshold = 5; // Adjust this value as needed

				// Check if the scroll position is at the top or near the top
				if (scrollTop <= scrollThreshold && !allMessagesLoaded) {
					console.log("------ 2 SCROOOOLLLLLL Load More!!!! ------");
					// Delay the execution of loading function by 0.5 seconds
					setTimeout(function () {
						loadPreviousMessages();
					}, 1000);
				}
			});



			// Function to show loading message (Implement as needed)
			function showLoadingMessage() {
				$('#loading-message').show();
			}

			// Function to hide loading message (Implement as needed)
			function hideLoadingMessage() {
				$('#loading-message').hide();
			}

			// Event handler for send button click to clear interval
			$('#send-button').on('click', function () {
				//clearInterval(intervalId); // Clear the interval
				stopCheckingForMessages();

				sendMessage(); // Call sendMessage function
				// Set a new interval after sending a message
				intervalId = setInterval(function () {
					getChatMessages(loadMsgAmount); // Retrieve messages using loadMsgAmount
				}, sendMessageInterval);
			});

			// Event handler for Enter key press in message input field
			$('#message-input').keypress(function (event) {
				if (event.which == 13) { // Check if Enter key is pressed
					event.preventDefault(); // Prevent default action (form submission)
					hideTypingIndicator();
					sendMessage(); // Call sendMessage function
				}
			});

			// Scroll to bottom function with animation
			function scrollToBottom() {
				var chatMessagesContainer = $('#chat-messages');
				var scrollHeight = chatMessagesContainer.prop("scrollHeight");
				chatMessagesContainer.animate({ scrollTop: scrollHeight }, 0); // Adjust animation duration as needed
			}

			// Go to Bottom Button
			var chatMessagesContainer = $('#chat-messages');
			var scrollToBottomButton = $('#scrollToBottomButton');

			chatMessagesContainer.on('scroll', function () {
				var isCloseToBottom = (this.scrollHeight - this.scrollTop - this.clientHeight) < 140;
				if (!isCloseToBottom) {
					scrollToBottomButton.fadeIn();
				} else {
					scrollToBottomButton.fadeOut();
				}
			});

			scrollToBottomButton.on('click', function () {
				chatMessagesContainer.animate({ scrollTop: chatMessagesContainer.prop("scrollHeight") }, 'slow');
			});


			function sendMessage(mediaId = null) {
				var message = $('#message-input').val(); // Get message from input field

				senderId = $('#javo-interface-inner').attr('data-sender-id');
				console.log('sendmsg-senderId :', senderId);

				// If senderId is not set or empty, try to get it from the cookie
				if (!senderId) {
					senderId = getCookie('visitor_email');
					console.log('sendmsg-senderId from cookie:', senderId);
				}

				var data = {
					action: 'send_message',
					sender_id: senderId,
					receiver_id: receiverId,
					message: message,
					nonce: chatSettings.nonce
				};

				// Add image id if selected
				if (mediaId) {
					data.media_id = mediaId;
					console.log('imgID', mediaId);
				}

				// Check if message is not empty or an image is selected
				if (message.trim() !== '' || mediaId) {
					hideTypingIndicator();
					// AJAX call to send the message and image ID to the server
					$.ajax({
						type: 'POST',
						url: chatSettings.ajax_url,
						data: data,
						beforeSend: function () {
							// Show spinner and disable button
							$('.btn-txt').hide();
							$('#send-button').prop('disabled', true).find('.spinner-grow').removeClass('d-none');
						},
						success: function (response) {
							// Handle success response
							$('#message-input').val('').focus(); // Clear input field and set focus
							scrollToBottom(); // Scroll to bottom
							getChatMessages(loadMsgAmount); // Retrieve messages immediately after sending
							hideTypingIndicator(); // Hide typing indicator explicitly
							mediaId = null; // Reset selected image ID
						},
						error: function (error) {
							// Handle error
							console.error('Error sending message:', error);
						},
						complete: function () {
							// Hide spinner and enable button
							$('.btn-txt').show();
							$('#send-button').prop('disabled', false).find('.spinner-grow').addClass('d-none');
						}
					});
				} else {
					// Display error message if message and image are empty
					console.error('Error: Message and Image are empty');
				}
			}

			var isStartingChat = false;
			// Event delegation for 'start-chat' click events
			$(document).on('click', '.start-chat', function () {
				isStartingChat = true;

				console.log('start-chat button');
				receiverId = $(this).data('receiver-id'); // 클릭된 요소의 receiverId를 전역 변수에 할당합니다.
				hasUserAttemptedToLoadMore = false;
				isFirstPageLoad = true;
				if (isFirstPageLoad) {
					hideNoMoreDataAtTop();
				}
				console.log('isFirstPageLoad:' + isFirstPageLoad);
				console.log('hasUserAttemptedToLoadMore:' + hasUserAttemptedToLoadMore);

				initializeChatInterface(receiverId, function () {
					scrollToBottom();
				});

				var messageId = $(this).data('message-id'); // Extract message ID
				if (messageId) {
					console.log('message position');
					calculateLoadMsgAmountAndScrollToMessage(messageId, receiverId); // Call new function
				} else {
					console.log('receiverId :', receiverId);
					console.log('initMsgAmount :', initMsgAmount);
					// Existing logic to retrieve and display messages
					loadMsgAmount = initMsgAmount; // Reset the load msg amount if it's not the 1st click
					getChatMessages(loadMsgAmount, function () {
						scrollToBottom(function () {
							//isStartingChat = false;
						});
						isStartingChat = false;
					});
					scrollToBottom();
					intervalId = setInterval(checkForNewMessages, 5000);
				}
			});


			// Use event delegation to handle clicks on dynamically added elements
			function initializeChatInterface(receiverId) {
				$('#back-to-chat').trigger('click'); // Clicking on #back-to-chat

				stopCheckingForMessages(); // Stop Interval
				allMessagesLoaded = false;
				hasUserAttemptedToLoadMore = false;
				isFirstPageLoad = true;
				if (isFirstPageLoad) {
					hideNoMoreDataAtTop();
				}

				// var chatMessagesContainer = $('#chat-messages');
				// chatMessagesContainer.empty();

				$('#message-input').focus();

				// Find and activate the clicked element
				var $clickedElement = $('.start-chat[data-receiver-id="' + receiverId + '"]');
				$('.start-chat').removeClass('active');
				$clickedElement.addClass('active');

				// Show chat-input when any user is clicked to start chatting
				$('.chat-input').addClass('d-flex').show();
				$('#admin-notice').addClass('d-flex flex-column').show();

				// Use the receiverId directly instead of trying to extract it again
				// Get other necessary data from the clicked chat item
				var displayName = $clickedElement.data('name');
				var avatarUrl = $clickedElement.data('avatar-url');
				var unreadMessagesCount = $clickedElement.data('unread-messages-count');
				var userStatus = $clickedElement.data('user-status');
				var userLastActive = $clickedElement.data('user-last-active');

				// Check if the receiver ID is not 0
				if (receiverId !== 0) {
					// Check if any of the necessary data is missing
					if (!displayName || !avatarUrl || !unreadMessagesCount || !userStatus || !userLastActive) {
						// Call updateParticipantPanelWithAjax function if any data is missing
						updateParticipantPanelWithAjax(receiverId);
					} else {
						// Update the chat window with the selected conversation partner's information
						updateParticipantPanel(receiverId, displayName, avatarUrl, unreadMessagesCount, userStatus, userLastActive);
					}
				} else {
					console.log('visiter');
				}

				// Show load previous button and message input form if receiverId is defined
				if (typeof receiverId !== 'undefined') {
					$('#load-previous, #message-input, #send-button').show();
				} else {
					$('#load-previous, #message-input, #send-button').hide();
				}

			};

			function updateParticipantPanel(receiverId, displayName, avatarUrl, unreadMessagesCount, userStatus, userLastActive, favoriteUser, blockedUser) {
				$('#participant-panel').html(`
                <div class="profile-info d-flex gap-3">
                    <div class="avatar">
                        <img src="${avatarUrl}" class="avatar rounded-circle" alt="${displayName}">
                    </div>
                    <div class="info">
                        <span class="name fw-semibold text-capitalize">${displayName} ( #${receiverId} )</span>
                        <div class="d-flex gap-3">
                            <span class="user-status fs-6">${userStatus}</span>
                            <span class="user-last-active fs-6">${userLastActive}</span>
                        </div>
                    </div>
                </div>
                <div class="functionality-buttons d-flex align-items-center gap-2">
                    <a href="#" class="favorite-btn" data-receiver-id="${receiverId}">
                        <i class="feather feather-heart fs-5 ${favoriteUser ? 'text-danger' : ''}"></i>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </a>
                    <a href="#" class="block-user-btn" data-receiver-id="${receiverId}">
                        <i class="feather feather-slash fs-5 ${blockedUser ? 'text-danger' : ''}"></i>
                    </a>
                    <div class="profile-opener mt-1">
                        <a href="#" data-bs-target="#profile-sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 d-flex align-items-center">
                            <i class="feather feather-chevron-right"></i>
                            <i class="feather feather-chevron-left"></i>
                        </a>
                    </div>
                </div>
            `);
			}

			// Function to update the participant panel with selected conversation partner's information
			function updateParticipantDetailPanel(receiverId, displayName, avatarUrl, unreadMessagesCount, userStatus, userLastActive, favoriteUser, blockedUser) {
				$('#participant-detail-panel').html(`
                <div class="profile-info d-flex flex-column align-items-center gap-3">
                    <div class="user-avatar position-relative">
                        <img src="${avatarUrl}" class="avatar rounded-circle" alt="${displayName}">
                        <span class="status-dot"></span>
                    </div>
                    <div class="info d-flex flex-column align-items-center justify-content-center">
                        <span class="name fw-semibold text-capitalize">${displayName} ( #${receiverId} )</span>
                        <div class="d-flex gap-3">
                            <span class="user-last-active fs-6">${userLastActive}</span>
                        </div>
                    </div>
                </div>
                <div class="functionality-buttons">
                    <a href="#" class="favorite-btn data-receiver-id="${receiverId}">
                        <i class="feather feather-heart fs-5 ${favoriteUser ? 'text-danger' : ''}"></i>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </a>
                    <a href="#" class="block-user-btn data-receiver-id="${receiverId}">
                        <i class="feather feather-slash fs-5 ${blockedUser ? 'text-danger' : ''}"></i>
                    </a>
                </div>
            `);
			}

			function updateParticipantPanelWithAjax(receiverId) {
				$.ajax({
					type: 'POST',
					url: chatSettings.ajax_url,
					data: {
						action: 'get_chat_partner_single',
						receiverId: receiverId,
						nonce: chatSettings.nonce
					},
					success: function (response) {
						if (response.success) {
							var userData = response.data.userData;
							updateParticipantPanel(receiverId, userData.displayName, userData.avatarUrl, userData.unreadMessagesCount, userData.userStatus, userData.userLastActive, userData.favoriteUser, userData.blockedUser);
							updateParticipantDetailPanel(receiverId, userData.displayName, userData.avatarUrl, userData.unreadMessagesCount, userData.userStatus, userData.userLastActive, userData.favoriteUser, userData.blockedUser);
							// Check if the user is blocked
							if (userData.isBlocked) {
								// If blocked, disable the message input
								$('#message-input').prop('disabled', true);
								// Change the placeholder text to indicate that the user is blocked
								$('#message-input').attr('placeholder', 'You are currently blocked.');
							} else if (userData.isMyself) {
								// If chatting with oneself, disable the message input
								$('#message-input').prop('disabled', true);
								// Change the placeholder text to indicate the reason for disabled input
								$('#message-input').attr('placeholder', 'Can\'t send messages to yourself.');
								$('#chat-messages').empty(); // If chatting with oneself, empty the chat messages container
								stopCheckingForMessages(); // Stop checking new messages
							} else {
								// If not blocked or chatting with oneself, enable the message input
								$('#message-input').prop('disabled', false);
								// Restore the original placeholder text
								$('#message-input').attr('placeholder', 'Type and press Enter to send...');
							}
						} else {
							console.error('Failed to fetch user data.');
						}
					},
					error: function (xhr, status, error) {
						console.error('Ajax request failed:', status, error);
					}
				});
			}


			// Function to update chat messages UI
			function updateChatMessages(messages) {
				// Select the chat messages container element
				var chatMessagesContainer = $('#chat-messages');
				// Clear the existing messages
				chatMessagesContainer.empty();
				// console.log('empty message');

				// Initialize variables to store last displayed date and time
				var lastDisplayedDate = null;
				var lastDisplayedTime = null;

				// Reverse the order of messages and loop through them to append to the container
				messages.reverse().forEach(function (message) {
					var messageDate = new Date(message.message_time);
					var messageTime = messageDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

					// Format the message date
					var options = { year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' };
					var messageDateString = messageDate.toLocaleDateString('en-US', options);

					// Check if the message date is different from the last displayed date
					if (messageDateString !== lastDisplayedDate) {
						// Display the message date
						var messageDateHTML = '<div class="message-date my-4 w-100 d-flex justify-content-center"><div class="badge chat-time-line">' + messageDateString + '</div></div>';
						chatMessagesContainer.append(messageDateHTML);
						// Update the last displayed date
						lastDisplayedDate = messageDateString;
						// Reset the last displayed time
						lastDisplayedTime = null;
					}

					// Check if the message time is different from the last displayed time
					if (messageTime !== lastDisplayedTime) {
						// Display the message time
						var messageTimeHTML = '<div class="message-time text-gray">' + messageTime + '</div>';
						//chatMessagesContainer.append(messageTimeHTML);
						// Update the last displayed time
						lastDisplayedTime = messageTime;
					}

					// Construct the message HTML with user name, avatar, time, and read status
					var messageHTML = createMessageHTML(message);

					// Append the message HTML to the container
					chatMessagesContainer.append(messageHTML);
				});

				setTimeout(function () {
					convertTextUrlsToLinks();
				}, 5); // 500ms Convert URL
			}
			// Function to create message HTML
			function createMessageHTML(message) {

				const messageId = message.message_id || message.id; // Use message_id if available, otherwise use id
				const receiverId = message.receiver_id.toString(); // Convert sender_id to string
				const senderId = message.sender_id.toString(); // Convert sender_id to string
				const message_time = message.message_time;
				const isSender = message.sender_id === chatSettings.chat_user_id; // Not only nubers

				// Get the message time
				var messageDate = new Date(message_time);
				var messageTime = messageDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

				// Construct the message HTML
				var messageHTML = '<div class="message d-flex gap-2 justify-content-' + (isSender ? 'end my-message' : 'start ps-3') + ' mt-3 pe-3';
				// Add class to indicate read or unread message
				messageHTML += message.read_status == 'read' ? ' read' : ' unread';
				messageHTML += '" data-message-id="' + messageId + '" data-receiver-id="' + receiverId + '"';
				if (isSender) { // It's mine
					messageHTML += ' data-sender-id="' + senderId + '"';
				}
				messageHTML += '>';

				if (!isSender) {
					messageHTML += '<div class="sender user-' + senderId + ' d-flex flex-column">';
					messageHTML += '<img src="' + message.avatar_url + '" alt="' + message.user_name + '" class="avatar rounded-circle">';
					messageHTML += '<span class="fs-7 d-none">' + message.user_name + '</span></div>';
				}

				messageHTML += '<div class="d-flex ' + (isSender ? '' : 'flex-row-reverse') + ' gap-2">';
				messageHTML += '<div class="message-details d-flex flex-column justify-content-end mb-1">';
				// Add read status indicator
				messageHTML += '<div class="read-status fs-7">' + (message.read_status != 'read' ? '<span class="text-danger">Unread</span>' : '') + '</div>';
				messageHTML += '<div class="message-time fs-7 lh-1">' + messageTime + '</div>';
				// Dropdown menu
				messageHTML += '<div class="dropdown chat-content-options">';
				messageHTML += '<button class="btn dropdown-toggle border-0" type="button" id="dropdownMenuButton' + messageId + '" data-bs-toggle="dropdown" aria-expanded="false">';
				messageHTML += '<i class="fas fa-ellipsis-v"></i>'; // Dropdown icon (Example using FontAwesome)
				messageHTML += '</button>';
				messageHTML += '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' + messageId + '">';
				messageHTML += '<li><a class="dropdown-item" href="#">Save Message</a></li>';
				messageHTML += '<li><a class="dropdown-item" href="#">Report Message</a></li>';
				messageHTML += '</ul>';
				messageHTML += '</div>'; // End of dropdown menu
				messageHTML += '</div>'; // Close message-details

				messageHTML += '<div class="message-content position-relative">';
				messageHTML += '<div class="content py-3 px-3 rounded shadow-sm">' + message.message + '</div>';

				messageHTML += '</div>'; // Close message-content

				messageHTML += '</div>'; // Close message-content-wrap
				messageHTML += '</div>'; // Close message

				return messageHTML;
			}

			function displaychatOwnerNotice() {
				// console.log('chat owner receiver id' + receiverId);
				$.ajax({
					url: chatSettings.ajax_url,
					type: 'POST',
					data: {
						action: 'get_chat_owner_notice',
						nonce: chatSettings.nonce,
						receiver_id: receiverId
					},
					success: function (response) {
						if (response.success) {
							// console.log(response);
							if (response.data.chat_owner_notice !== undefined) { // Check if chat_owner_notice exists in response
								// Define the message and duration
								const ownerMessage = response.data.chat_owner_notice;
								const duration = 30; // Duration in seconds for demonstration

								// Create the alert container
								const alertContainer = document.createElement('div');
								alertContainer.className = "alert bg-success p-2 text-white bg-opacity-40 alert-dismissible fade show position-sticky top-0 z-index-1 bg-opacity-90 hstack";
								alertContainer.role = "alert";
								alertContainer.innerHTML = `
                        <strong class="me-auto">${ownerMessage}</strong>
                        <span class="me-4 pe-1 fs-7">Closing in <span class="countdown">${duration}</span> sec</span>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;

								// Select the chat messages container
								const chatMessages = document.getElementById('chat-messages');

								// Prepend the alert to the chat messages container
								chatMessages.prepend(alertContainer);

								// Countdown logic
								let remainingTime = duration;
								const countdown = alertContainer.querySelector('.countdown');
								const countdownInterval = setInterval(() => {
									remainingTime -= 1;
									countdown.textContent = remainingTime;

									if (remainingTime <= 0) {
										clearInterval(countdownInterval);
										alertContainer.remove();
									}
								}, 1000);

								// Close button functionality
								const closeButton = alertContainer.querySelector('.btn-close');
								closeButton.onclick = function () {
									clearInterval(countdownInterval);
									alertContainer.remove();
								};
							}
						}
					}
				});
			}



			/*
				Read/Unread Handler
			*/
			// Variable to track whether message collection is in progress
			var collectingMessages = false;

			// Function to collect and mark unread messages of the other user as read
			function collectAndMarkUnreadMessagesAsRead() {
				// Check if message collection is already in progress
				if (collectingMessages) {
					return; // Exit if already collecting messages
				}

				// Set collectingMessages to true to prevent further collection
				collectingMessages = true;

				// Array to store IDs of unread messages of the other user
				var unreadMessageIds = [];
				var senderId;

				// Loop through each unread message of the other user in the javo-interface-inner window
				$('#javo-interface-inner .message.unread:not(.my-message)').each(function () {
					// Get the message ID
					var messageId = $(this).data('message-id');
					// Add the message ID to the array
					unreadMessageIds.push(messageId);
					// Update UI to mark message as read (optional)
					$(this).removeClass('unread').addClass('read');

					// Update UI to mark read status
					$(this).find('.read-status').html('<span class="text-success">Read</span>');

					// Get the Sender ID ( It's your message chat. That's why sender id. Not receiver id ) from the message element
					senderId = $(this).data('sender-id');
				});

				// Check if there are unread messages to mark as read
				if (unreadMessageIds.length === 0) {
					// Reset collectingMessages flag
					collectingMessages = false;
					return; // Exit if there are no unread messages
				}

				// Send unread message IDs to PHP to mark them as read
				$.ajax({
					type: 'POST',
					url: chatSettings.ajax_url,
					data: {
						action: 'mark_unread_messages_as_read',
						unread_message_ids: unreadMessageIds,
						sender_id: senderId, // To callback ( getback the total amount of this receiverID )
						nonce: chatSettings.nonce
					},
					success: function (response) {
						// Update the badge with the new unread total count for the specific receiver
						$('.start-chat[data-receiver-id="' + response.data.receiverId + '"] .unread-count').text(response.data.newunreadtotalcount);
					},
					error: function (xhr, status, error) {
						// Handle error
						console.error('Error marking unread messages of the other user as read:', error);
						// Optionally, handle the error response from the server
						console.log(xhr.responseText);
					},
					complete: function () {
						// Reset collectingMessages flag after completion
						collectingMessages = false;
					}
				});
			}

			// Event handler for scroll or click in the javo-interface-inner window
			$('#javo-interface-inner').on('scroll click', function () {
				// Collect and mark unread messages as read
				collectAndMarkUnreadMessagesAsRead();
			});

			/*
				Typing Indicator
			*/
			// Function to handle typing indicator display
			function showTypingIndicator() {
				$('#typing-indicator').css('display', 'block');
			}

			// Function to hide the typing indicator
			function hideTypingIndicator() {
				$('#typing-indicator').css('display', 'none');
			}

			// Function to handle typing status and send AJAX request
			function handleTypingStatus(isTyping) {
				// Get the typed message
				var typedMessage = $('#message-input').val().trim();

				// Send AJAX request to notify typing status
				$.ajax({
					type: 'POST',
					url: chatSettings.ajax_url,
					data: {
						action: 'handle_typing_status',
						typing: isTyping, // Indicate whether user is typing
						receiver_id: receiverId, // Receiver ID
						message: typedMessage, // Send the typed message
						nonce: chatSettings.nonce
					},
					success: function (response) {
						// Handle success response
						if (isTyping) {
							showTypingIndicator(); // Show typing indicator if typing
						} else {
							hideTypingIndicator();
						}
					},
					error: function (xhr, status, error) {
						// Handle error
						console.error('Error sending typing status:', error);
					},
					complete: function () {
						console.log('typing Completed');
						// Hide typing indicator when request is completed
						if (!isTyping) {
							hideTypingIndicator();
						}
					}
				});
			}

			// Event listener for input in the message input field
			$('#message-input').on('input', function () {
				var message = $(this).val().trim(); // Get the typed message
				if (message.length > 0) { // Check if there is any input
					//handleTypingStatus(true); // Indicate that user is typing
					//showTypingIndicator();

					$('#send-button').prop('disabled', false); // Enable send button
				} else {
					//handleTypingStatus(false); // Indicate that user is not typing
					//hideTypingIndicator();

					$('#send-button').prop('disabled', true); // Disable send button
				}
			});

			/*
				Search
			*/

			function debounce(func, wait) {
				let timeout;
				return function () {
					const context = this, args = arguments;
					clearTimeout(timeout);
					timeout = setTimeout(function () {
						timeout = null;
						func.apply(context, args);
					}, wait);
				};
			};

			// Variable to hold the timeout for search delay
			var searchTimeout;

			// Function to handle search input
			$('#search-input').on('keyup', debounce(function () {
				const query = $(this).val().trim();
				// Moe than 3 letters
				if (query.length > 2) {
					performSearch(query);
				} else {
					// 입력된 텍스트가 없으면 검색 결과를 지우고 'X' 버튼을 숨김
					$('#search-result-wrap').empty();
					$('#clear-search').hide();
					$('#chat-user-list').show();
				}
			}, 500));

			// Event handler for the 'clear search' button and icon clicks
			$('#clear-search, .chat-side a').on('click', function () {
				// Clear the search input
				$('#search-input').val('');

				// Empty the search results container
				$('#search-result-wrap').empty();

				// Show the user list
				$('#chat-user-list').show();

				// Hide the 'clear search' button if it was clicked
				$('#clear-search').hide();
			});

			// Function to perform search using AJAX
			function performSearch(query) {
				// console.log('search query:' + query);
				// Display loading spinner and hide 'X' icon
				$('#spinner').show();
				$('#clear-search').hide();

				// Perform AJAX request to retrieve search results
				$.ajax({
					url: chatSettings.ajax_url,
					type: 'POST',
					data: {
						action: 'get_search_results',
						query: query
					},
					success: function (response) {
						// Hide loading spinner
						$('#spinner').hide();
						if (response.success) {
							// Get the search results data from the response
							const searchData = response.data;

							// Call createTabs function with appropriate data
							createTabs(searchData);

							// Display 'X' icon
							$('#clear-search').show();

							// Hide the user list when search starts
							$('#chat-user-list').hide();

						} else {
							// Display error message
							$('#search-results').html('<p>Error: ' + response.data + '</p>');
						}
					},
					error: function (xhr, status, error) {
						// Handle error
						console.error('Error:', error);
						$('#search-results').html('<p>Error: ' + error + '</p>');
					}
				});
			}

			/// Function to create HTML for user search result
			function createUserSearchResultHTML(user) {
				// Default values for user properties
				const avatarUrl = user.avatar_url || 'https://www.gravatar.com/avatar/90ece45ce4ca911eaa62e984909e0946?s=96&r=g&d=mm'; // Default avatar URL if user.avatar_url is null or empty
				const unreadMessagesCount = user.unread_messages_count || 0; // Default to 0 if not provided
				const userStatus = user.status || user.online_status; // Default to 'offline' if not provided
				const lastActive = user.last_active || user.formatted_last_activity; // Default text if user.last_active is null or empty

				return `
                <a href="#" class="start-chat d-flex align-items-center gap-3" 
                    data-receiver-id="${user.user_id}" 
                    data-name="${user.user_name}" 
                    data-avatar-url="${avatarUrl}" 
                    data-unread-messages-count="${unreadMessagesCount}" 
                    data-user-status="${userStatus}" 
                    data-user-last-active="${lastActive}">
                    <div class="avatar me-2">
                        <img src="${avatarUrl}" alt="Avatar" class="rounded-circle">
                    </div>
                    <div class="info">
                        <p><strong>${user.user_name}</strong></p>
                        <p>Active: ${lastActive}</p>
                    </div>
                </a>
            `;
			}


			// Function to create HTML for message search result
			function createMessageSearchResultHTML(message) {
				const senderName = message.sender_name || 'Unknown';
				const messageContent = message.message_excerpt || 'No message content';

				return `
                <a href="#" class="start-chat d-flex gap-3 py-2 px-2 align-items-center rounded-3 saved-msg"
                    data-message-id="${message.message_id}" 
                    data-receiver-id="${message.partner_id}" 
                    data-name="${senderName}" 
                    data-submit-date="${message.submit_date}">
                    <div class="avatar me-2">
                        <div class="rounded-circle text-center" style="width: 40px; height: 40px; line-height: 40px;">
                            <i class="feather feather-message-circle"></i>
                        </div>
                    </div>
                    <div class="info d-flex flex-column gap-0 w-100">
                        <div class="p-0 d-flex justify-content-between">
                            <span class="fw-semibold">${message.partner_name}</span>
                            <span class="p-0 fs-7">${message.submit_date}</span>
                        </div>
                        <div class="p-0 last-message fs-7">${senderName}: ${messageContent}</div>
                    </div>
                </a>
            `;
			}



			// Corrected and Simplified Function to Create Tabs
			function createTabs(data) {

				const tabsContainer = document.getElementById('search-result-wrap');
				tabsContainer.innerHTML = ''; // Clear existing content

				// Generate HTML for each tab
				const tabContent = createTabContentHTML(data);

				// Calculate the counts for each tab
				const allCount = (data.user ? data.user.length : 0) + (data.message ? data.message.length : 0);
				const userCount = data.user ? data.user.length : 0;
				const messageCount = data.message ? data.message.length : 0;

				// Function to create a no result message
				function noResultMessage(tabName) {
					return `<div class="no-result">No results found for ${tabName}.</div>`;
				}

				// Check for results and create tab content or no result message
				let allHTML = allCount > 0 ? tabContent.all : noResultMessage('All');
				let userHTML = userCount > 0 ? tabContent.user : noResultMessage('Users');
				let messageHTML = messageCount > 0 ? tabContent.message : noResultMessage('Messages');

				// Create tabs HTML
				const tabsHTML = `
            <ul class="nav nav nav-pills nav-fill fs-6" id="chatSearchTabTitles" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All <span class="badge position-absolute top-0 end-0">${allCount}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab" aria-controls="user" aria-selected="false">Users <span class="badge position-absolute top-0 end-0">${userCount}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative" id="message-tab" data-bs-toggle="tab" data-bs-target="#message" type="button" role="tab" aria-controls="message" aria-selected="false">Messages <span class="badge position-absolute top-0 end-0">${messageCount}</span></button>
                </li>
            </ul>
            <div class="tab-content" id="chatSearchTabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    ${allHTML}
                </div>
                <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">
                    ${userHTML}
                </div>
                <div class="tab-pane fade" id="message" role="tabpanel" aria-labelledby="message-tab">
                    ${messageHTML}
                </div>
            </div>
            `;

				// Render tabs
				tabsContainer.innerHTML = tabsHTML;
			}

			// Adjusted Function to Create Tab Content HTML
			function createTabContentHTML(data) {
				let allHTML = '';
				let userHTML = '';
				let messageHTML = '';

				// User content
				if (data.user) {
					data.user.forEach(user => {
						const userResultHTML = createUserSearchResultHTML(user);
						allHTML += userResultHTML;
						userHTML += userResultHTML;
					});
				}

				// Message content
				if (data.message) {
					data.message.forEach(message => {
						const messageResultHTML = createMessageSearchResultHTML(message);
						allHTML += messageResultHTML;
						messageHTML += messageResultHTML;
					});
				}

				return {
					all: allHTML,
					user: userHTML,
					message: messageHTML
				};
			}

			// Online, Offline Check
			function updateLastActivity() {
				$.ajax({
					type: 'POST',
					url: chatSettings.ajax_url,
					data: {
						action: 'update_last_activity',
						nonce: chatSettings.nonce
					},
					success: function (response) {

					},
					error: function (xhr, status, error) {
						console.error('Ajax request failed:', status, error);
					}
				});
			}

			// Update
			jQuery(document).ready(function ($) {
				setInterval(updateLastActivity, activityInterval); // 5min
			});

			// Emoji
			//const button = document.querySelector("#emoji_btn");
			const button = $('#emoji_btn');
			const picker = new EmojiButton({
				position: 'bottom-start'
			});

			// Add event listener to the button to toggle the emoji picker
			button.on('click', () => {
				picker.togglePicker(button[0]); // Pass the DOM element to togglePicker
			});

			// Listen for emoji selection event and append the emoji to the message input
			picker.on('emoji', emoji => {
				const text_box = $('#message-input');
				text_box.val(text_box.val() + emoji); // Append emoji to the message input
			});

			/* 
			User Favorite
			*/

			// Click event for Favorite Button
			$(document).on('click', '.favorite-btn', function (e) {
				e.preventDefault();
				const receiverId = $(this).data('receiver-id');
				const spinner = $(this).find('.spinner-border');
				const heartIcon = $(this).find('.feather-heart');
				const sidebarIcon = $('.chat-side .view-favorite-users'); //Sidebar icon
				spinner.removeClass('d-none'); // Show spinner

				// AJAX request to toggle favorite status
				$.ajax({
					url: chatSettings.ajax_url,
					type: 'POST',
					data: {
						action: 'toggle_favorite_users',
						receiver_id: receiverId,
						nonce: chatSettings.nonce
					},
					success: function (response) {
						if (response.data.is_favorite) {
							heartIcon.addClass('text-danger'); // Highlight heart icon
							sidebarIcon.addClass('text-danger shake'); // Add red color to sidebar icon and shake animation
							showToast('Added to favorites'); // Show toast message
							setTimeout(function () {
								sidebarIcon.removeClass('shake'); // Remove shake animation after 2 seconds
							}, 2000);
						} else {
							heartIcon.removeClass('text-danger'); // Un-highlight heart icon
							// Revert the effect on the sidebar icon
							showToast('Removed from favorites'); // Show toast message
						}
						loadChatPartners('favorites');
					},
					error: function () {
						showToast('An error occurred');
					},
					complete: function () {
						spinner.addClass('d-none'); // Hide spinner
					}
				});
			});

			// Function to show toast messages
			function showToast(message) {
				// Create unique ID for each toast
				const toastId = 'toast_' + Math.random().toString(36).substring(2, 15);

				// Create toast element
				const toast = `
                <div id="${toastId}" class="toast align-items-center text-white text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header text-white text-bg-dark d-flex align-items-center">
                        <i class="feather feather-bell me-3"></i>
                        <strong class="me-auto">Notification</strong>
                        <small class="text-body-secondary">just now</small>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                        ${message}
                        </div>
                    </div>
                </div>
            `;

				// Append toast to container
				$('#chatToastContainer').append(toast);

				// Show the toast
				$(`#${toastId}`).toast('show');

				// Remove toast after it's hidden
				$(`#${toastId}`).on('hidden.bs.toast', function () {
					$(this).remove();
				});
			}


			/**
			* Load All Chat Users.
		   */
			loadChatPartners('all');

			// Click event listener for all list
			$('.view-all-users').on('click', function () {
				// Trigger the loading of all chat users when the button is clicked
				loadChatPartners('all');
			});




			/**
			 * Function to update the chat partners list title.
			 */
			function updateChatPartnersTitle(context) {
				let titleMap = {
					'all': 'All Chat Users',
					'favorites': 'Favorite Chat Users',
					'blocked': 'Blocked Users',
					'messages': 'Saved Messages'
				};

				// Default to 'All Chat Users' if context is not recognized
				let title = titleMap[context] || 'All Chat Users';
				$('#chat-partners-title').text(title);
			}

			function loadChatPartners(context = 'all') {
				updateChatPartnersTitle(context);

				// Show skeleton loading
				$('#chat-partner-list').html(getSkeletonHTML());

				// Adjust the data sent in the AJAX request based on the context
				let ajaxData = {
					action: 'get_chat_partners',
					nonce: chatSettings.nonce
				};

				if (context === 'favorites') {
					ajaxData.load_favorites = true;
				} else if (context === 'blocked') {
					ajaxData.load_blocked = true;
				}

				$.ajax({
					url: chatSettings.ajax_url,
					type: 'POST',
					data: ajaxData,
					success: function (response) {
						// console.log(response);
						var partnersHtml = '';
						// Check if the response is successful and the userData array exists
						// if (response.success && response.data.userData && Array.isArray(response.data.userData)) {
						if (response.success && response.data.userData !== undefined && response.data.userData.length > 0) {

							// console.log('success and found data');
							// console.log(response.data);
							// Iterate through each partner in the userData array
							response.data.userData.forEach(function (partner) {
								// Adjust for the new field names (camelCase)
								var avatarUrl = partner.avatarUrl.startsWith('http') ? partner.avatarUrl : 'https:' + partner.avatarUrl;
								partnersHtml += constructPartnerHtml(partner, avatarUrl);
							});
							$('#chat-partner-list').html(partnersHtml);
						} else {
							// Display different message based on the context
							let errorMessage = '';
							switch (context) {
								case 'favorites':
									errorMessage = 'No favorite chat users found.<br><a href="#" class="view-all-users pe-auto">Go back to All list</a>';
									break;
								case 'blocked':
									errorMessage = 'No blocked users found.<br><a href="#" class="view-all-users pe-auto">Go back to All list</a>';
									break;
								default:
									errorMessage = 'No chat users found.<br><button class="search-users btn btn-primary text-white">Search for users</button>';
									break;
							}
							$('#chat-partner-list').html('<li class="text-center pt-5">' + errorMessage + '</li>');
						}
					},
					error: function (xhr, status, error) {
						console.error("Failed to load chat partners:", error);
						$('#chat-partner-list').html('<li>Error loading partners.</li>');
					}
				});
				// Bind click events for dynamic elements
				$('#chat-partner-list').on('click', '.view-all-users', function () {
					loadChatPartners('all');
				});

				// Bind click event for search button
				$('#chat-partner-list').on('click', '.search-users', function () {
					// Move focus to search input
					$('#search-input').focus();
				});

			}

			// Utility function to construct HTML for each chat partner
			function constructPartnerHtml(partner, avatarUrl) {
				return `
        <li class="m-0">
            <a href="#" class="start-chat d-flex gap-3 py-3 px-3 align-items-center rounded-3"
                data-receiver-id="${partner.ID}" 
                data-name="${partner.displayName}" 
                data-avatar-url="${avatarUrl}" 
                data-user-status="${partner.userStatus}" 
                data-user-last-active="${partner.userLastActive}">
                <div class="user-avatar position-relative">
                    <img src="${avatarUrl}" class="avatar rounded-circle" alt="${partner.displayName}">
                    <span class="status-dot" style="background-color: ${partner.userStatus === 'online' ? '#28a745' : '#ccc'};"></span>
                </div>
                <div class="user-info d-flex flex-grow-1 flex-column">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-capitalize">${partner.displayName}</span>
                        <span class="text-muted fs-7">${partner.userLastActive}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="last-message fs-7 lh-1 text-truncate">${partner.lastMessage}</span>
                        <span class="badge bg-danger unread-count d-flex align-items-center justify-content-center rounded-circle${partner.unreadMessagesCount > 0 ? '' : ' d-none'}">${partner.unreadMessagesCount}</span>
                    </div>
                </div>
            </a>
        </li>
    `;
			}



			// Utility function to generate skeleton HTML
			function getSkeletonHTML() {
				return `
                <li class="m-0">
                    <div class="start-chat d-flex gap-3 py-2 align-items-center">
                        <div class="user-avatar position-relative skeleton-avatar m-0 p-0">
                        </div>
                        <div class="d-flex flex-column w-100 gap-1">
                            <div class="d-flex justify-content-between gap-5">
                                <span class="fw-semibold text-capitalize skeleton-text"></span>
                                <span class="text-muted fs-7 skeleton-text"></span>
                            </div>
                            <div class="d-flex justify-content-between gap-5">
                                <span class="last-message fs-6 skeleton-text"></span>
                                <span class="badge unread-count d-flex align-items-center justify-content-center rounded-circle">1</span>
                            </div>
                        </div>
                    </div>
                </li>
            `.repeat(5); // Repeat skeleton HTML for 5 partners
			}



			// Set up triggers for loading different chat partner contexts
			$('.view-all-users').click(function () {
				loadChatPartners('all');
			});

			$('.view-favorite-users').click(function () {
				loadChatPartners('favorites');
			});

			// Placeholder for future functionality
			$('.view-blocked-users').click(function () {
				loadChatPartners('blocked');
			});

			/* 
			 Block Favorite
		   */
			// Click event for Block User Button
			$(document).on('click', '.block-user-btn', function (e) {
				e.preventDefault(); // Prevent default action of the button
				const receiverId = $(this).data('receiver-id'); // Get the receiver ID to block/unblock, assuming data-receiver-id attribute is set correctly
				const spinner = $(this).find('.spinner-border'); // Find spinner element
				const blockIcon = $(this).find('.feather-lock'); // Find the block icon element
				spinner.removeClass('d-none'); // Show the spinner

				// AJAX request to toggle block status
				$.ajax({
					url: chatSettings.ajax_url, // URL to WordPress AJAX handling
					type: 'POST', // Using POST method for the request
					data: {
						action: 'toggle_block_user', // Action to perform in the WordPress backend
						receiver_id: receiverId, // Use receiver_id instead of user_id
						nonce: chatSettings.nonce // Security nonce
					},
					success: function (response) {
						if (response.data.is_blocked) {
							blockIcon.addClass('text-danger'); // Highlight block icon if user is blocked
							showToast('User blocked'); // Show toast message for blocking
						} else {
							blockIcon.removeClass('text-danger'); // Remove highlight if user is unblocked
							showToast('User unblocked'); // Show toast message for unblocking
						}
						loadChatPartners('blocked');
					},
					error: function () {
						showToast('An error occurred'); // Show error message
					},
					complete: function () {
						spinner.addClass('d-none'); // Hide the spinner after AJAX call is complete
					}
				});
			});

			/*
			Chat Action History Page
			*/
			$(document).ready(function () {
				$('.view-chat-action-history').on('click', function () {
					// Show skeleton loading
					$('#chat-partner-list').html(getActionHistorySkeletonHTML());

					$.ajax({
						url: chatSettings.ajax_url,
						type: 'POST',
						data: {
							action: 'load_action_history',
							nonce: chatSettings.nonce
						},
						success: function (response) {
							console.log('history haha: ', response.data);
							if (response.success) {
								// Assuming `response.data` contains an array of chat history entries
								var historyHtml = '';
								response.data.forEach(function (history) {
									historyHtml += constructActionHistoryHtml(history);
								});
								$('#chat-partner-list').html(historyHtml);
							} else {
								$('#chat-partner-list').html('<div>No history found.</div>');
							}
						},
						error: function () {
							$('#chat-partner-list').html('<div>Error loading history.</div>');
						}
					});
				});
			});

			// Utility function to construct HTML for each chat history entry
			function constructActionHistoryHtml(history) {
				let actionDescription;
				switch (history.action_type) {
					case "add_favorite":
						actionDescription = `added ${history.targetDisplayName} to favorites.`;
						break;
					case "remove_favorite":
						actionDescription = `removed ${history.targetDisplayName} from favorites.`;
						break;
					case "add_block":
						actionDescription = `added ${history.targetDisplayName} to block list.`;
						break;
					case "removed_block":
						actionDescription = `removed ${history.targetDisplayName} from block list.`;
						break;
					case "add_favorite_msg":
						actionDescription = `added ${history.targetDisplayName} to saved msg.`;
						break;
					case "removed_favorite_msg":
						actionDescription = `removed ${history.targetDisplayName} from saved msg.`;
						break;
					default:
						actionDescription = `performed an unknown action.`;
				}

				return `
                <div class="chat-history-entry">
                    <div class="fs-6 text-capitalize">${actionDescription}</div>
                    <div class="fs-7">${history.created_at}</div>
                    <hr>
                </div>
            `;
			}


			function getActionHistorySkeletonHTML() {
				let skeletonHTML = '';
				for (let i = 0; i < 5; i++) {
					skeletonHTML += `
                    <div class="chat-history-skeleton">
                        <div class="skeleton-content">
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line short"></div>
                        </div>
                    </div>
                `;
				}
				return skeletonHTML;
			}

			/*
			Setting Page
			*/
			// Variable to track the state of settings
			let settingsOpen = false;

			// Click handler for the 'Settings' button
			$('.settings-button').click(function () {
				// Check the current state and toggle accordingly
				if (settingsOpen) {
					// If already open, hide the settings and show the chat interface
					$('#chat-interface').show();
					$('#chat-settings-page').hide();
					settingsOpen = false; // Update the state to closed
				} else {
					// If closed, hide the chat interface and show the settings page
					$('#chat-interface').hide();
					$('#chat-settings-page').show();
					settingsOpen = true; // Update the state to open
				}
			});

			// Function to handle 'Back to Chat' button click
			$('#back-to-chat').click(function () {
				// Hide the settings page and show the chat interface
				$('#chat-settings-page').hide();
				$('#chat-interface').show();
			});

			// Function to handle changes in settings and save them
			$('.setting-item input, .setting-item select, #chat-owner-notice, #greeting-message').change(function () {
				saveChatSettings();
			});

			// Function to handle changing the avatar
			$('#change-avatar').click(function () {
				if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
					// Open WordPress media uploader modal
					var frame = wp.media({
						title: 'Select Avatar',
						multiple: false,
						library: { type: 'image' },
						button: { text: 'Select' }
					});

					frame.on('select', function () {
						var attachment = frame.state().get('selection').first().toJSON();
						var attachmentId = attachment.id;
						$('#avatar-attachment-id').val(attachmentId); // Set the selected avatar attachment ID in a hidden input
						saveChatSettings(); // Save the avatar ID immediately after selection
					});

					frame.open();
				} else {
					console.error('WordPress media editor is not available.');
				}
			});

			// Function to save chat settings and chat owner message to user meta using AJAX
			function saveChatSettings() {
				// Gather selected options
				var emailNotifUnread = $('#email-notif-unread').val();
				var emailNotifNewChat = $('#email-notif-new-chat').prop('checked');
				var emailNotifOfflineChat = $('#email-notif-offline-chat').prop('checked');
				var soundNotification = $('#sound-notification').prop('checked');
				var messagePreview = $('#message-preview').prop('checked');
				var autoReply = $('#auto-reply').prop('checked');
				var chatTheme = $('#chat-theme').val();
				var newChatTime = $('#new-chat-time').val();
				var avatarAttachmentId = $('#avatar-attachment-id').val(); // Get the selected avatar attachment ID
				var chatOwnerNotice = $('#chat-owner-notice').val(); // Get the chat owner message
				var greetingMessage = $('#greeting-message').val(); // Get the chat owner message

				// Send data to server for storage using AJAX
				$.ajax({
					url: chatSettings.ajax_url,
					type: 'POST',
					data: {
						action: 'save_chat_settings', // Action to perform in the WordPress backend
						nonce: chatSettings.nonce, // Security nonce
						emailNotifUnread: emailNotifUnread,
						emailNotifNewChat: emailNotifNewChat ? 'on' : 'off',
						emailNotifOfflineChat: emailNotifOfflineChat ? 'on' : 'off',
						soundNotification: soundNotification ? 'on' : 'off',
						messagePreview: messagePreview ? 'on' : 'off',
						autoReply: autoReply ? 'on' : 'off',
						chatTheme: chatTheme,
						newChatTime: newChatTime,
						avatarAttachmentId: avatarAttachmentId,
						chatOwnerNotice: chatOwnerNotice,
						greetingMessage: greetingMessage
					},
					success: function (response) {
						// Check if avatar URL is provided in the response
						if (response.success && response.data.avatar_url) {
							// Update avatar image with the new URL
							$('.my-avatar').attr('src', response.data.avatar_url);
						}
						console.log(response.data.message);
						showToast('Settings have been changed!'); // Show toast message
					},
					error: function (xhr, status, error) {
						console.error('Error saving chat settings:', error);
					}
				});
			}

			// Function to update the character count
			function updateCharacterCount(textarea, countElementId, minLength, maxLength) {
				var countElement = document.getElementById(countElementId);
				var textLength = textarea.value.length;
				countElement.textContent = `${textLength}/${maxLength}`;

				// Update the color based on the text length
				if (textLength >= minLength && textLength <= maxLength) {
					countElement.classList.remove('text-danger');
					countElement.classList.add('text-success');
				} else {
					countElement.classList.remove('text-success');
					countElement.classList.add('text-danger');
				}
			}

			// Assign event listeners for each textarea
			var greetingMessageTextarea = document.getElementById('greeting-message');
			var chatOwnerNoticeTextarea = document.getElementById('chat-owner-notice');

			greetingMessageTextarea.addEventListener('input', function () {
				updateCharacterCount(this, 'greeting-message-count', 5, 50);
			});

			chatOwnerNoticeTextarea.addEventListener('input', function () {
				updateCharacterCount(this, 'chat-owner-notice-count', 5, 50);
			});

			// Initialize character counts on page load
			updateCharacterCount(greetingMessageTextarea, 'greeting-message-count', 5, 50);
			updateCharacterCount(chatOwnerNoticeTextarea, 'chat-owner-notice-count', 5, 50);

			// Function to update the unread messages count in the UI
			function updateUnreadCountInUI(receiverId, unreadCount) {
				// Select the element with the unread count badge
				var unreadBadge = $(`#chat-partner-list a[data-receiver-id="${receiverId}"] .unread-count`);
				// Check if the unread count is greater than zero
				if (unreadCount > 0) {
					// Update the text content of the badge with the unread count
					unreadBadge.text(unreadCount);
					// Show the badge
					unreadBadge.removeClass('d-none');
				} else {
					// Update the text content of the badge with the unread count
					unreadBadge.text(unreadCount);
					// Hide the badge if the unread count is zero
					unreadBadge.addClass('d-none');
				}
			}


			// Function to handle AJAX request and update UI
			function updateUnreadCount(receiverId) {
				// Send AJAX request to get unread count
				$.ajax({
					url: chatSettings.ajax_url,
					type: 'POST',
					data: {
						action: 'get_unread_count',
						receiverId: receiverId,
						nonce: chatSettings.nonce
					},
					success: function (response) {
						// Check if AJAX request was successful
						if (response.success) {
							// Get the unread messages count from the response data
							var unreadCount = response.data.unreadCount;
							// Update the unread messages count in the UI
							updateUnreadCountInUI(receiverId, unreadCount);
						} else {
							// Log an error message if the AJAX request failed
							console.error('Failed to fetch unread message count.');
						}
					},
					error: function (xhr, status, error) {
						// Log an error message if the AJAX request encountered an error
						console.error('AJAX request failed:', status, error);
					}
				});
			}

			// Periodically update unread count
			setInterval(function () {
				// Iterate over each chat partner element
				$('#chat-partner-list a').each(function () {
					// Get the receiver ID from data attribute
					var receiverId = $(this).data('receiver-id');
					// Update the unread count for the chat partner
					updateUnreadCount(receiverId);
				});
			}, 5000); // Update every 5 seconds


			// Handling click events on dropdown menu items dynamically added to the messages
			$(document).on('click', '.chat-content-options .dropdown-menu a.dropdown-item', function (e) {
				e.preventDefault(); // Prevent the default anchor action

				console.log('ok');

				// Getting the text of the clicked dropdown menu item
				var menuItemText = $(this).text();

				// Retrieving the message ID from the closest message container
				var messageId = $(this).closest('.message').data('message-id');

				// Perform different actions based on the clicked menu item
				switch (menuItemText) {
					case 'Save Message':
						// Logic to save the message
						console.log('Saving message with ID:', messageId);
						saveFavoriteMessage(messageId);
						// Implement the save message functionality here
						break;
					case 'Report Message':
						// Logic to report the message
						console.log('Reporting message with ID:', messageId);
						// Implement the report message functionality here
						break;
					default:
						console.log('Unknown action');
				}
			});

			// Function to handle saving a message
			function saveFavoriteMessage(messageId) {
				// AJAX request to save the message
				$.ajax({
					url: chatSettings.ajax_url, // WordPress AJAX URL
					type: 'POST',
					data: {
						action: 'save_favorite_message', // Action for PHP callback
						messageId: messageId, // ID of the message to save
						nonce: chatSettings.nonce // Nonce for security validation
					},
					success: function (response) {
						if (response.success) {
							// Check if the message was already saved
							if (response.data.alreadySaved) {
								showToast('Message unsaved');
							} else {
								// Message saved successfully
								console.log('Message saved:', response.message);
								showToast('Message saved'); // Show toast message
							}

							loadFavoriteMessages();

							// Perform any additional actions after saving the message
						} else {
							// Error occurred while saving the message
							console.error('Error saving message:', response.data);
						}
					},
					error: function (xhr, status, error) {
						// Error handling for AJAX request
						console.error('AJAX error:', error);
					}
				});
			}



			// Placeholder for future functionality
			$('.view-favorite-messages').click(function () {
				loadFavoriteMessages();
			});

			// Function to load favorite messages and display them in the chat partner list
			function loadFavoriteMessages() {
				// Show skeleton loading
				$('#chat-partner-list').html(getFavoriteMsgSkeletonHTML());

				// Perform AJAX request to fetch favorite messages
				$.ajax({
					url: chatSettings.ajax_url,
					method: 'POST',
					data: {
						action: 'load_favorite_messages', // Action for PHP callback
						nonce: chatSettings.nonce // Nonce for security validation
					},
					success: function (response) {
						console.log('favorite messages:', response.data.favorite_messages);
						// Check if favorite messages were successfully retrieved
						if (response && response.data && response.data.favorite_messages) {
							console.log('ok');
							const favoriteMessages = response.data.favorite_messages; // Assuming the response contains favorite messages

							// Clear the existing content in the chat partner list
							$('#chat-partner-list').empty();
							// Update Title
							updateChatPartnersTitle('messages');

							// Iterate through favorite messages and create HTML for each message
							favoriteMessages.forEach(function (message) {
								const messageHTML = createMessageSearchResultHTML(message);

								// Append the message HTML to the chat partner list
								$('#chat-partner-list').append(messageHTML);
							});
						} else {
							// Display message when there are no favorite messages
							$('#chat-partner-list').html('<li class="text-center pt-5">No favorite chat messages found.<br><a href="#" class="view-all-users pe-auto">Go back to All list</a></li>');
						}
					},
					error: function (xhr, status, error) {
						console.error('AJAX error:', error);
					}
				});
			}

			function getFavoriteMsgSkeletonHTML() {
				let skeletonHTML = '';
				for (let i = 0; i < 5; i++) {
					skeletonHTML += `
                    <div class="chat-history-skeleton">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-content">
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line short"></div>
                        </div>
                    </div>
                `;
				}
				return skeletonHTML;
			}


			// Check if the search input exists on the page
			if ($("#search-input").length > 0) {
				// PlaceHolder for search
				var defaultPlaceholder = "Search for users, messages...";
				var focusPlaceholder = "Type 3+ letters to search / ESC to clear";

				var searchInput = $("#search-input");
				var clearButton = $("#clear-search");

				// Change placeholder text on focus
				searchInput.on("focus", function () {
					$(this).attr("placeholder", focusPlaceholder);
				});

				// Restore placeholder text on blur if input is empty
				searchInput.on("blur", function () {
					if ($(this).val() === "") {
						$(this).attr("placeholder", defaultPlaceholder);
					}
				});

				// Clear search input and restore original placeholder text
				clearButton.on("click", function () {
					searchInput.val("");
					searchInput.attr("placeholder", defaultPlaceholder);
				});

				// Listen for "Esc" key press and trigger clear button functionality
				$(document).on("keydown", function (event) {
					if (event.key === "Escape") {
						clearButton.click();
					}
				});
			}

			// Check if the message input exists on the page
			if ($("#message-input").length > 0) {
				// Placeholder for message input
				var messageInput = $("#message-input");

				// Change placeholder text on focus
				messageInput.on("focus", function () {
					$(this).attr("placeholder", "Type and press Enter to send...");
				});

				// Restore placeholder text on blur if input is empty
				messageInput.on("blur", function () {
					if ($(this).val() === "") {
						$(this).attr("placeholder", "Type your message...");
					}
				});
			}

			// Enable dropdown hover effect
			$('.my-avatar .dropdown').hover(function () {
				$(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
			}, function () {
				$(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
			});

			// AJAX request to update user status
			$('.my-avatar .dropdown-item').on('click', function (e) {
				e.preventDefault();
				var status = $(this).data('status');

				$.ajax({
					url: chatSettings.ajax_url,
					type: 'POST',
					data: {
						action: 'update_user_status', // Change action to user status
						status: status,
						nonce: chatSettings.nonce
					},
					success: function (response) {
						if (response.success) {
							console.log('status data:', response.data.status);
							console.log('User status updated:', response.data.status);
							// Update button text and class
							$('.btn-status').text(status).removeClass().addClass('btn chat-user-status-' + status + ' dropdown-toggle btn-status text-capitalize py-0 px-2 fs-7 fw-semibold');
							// Get the status message from response data
							var statusMessage = response.data.status;
							showToast('User status updated:', statusMessage); // Show toast message
						} else {
							console.error('Error updating user status:', response.data);
						}
					},
					error: function (xhr, status, error) {
						console.error('AJAX error:', error);
					}
				});
			});

			// Add click event to the button
			var printButton = document.querySelector('.printButton');
			if (printButton) {
				printButton.addEventListener('click', function () {
					printChatHistory();
				});
			}

			// Function to print chat history
			function printChatHistory() {
				var chatHistory = document.getElementById('chat-messages').innerHTML;

				// Create a new window for printing
				var printWindow = window.open('', '_blank', 'width=600,height=400'); // Specify popup window size
				printWindow.document.write('<html><head><title>Print Chat History</title>');
				printWindow.document.write('<link rel="stylesheet" type="text/css" href="http://localhost/wp-content/plugins/javo-points/public/css/javo-interface-inner.css?ver=1.0.0.11">'); // Load external stylesheet
				printWindow.document.write('<link rel="stylesheet" id="jvcore-style-css" href="http://localhost/wp-content/plugins/javo-core/dist/css/style.css?ver=6.4.3" type="text/css" media="all">'); // Load external stylesheet
				printWindow.document.write('<link rel="stylesheet" id="bootstrap-css" href="http://localhost/wp-content/themes/javo-theme/assets/dist/css/bootstrap.css?ver=5.2.1" type="text/css" media="all">'); // Load external stylesheet
				printWindow.document.write('</head><body>');
				printWindow.document.write(chatHistory);
				printWindow.document.write('</body></html>');
				printWindow.document.close(); // Complete document writing

				// Function to trigger the printer
				printWindow.onload = function () {
					printWindow.focus(); // Focus on the print dialog
					printWindow.print(); // Open print dialog
					printWindow.close(); // Close the popup window after printing
				};
			}

			/**
			 * Move to the message ID
			 */
			// Calculates the necessary amount of messages to load and scrolls to the target message
			function calculateLoadMsgAmountAndScrollToMessage(messageId, receiverId) {
				$.ajax({
					type: 'POST',
					url: chatSettings.ajax_url,
					data: {
						action: 'calculate_load_msg_amount',
						message_id: messageId,
						receiver_id: receiverId,
						nonce: chatSettings.nonce
					},
					success: function (response) {
						if (response.success) {
							var loadMsgAmount = response.data.loadMsgAmount;
							console.log('MSG-Position', loadMsgAmount);
							getChatMessages(loadMsgAmount, function () {
								scrollToMessage(messageId); // Scroll to the specific message after loading
							});
						}
					}
				});
			}

			// Function to apply a highlight effect to the target message
			function scrollToMessage(messageId) {
				var $targetMessage = $('[data-message-id="' + messageId + '"]');
				if ($targetMessage.length) {
					var $messageContent = $targetMessage.find('.content');
					$messageContent.addClass('highlighted'); // Add highlighted class to message content
					setTimeout(function () {
						$messageContent.removeClass('highlighted'); // Remove class after delay
					}, 500); // Adjust delay time as needed (500 milliseconds)
					isStartingChat = false;
				} else {
					console.error('Message with the specified ID not found.');
				}
			}

			jQuery(document).ready(function ($) {
				// 기본 테마를 'default'로 설정
				var defaultTheme = 'default';
				$('body').attr('data-chat-theme', defaultTheme.toLowerCase());

				// Function to update the theme
				function updateTheme(themeValue) {
					if (themeValue) {
						$('body').attr('data-chat-theme', themeValue.toLowerCase());
					} else {
						$('body').attr('data-chat-theme', defaultTheme.toLowerCase());
					}
				}

				// Event listener for theme selector changes
				$('#chat-theme').on('change', function () {
					updateTheme($(this).val());
				});

				// Get the current theme from the selector and update the theme on page load
				var currentTheme = $('#chat-theme').val();
				updateTheme(currentTheme);
			});


			function convertTextUrlsToLinks() {
				$('.message-content .content').each(function () {
					var messageContent = $(this);
					// chat-attachment check child element
					if (messageContent.find('.chat-attachment').length === 0) {
						var htmlContent = messageContent.html();
						var urlPattern = /(\b(https?:\/\/)?[a-z0-9-]+(\.[a-z0-9-]+)+([\/?].*)?)/gi;
						var linkedHtmlContent = htmlContent.replace(urlPattern, function (url) {
							var fullUrl = url;
							if (!/^https?:\/\//i.test(url)) {
								fullUrl = 'http://' + url;
							}
							return '<a href="' + fullUrl + '" target="_blank" rel="noopener noreferrer">' + url + '</a>';
						});
						messageContent.html(linkedHtmlContent);
					}
				});
			}



			//Uploading Media File
			var mediaUploader;

			$('#send_img_btn').click(function (e) {
				e.preventDefault();
				// Media library init and open
				if (mediaUploader) {
					mediaUploader.open();
					return;
				}
				mediaUploader = wp.media({
					title: 'Choose Image',
					button: {
						text: 'Choose Image'
					},
					multiple: false // Only 1 file
				});

				// Callback after selecting an image
				mediaUploader.on('select', function () {
					var attachment = mediaUploader.state().get('selection').first().toJSON();

					// Display a confirmation dialog to the user
					var confirmSend = confirm("Are you sure you want to send this file?");

					if (confirmSend) {
						// If the user clicks 'OK'
						console.log(attachment);
						sendMessage(attachment.id);
					} else {
						// If the user clicks 'Cancel'
						console.log("File transmission has been canceled.");
					}
				});

				// Open Uploader
				mediaUploader.open();
			});

			// Floating Chat Button Effect
			const chatButton = document.getElementById('jv-floating-chat-button');
			const chatBubble = document.querySelector('.chat-icon-bubble');
			const chatClose = document.querySelector('.chat-icon-x');

			// Check if the chat button exists
			if (chatButton) {
				// Add click event for the chat button
				chatButton.addEventListener('click', function () {
					chatBubble.classList.toggle('active');
					chatClose.classList.toggle('active');
				});
			}

		} // check #javo-chat
	});
})(jQuery);