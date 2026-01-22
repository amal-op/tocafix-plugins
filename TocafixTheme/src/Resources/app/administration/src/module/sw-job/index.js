import './page/tocafix-job-list';
import './page/tocafix-job-create';
import './page/tocafix-job-detail';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('tocafix-job', {
    type: 'plugin',
    name: 'job',
    title: 'tocafix-job.general.mainMenuItemGeneral',
    description: 'sw-property.general.descriptionTextModule',
    color: '#57D9A3',
    icon: 'default-symbol-products',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        list: {
            component: 'tocafix-job-list',
            path: 'list'
        },
        create: {
            component: 'tocafix-job-create',
            path: 'create',
            meta: {
                parentPath: 'tocafix.job.list'
            }
        },
        detail: {
            component: 'tocafix-job-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'tocafix.job.list'
            }
        },
    },

    navigation: [{
        label: 'tocafix-job.general.mainMenuItemGeneral',
        path: 'tocafix.job.list',
        parent: 'sw-content',
        position: 110
    }]
});