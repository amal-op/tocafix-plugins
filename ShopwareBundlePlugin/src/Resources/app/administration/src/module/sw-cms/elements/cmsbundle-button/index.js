import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'cmsbundle-button',
    label: 'cmsbundle.element.button.label',
    component: 'sw-cms-el-cmsbundle-button',
    configComponent: 'sw-cms-el-config-cmsbundle-button',
    previewComponent: 'sw-cms-el-preview-cmsbundle-button',
    defaultConfig: {
        url1: {
            source: 'static',
            value: null,
        },
        newTab1: {
            source: 'static',
            value: false,
        },
        buttonText1: {
            source: 'static',
            value: null,
        },
        buttonType1: {
            source: 'static',
            value: null,
        },
        buttomLevel1: {
            source: 'static',
            value: false,
        },
        url2: {
            source: 'static',
            value: null,
        },
        newTab2: {
            source: 'static',
            value: false,
        },
        buttonText2: {
            source: 'static',
            value: null,
        },
        buttonType2: {
            source: 'static',
            value: null,
        },
        buttomLevel2: {
            source: 'static',
            value: false,
        },
        url3: {
            source: 'static',
            value: null,
        },
        newTab3: {
            source: 'static',
            value: false,
        },
        buttonText3: {
            source: 'static',
            value: null,
        },
        buttonType3: {
            source: 'static',
            value: null,
        },
    },
});
