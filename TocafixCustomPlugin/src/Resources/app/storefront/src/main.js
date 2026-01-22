import TocafixCommissionPlugin from './js/save-commission';
import TocafixDeliveryDatePlugin from './js/delivery-date';

const PluginManager = window.PluginManager;

PluginManager.register('TocafixCommission', TocafixCommissionPlugin, '[data-tocafix-commission]');
PluginManager.register('TocafixDeliveryDate', TocafixDeliveryDatePlugin, '.cart-add-delivery-date');

// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}
