import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'tocafix-teams',
    label: 'tocafix.block.teams.label',
    category: 'Tocafix',
    component: 'sw-cms-block-tocafix-teams',
    previewComponent: 'sw-cms-preview-tocafix-teams',
    defaultConfig: {
    },
    slots: {
        content: {
            type: 'tocafixteams'
        }
    }
});
