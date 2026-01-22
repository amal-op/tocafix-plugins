import './component';
import './config';
import './preview';

const { Criteria } = Shopware.Data;
const criteria = new Criteria();

Shopware.Service('cmsService').registerCmsElement({
    name: 'tocafixteams',
    label: 'tocafix.element.teams.label',
    component: 'sw-cms-el-tocafix-teams',
    configComponent: 'sw-cms-el-config-tocafix-teams',
    previewComponent: 'sw-cms-el-preview-tocafix-teams',

    defaultConfig: {
        numberOfPosts: {
            source: 'static',
            value: 4
        },
        category: {
            source: 'static',
            value: null 
        }
    }
});