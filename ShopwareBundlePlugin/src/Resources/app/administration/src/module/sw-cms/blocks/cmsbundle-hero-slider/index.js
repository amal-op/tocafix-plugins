import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'cmsbundle-hero-slider',
    label: 'cmsbundle.block.hero-slider.label',
    category: 'cmsbundleSliders',
    component: 'sw-cms-block-cmsbundle-hero-slider',
    previewComponent: 'sw-cms-preview-cmsbundle-hero-slider',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed'
    },
    slots: {
        'background': {
            type: 'cmsbundle-hero-slider-background',
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
        'bannerSlider01': {
            type: 'cmsbundle-hero-slider',
            default: {
                config: {
                    displayMode: { source: 'static', value: 'cover' },
                },
                data: {
                    media: {
                        url: '/shopwarebundleplugin/static/img/cta-element.jpg',
                    },
                },
            },
        },
        'bannerSlider02': {
            type: 'cmsbundle-hero-slider',
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
        'bannerSlider03': {
            type: 'cmsbundle-hero-slider',
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
        'bannerSlider04': {
            type: 'cmsbundle-hero-slider',
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
        'bannerSlider05': {
            type: 'cmsbundle-hero-slider',
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
        'bannerSlider06': {
            type: 'cmsbundle-hero-slider',
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
