/**
 * @file
 * FCM javascript functions.
 */

Drupal.behaviors.fcm = {
  attach: function (context, settings) {
      // Initialize Firebase
      var config = {
        apiKey: drupalSettings.fcm.settings.apiKey,
        authDomain: drupalSettings.fcm.settings.authDomain,
        databaseURL: drupalSettings.fcm.settings.databaseUrl,
        projectId: drupalSettings.fcm.settings.projectId,
        storageBucket: drupalSettings.fcm.settings.storageBucket,
        messagingSenderId: drupalSettings.fcm.settings.messagingSenderId
      };
      firebase.initializeApp(config);
  }
}
