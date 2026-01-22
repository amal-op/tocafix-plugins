// Import plugins
import TocafixFooterPlugin from './plugins/footer';
import ApplicationFormPlugin from './plugins/application-form.plugin';

// Register plugins with PluginManager
// Shopware 6.5.x compatible plugin registration
const PluginManager = window.PluginManager;

// Register on body to allow finding elements anywhere in the page
PluginManager.register('TocafixFooterPlugin', TocafixFooterPlugin, 'body');
PluginManager.register('ApplicationFormPlugin', ApplicationFormPlugin, '[data-application-form-plugin]');