import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'tocafix-job-teaser',
    label: 'tocafix.block.job-teaser.label',
    category: 'Tocafix',
    component: 'sw-cms-block-tocafix-job-teaser',
    previewComponent: 'sw-cms-preview-tocafix-job-teaser',
    defaultConfig: {

    },
    slots: {
        // settings: {
        //     type: 'tocafix-title',
        //     default: {
        //         config: {
        //             title: {
        //                 source: 'static',
        //                 value: 'Offene Stellen',
        //             },
        //             subTitle: {
        //                 source: 'static',
        //                 value: 'Entdecken Sie unsere aktuellen Stellenangebote'
        //             }
        //         }
        //     }
        // },
        content: {
            type: 'tocafix-job-teaser'
        }
    },
});
