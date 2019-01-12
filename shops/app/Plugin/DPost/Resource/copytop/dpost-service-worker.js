'use strict'

var open_url = "/";
var device_regist_id = "";

self.addEventListener('push', function(event) {
      console.log('Received a push message', event);

      self.registration.pushManager.getSubscription().then(
          function(subscription) {

              if(!subscription) {
                  console.log('subscription', "NULL");
                  return;
              }

              var mergedEndpoint = endpointWorkaround(subscription);

              var endpointSections = mergedEndpoint.split('/');
              var subscriptionId = endpointSections[endpointSections.length - 1];

              console.log('subscription', subscriptionId);

              device_regist_id = subscriptionId;

              var send_url = "./dpost/message?regist_id=" + device_regist_id;

              var promise = self.fetch(send_url , {
                  credentials: "include"
              }).then(function (res) {
                  return res.text();
              }).then(function (text) {
                 var data = JSON.parse(text);

                 open_url = decodeURI(data.open_url);

                 return self.registration.showNotification(decodeURI(data.title), {
                    body: decodeURI(data.body),
                    icon: './plugin/dpost/logo-192x192.png',
                    tag: "dpost-notification-tag"
                 });
              });

          });

    });

    function endpointWorkaround(pushSubscription) {

      if (pushSubscription.endpoint.indexOf('https://android.googleapis.com/gcm/send') !== 0) {
        return pushSubscription.endpoint;
      }

      var mergedEndpoint = pushSubscription.endpoint;

      if (pushSubscription.subscriptionId &&
        pushSubscription.endpoint.indexOf(pushSubscription.subscriptionId) === -1) {

        mergedEndpoint = pushSubscription.endpoint + '/' +
          pushSubscription.subscriptionId;
      }
      return mergedEndpoint;
    }


    self.addEventListener('notificationclick', function(event) {
      console.log('On notification click: ', event.notification.tag);

      event.notification.close();

      // click時
      event.waitUntil(clients.matchAll({
        type: "window"
      }).then(function(clientList) {
        for (var i = 0; i < clientList.length; i++) {
          var client = clientList[i];
          if (client.url == '/' && 'focus' in client)
            return client.focus();
        }
        if (clients.openWindow)

          if(open_url == "") {
              open_url = "/";
          }

          return clients.openWindow(open_url);
      }));

    });
