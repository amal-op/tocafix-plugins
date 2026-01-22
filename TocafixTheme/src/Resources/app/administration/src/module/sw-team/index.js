import './page/tocafix-team-list';
import './page/tocafix-team-create';
import './page/tocafix-team-detail';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('tocafix-team', {
    type: 'plugin',
    name: 'team',
    title: 'tocafix-team.general.mainMenuItemGeneral',
    description: 'sw-property.general.descriptionTextModule',
    color: '#57D9A3',
    icon: 'default-symbol-products',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'tocafix-team-list',
            path: 'list'
        },
        create: {
            component: 'tocafix-team-create',
            path: 'create',
            meta: {
                parentPath: 'tocafix.team.list'
            }
        },
        detail: {
            component: 'tocafix-team-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'tocafix.team.list'
            }
        },
    },

    navigation: [{
        label: 'tocafix-team.general.mainMenuItemGeneral',
        path: 'tocafix.team.list',
        parent: 'sw-content',
        position: 90
    }]
});