import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'cmsbundle-image',
    label: 'cmsbundle.element.picture.label',
    component: 'sw-cms-el-cmsbundle-image',
    configComponent: 'sw-cms-el-config-cmsbundle-image',
    previewComponent: 'sw-cms-el-preview-cmsbundle-image',
    defaultConfig: {
        mediaDesktop: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'media',
            },
        },
        mediaTablet: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'media',
            },
        },
        mediaMobile: {
            source: 'static',
            value: null,
            required: false,
            entity: {
                name: 'media',
            },
        },
        lazyLoad: {
            source: 'static',
            value: true,
        },
        alignment: {
            source: 'static',
            value: '',
        },
        customClass: {
            source: 'static',
            value: '',
        },
        objectFit: {
            source: 'static',
            value: 'contain',
        },
        maxHeight: {
            source: 'static',
            value: 340,
        },
    },
});
