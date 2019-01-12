var GCM_ENDPOINT = 'https://android.googleapis.com/gcm/send';

var isPushEnabled = false;
var isClickFlg = false;
var isRetryFlg = false;
var isSkipFlg = false;

function controllbtn(disabledflg) {
    $("#js-push-button").prop("disabled", disabledflg);

    if(disabledflg == true) {
        // アラート非表示
        $("#alert_msg").css("display", "none");
    } else {
        if($("#my_regist_id").val() != "") {
            // アラート表示
            $("#alert_msg").css("display", "");
        } else {
            $("#alert_msg").css("display", "none");
        }
    }
}

function endpointWorkaround(pushSubscription) {
  if (pushSubscription.endpoint.indexOf('https://android.googleapis.com/gcm/send') !== 0) {
    controllbtn(true);
    isSkipFlg = true;
    $("#alert_msg").css("display", "none");
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
                  pb.textContent = '通知の有効化';
                  controllbtn(true);
                  return;
                }

                pushSubscription.unsubscribe().then(function(successful) {
                    pb.disabled = false;
                  controllbtn(true);
                  pb.textContent = '通知の有効化';
                  isPushEnabled = false;
                }).catch(function(e) {
                    pb.disabled = false;
                  controllbtn(true);
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

    $("#alert_msg").css("display", "none");

    var my_regist_id = $("#my_regist_id").val();

    if(my_regist_id != subscriptionId && isClickFlg == true) {
        // submit
        $('#form1').submit();
    } else {
        if(my_regist_id != "" && my_regist_id != subscriptionId) {
            $("#alert_msg").css("display", "");

            var pushButton = document.querySelector('.js-push-button');
            pushButton.textContent = '通知の有効化';

            isRetryFlg = true;

        } else if(my_regist_id == "" && subscriptionId != "") {

            var pushButton = document.querySelector('.js-push-button');
            pushButton.textContent = '通知の有効化';

            isRetryFlg = true;
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
      url : './dpostcheck',
      data: data,
      cache : false,
      dataType : 'json',
      error: function() {
          alert("通知の無効化に失敗しました。");
      },
      success : function(data, dataType) {

          $("#mode").val('delete');
          $('#form1').submit();

          // 停止でクリアされるため my_regist_id はクリア
          $("#my_regist_id").val("");

          if(data.ret == '1') {
              // 停止のみ
              isPushEnabled = false;
              pushButton.disabled = false;
              pushButton.textContent = '通知の有効化';
              controllbtn(true);

          } else {
              // 削除
              navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {

                    serviceWorkerRegistration.pushManager.getSubscription().then(
                      function(pushSubscription) {

                        if (!pushSubscription) {
                          isPushEnabled = false;
                          pushButton.disabled = false;
                          pushButton.textContent = '通知の有効化';
                          controllbtn(true);
                          return;
                        }

                        pushSubscription.unsubscribe().then(function(successful) {
                          pushButton.disabled = false;
                          controllbtn(false);
                          pushButton.textContent = '通知の有効化';
                          isPushEnabled = false;
                        }).catch(function(e) {
                          pushButton.disabled = false;
                          controllbtn(true);
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
        pushButton.textContent = '通知の無効化';
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
          pushButton.textContent = '通知の有効化';
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
            pushButton.textContent = '通知の無効化';
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
    navigator.serviceWorker.register('./../dpost-service-worker.js')
    .then(initialiseState);
  } else {
      controllbtn(true);
  }
});
