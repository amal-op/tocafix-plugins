import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'cmsbundle-two-column-banner',
    label: 'cmsbundle.block.column-banner.label.two',
    category: 'cmsbundleColumns',
    component: 'sw-cms-block-cmsbundle-two-column-banner',
    previewComponent: 'sw-cms-preview-cmsbundle-two-column-banner',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        left: {
            type: 'cmsbundle-image',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'cover' },
                },
                data: {
                    media: {
                        url: '/shopwarebundleplugin/static/img/column-banner.png',
                    },
                },
            },
        },
        right: {
            type: 'cmsbundle-image',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'cover' },
                },
                data: {
                    media: {
                        url: '/shopwarebundleplugin/static/img/column-banner.png',
                    },
                },
            },
        },
    },
});
