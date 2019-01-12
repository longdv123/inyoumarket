var GCM_ENDPOINT = 'https://android.googleapis.com/gcm/send';

var isPushEnabled = false;
var isClickFlg = false;
var isRetryFlg = false;
var btnONMsg = "入荷通知を受け取る";
var btnOFFMsg = "入荷通知の受け取りを無効化";
var isSkipFlg = false;

function controllbtn(disabledflg) {
    $("#js-push-button").prop("disabled", disabledflg);
}

function endpointWorkaround(pushSubscription) {
  if (pushSubscription.endpoint.indexOf('https://android.googleapis.com/gcm/send') !== 0) {
    controllbtn(true);
    isSkipFlg = true;
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

function sendSubscriptionToServer(subscription) {

  var mergedEndpoint = endpointWorkaround(subscription);

  // regist_id 登録
  postRegistId(mergedEndpoint);
}

function postRegistId(mergedEndpoint) {

    if(isSkipFlg) {
        // GCM以外の場合処理をスキップ
        alert("Chromeブラウザでのみご利用可能な機能となります。");

        var pb = document.querySelector('.js-push-button');

        navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
            serviceWorkerRegistration.pushManager.getSubscription().then(
              function(pushSubscription) {
                if (!pushSubscription) {
                  isPushEnabled = false;
                  pb.disabled = false;
                  pb.textContent = btnONMsg;
                  controllbtn(true);
                  return;
                }

                pushSubscription.unsubscribe().then(function(successful) {
                  pb.disabled = false;
                  controllbtn(true);
                  pb.textContent = btnONMsg;
                  isPushEnabled = false;
                }).catch(function(e) {
                  pb.disabled = false;
                  controllbtn(true);;
                });
              }).catch(function(e) {
                  controllbtn(true);
              });
          });

        return;
    }

    var endpointSections = mergedEndpoint.split('/');
    var subscriptionId = endpointSections[endpointSections.length - 1];

    // IDをhiddenへ設定
    $("#mode").val('setting');
    $("#regist_id").val(subscriptionId);

    if(isClickFlg == true) {
        // submit
        $('#form1').submit();

        $("#regist_id").val(subscriptionId);

    } else {
        if(subscriptionId != "") {

            // データチェック
              var data = {};
              data.mode = 'check';

              var rid = $("#regist_id").val();
              data.regist_id = rid;

              $.ajax({
                  type : 'POST',
                  url : '',
                  data: data,
                  cache : false,
                  dataType : 'json',
                  success : function(data, dataType) {
                      if (data.error) {
                          //
                      } else {

                         var pushButton = document.querySelector('.js-push-button');
                         if(data.ret == '1') {
                             pushButton.textContent = btnOFFMsg;
                             isRetryFlg = true;
                             isPushEnabled = true;
                         } else {
                             pushButton.textContent = btnONMsg;
                             isPushEnabled = false;
                         }
                      }
                  }
              });
        }
    }
}

function unsubscribe() {
  var pushButton = document.querySelector('.js-push-button');
  pushButton.disabled = true;
  controllbtn(true);

  // 停止確認
  var data = {};
  data.mode = 'stop_check';

  var rid = $("#regist_id").val();
  data.regist_id = rid;

  $.ajax({
      type : 'POST',
      url : '',
      data: data,
      cache : false,
      dataType : 'json',
      error: function() {
          alert("入荷通知の受け取り停止に失敗しました。");
      },
      success : function(data, dataType) {

          $("#mode").val('delete');
          $('#form1').submit();

          if(data.ret == '1') {
              // 停止のみ
              isPushEnabled = false;
              pushButton.disabled = false;
              pushButton.textContent = btnONMsg;
              controllbtn(false);

          } else {
              // 削除
              navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
                    serviceWorkerRegistration.pushManager.getSubscription().then(
                      function(pushSubscription) {
                        if (!pushSubscription) {
                          isPushEnabled = false;
                          pushButton.disabled = false;
                          pushButton.textContent = btnONMsg;
                          controllbtn(false);
                          return;
                        }

                        pushSubscription.unsubscribe().then(function(successful) {
                          pushButton.disabled = false;
                          controllbtn(false);
                          pushButton.textContent = btnONMsg;
                          isPushEnabled = false;
                        }).catch(function(e) {
                          pushButton.disabled = false;
                          controllbtn(false);
                        });
                      }).catch(function(e) {
                          controllbtn(true);
                      });
                  });

          }
      }
  });
}

function subscribe() {
  var pushButton = document.querySelector('.js-push-button');
  pushButton.disabled = true;
  controllbtn(true);

  navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
    serviceWorkerRegistration.pushManager.subscribe({userVisibleOnly: true})
      .then(function(subscription) {
        isPushEnabled = true;
        pushButton.textContent = btnOFFMsg;
        pushButton.disabled = false;
        controllbtn(false);

        return sendSubscriptionToServer(subscription);
      })
      .catch(function(e) {
        if (Notification.permission === 'denied') {
          pushButton.disabled = true;
          controllbtn(true);
        } else {
          pushButton.disabled = false;
          pushButton.textContent = btnONMsg;
          controllbtn(false);
        }
      });
  });
}

function initialiseState() {
  if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
      controllbtn(true);
      return;
  }

  if (Notification.permission === 'denied') {
      controllbtn(true);
      return;
  }

  if (!('PushManager' in window)) {
      controllbtn(true);
      return;
  }

  navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
    serviceWorkerRegistration.pushManager.getSubscription()
      .then(function(subscription) {
        var pushButton = document.querySelector('.js-push-button');

        pushButton.disabled = false;
        controllbtn(false);

        if (!subscription) {
          return;
        }

        sendSubscriptionToServer(subscription);

        if(!isRetryFlg) {
            pushButton.textContent = btnOFFMsg;
            isPushEnabled = true;
        }

      })
      .catch(function(err) {
          controllbtn(true);
      });
  });
}

window.addEventListener('load', function() {
  var pushButton = document.querySelector('.js-push-button');
  pushButton.addEventListener('click', function() {
    if (isPushEnabled) {
      unsubscribe();
    } else {
      isClickFlg = true;
      subscribe();
    }
  });

  if ('serviceWorker' in navigator) {
    controllbtn(true);
    navigator.serviceWorker.register('./../../dpost-service-worker.js')
    .then(initialiseState);
  } else {
      controllbtn(true);
  }
});
