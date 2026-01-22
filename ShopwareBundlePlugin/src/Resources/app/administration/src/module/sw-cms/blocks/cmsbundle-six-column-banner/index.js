import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'cmsbundle-six-column-banner',
    label: 'cmsbundle.block.column-banner.label.six',
    category: 'cmsbundleColumns',
    component: 'sw-cms-block-cmsbundle-six-column-banner',
    previewComponent: 'sw-cms-preview-cmsbundle-six-column-banner',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        'left-left': {
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
        'left-right': {
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
        'center-left': {
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
        'center-right': {
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
        'right-left': {
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
        'right-right': {
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
