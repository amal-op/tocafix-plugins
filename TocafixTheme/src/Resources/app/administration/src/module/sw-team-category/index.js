import './page/tocafix-team-category-list';
import './page/tocafix-team-category-create';
import './page/tocafix-team-category-detail';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

Shopware.Module.register('tocafix-team-category', {
    type: 'plugin',
    name: 'team',
    title: 'tocafix-team-category.general.mainMenuItemGeneral',
    description: 'sw-property.general.descriptionTextModule',
    color: '#57D9A3',
    icon: 'default-action-settings',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'tocafix-team-category-list',
            path: 'list',
            meta: {
                privilege: 'tocafix_team_category:read'
            }
        },
        create: {
            component: 'tocafix-team-category-create',
            path: 'create',
            meta: {
                parentPath: 'tocafix.team.category.list',
                privilege: 'tocafix_team_category:create'
            }
        },
        detail: {
            component: 'tocafix-team-category-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'tocafix.team.category.list',
                privilege: 'tocafix_team_category:read'
            }
        }
    },

    settingsItem: [{
        to: 'tocafix.team.category.list',
        group: 'plugins',
        icon: 'default-symbol-content',
    }]
});