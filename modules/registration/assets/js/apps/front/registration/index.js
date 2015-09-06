'use strict';

var React = require('react');
var Immutable = require('immutable');
var birchpress = require('birchpress');
var RegistrationStore = require('brithoncrm/registration/stores/RegistrationStore');
var internationalizationStore = require('brithoncrm/common/stores/i18nStore');

var regAppComponent = null;

var ns = birchpress.provide('brithoncrm.registration.apps.front.registration', {

  __init__: function() {
    birchpress.addAction('birchpress.initFrameworkAfter', ns.run);
  },

  run: function() {
    var registrationApp = require('brithoncrm/registration/components/front/registration/RegistrationPanel');
    var registrationData = Immutable.fromJS({});
    var i18nData = Immutable.fromJS({});
    var registerAppContainer = document.getElementById('registerapp');

    if (!registrationAppComponent && registerAppContainer) {
      var registrationStore = RegistrationStore(regData);
      var i18nStore = internationalizationStore(i18nData);

      i18nStore.loadPO(i18n_registration.poString);
      regAppComponent = React.render(
        React.createElement(regApp, {
          store: registrationStore,
          translationStore: i18nStore,
          cursor: store.getCursor()
        }),
        registerAppContainer
      );
      registrationStore.setAttr('component', regAppComponent);
      birchpress.addAction('brithoncrm.registration.stores.RegistrationStore.onChangeAfter', function(store, newCursor) {
        store.getAttr('component').setProps({
          store: store,
          cursor: newCursor
        });
      });

      i18nStore.setAttr('component', regAppComponent);
      birchpress.addAction('brithoncrm.common.stores.i18nStore.onChangeAfter', function(store, newCursor) {
        store.getAttr('component').setProps({
          translationStore: store,
          cursor: newCursor
        });
      });
    }
  }
});

module.exports = ns;
