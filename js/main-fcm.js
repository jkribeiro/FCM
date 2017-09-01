/**
 * @file
 * FCM javascript functions.
 */

(function ($) {
  Drupal.behaviors.fcm = {
    attach: function (context, settings) {
      // For now, the module only supports FCM messages for authenticated users.
      // @TODO: Add support for anonymous users.
      var userId = settings.user.uid;
      if (userId === 0) {
        return;
      }

      // Initialize Firebase.
      var config = {
        apiKey: drupalSettings.fcm.settings.apiKey,
        authDomain: drupalSettings.fcm.settings.authDomain,
        databaseURL: drupalSettings.fcm.settings.databaseUrl,
        projectId: drupalSettings.fcm.settings.projectId,
        storageBucket: drupalSettings.fcm.settings.storageBucket,
        messagingSenderId: drupalSettings.fcm.settings.messagingSenderId
      };
      firebase.initializeApp(config);

      // [START get_messaging_object]
      // Retrieve Firebase Messaging object.
      const messaging = firebase.messaging();
      // [END get_messaging_object]

      // [START refresh_token]
      // Callback fired if Instance ID token is updated.
      messaging.onTokenRefresh(function() {
        messaging.getToken()
            .then(function(refreshedToken) {
              console.log('Token refreshed.');
              // Indicate that the new Instance ID token has not yet been sent
              // to the app server.
              setTokenSentToServer(false);
              // Send Instance ID token to app server.
              sendTokenToServer(refreshedToken);
            })
            .catch(function(err) {
              console.log('Unable to retrieve refreshed token ', err);
            });
      });
      // [END refresh_token]

      // [START receive_message]
      // Handle incoming messages. Called when:
      // - a message is received while the app has focus
      // - the user clicks on an app notification created by a service worker
      //   `messaging.setBackgroundMessageHandler` handler.
      messaging.onMessage(function(payload) {
        console.log("Message received. ", payload);
        // Update the UI to include the received message.
        appendMessage(payload);
      });
      // [END receive_message]

      // Send the Instance ID token your application server, so that it can:
      // - send messages back to this app
      // - subscribe/unsubscribe the token from topics

      /**
       * Send the Instance ID token your application server.
       *
       * So that it can:
       * - Send messages back to this app
       * - Subscribe/unsubscribe the token from topics
       *
       * @param {string} currentToken
       *   The current firebase token set to the browser.
       */
      function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer()) {
          console.log('Sending token to server...');

          // Send token to backend.
          $.post( "/fcm/register", currentToken);

          // TODO(developer): Send the current token to your server.
          setTokenSentToServer(true);

          // Stores the token in the local html storage.
          window.localStorage.setItem('currentFirebaseToken', currentToken);
        } else {
          console.log('Token already sent to server so won\'t send it again ' +
              'unless it changes');
        }

      }

      /**
       * Checks if the current token was already sent to the backend.
       *
       * @returns {boolean}
       *   Returns TRUE in case of success, FALSE otherwise.
       */
      function isTokenSentToServer() {
        return window.localStorage.getItem('sentToServer') == 1;
      }

      /**
       * Flags that the token was already sent to the backend server.
       *
       * @param {boolean} sent
       *   Returns TRUE in case of success, FALSE otherwise.
       */
      function setTokenSentToServer(sent) {
        window.localStorage.setItem('sentToServer', sent ? 1 : 0);
      }

      /**
       * Displays a consent dialog to let users grant the app permission to
       * receive notifications in the browser.
       */
      function requestPermission() {
        console.log('Requesting permission...');

        // TODO: FOR DEVELOPMENT ONLY, REMOVE THIS CODE:
        setTokenSentToServer(false);
        sendTokenToServer('dJyKzIc-fRY:APA91bHjbRUFDLTDt4YXKBz90lWB-yPttbwzd5rCUfiuRS3A1kR4Vfe-R28XyaDHgXkcjwEZd1k09ZA0u7M-7AqmgdCo5lQKawFQcr-JLxRN4ucyqok_tyJI31CYmxDZTStSxJErre4I');
        // TODO: END DEV

        // [START request_permission]
        messaging.requestPermission()
            .then(function() {
              console.log('Notification permission granted.');
              // Get Instance ID token. Initially this makes a network call,
              // once retrieved subsequent calls to getToken will return from
              // cache.
              messaging.getToken()
                  .then(function(currentToken) {
                    console.log(currentToken);
                    if (currentToken) {
                      // If the user has revoked the notification, we need to
                      // send the token to the server again.
                      if (window.localStorage.getItem('currentFirebaseToken') !== currentToken) {
                        setTokenSentToServer(false);
                      }

                      sendTokenToServer(currentToken);
                    }
                  })
                  .catch(function(err) {
                    console.log('An error occurred while retrieving token. ', err);
                    setTokenSentToServer(false);
                  });
            })
            .catch(function(err) {
              console.log('Unable to get permission to notify.', err);
            });
        // [END request_permission]
      }

      /**
       * Deletes the current token.
       * @TODO This method is not being used.
       */
      function deleteToken() {
        // Delete Instance ID token.
        // [START delete_token]
        messaging.getToken()
            .then(function(currentToken) {
              messaging.deleteToken(currentToken)
                  .then(function() {
                    console.log('Token deleted.');
                    setTokenSentToServer(false);
                  })
                  .catch(function(err) {
                    console.log('Unable to delete token. ', err);
                  });
              // [END delete_token]
            })
            .catch(function(err) {
              console.log('Error retrieving Instance ID token. ', err);
            });

      }

      /**
       * Add a message to the messages element.
       *
       * @param {json} payload
       *   The message json payload.
       */
      function appendMessage(payload) {
        // TODO Show message in a tooltip.
      }

      requestPermission();
    }

  }
})(jQuery);
