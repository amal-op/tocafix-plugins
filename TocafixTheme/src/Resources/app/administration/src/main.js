import './module/sw-cms';
import './module/sw-team';
import './module/sw-team-category';
import './module/sw-job';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

Shopware.Locale.extend('de-DE', deDE);
Shopware.Locale.extend('en-GB', enGB);