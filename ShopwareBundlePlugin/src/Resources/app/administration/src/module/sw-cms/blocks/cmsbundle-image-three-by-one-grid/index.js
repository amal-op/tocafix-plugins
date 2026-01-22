Shopware.Component.register('sw-cms-preview-cmsbundle-image-three-by-one-grid', () => import('./preview'));
Shopware.Component.register('sw-cms-block-cmsbundle-image-three-by-one-grid', () => import('./component'));


Shopware.Service('cmsService').registerCmsBlock({
    name: 'cmsbundle-image-three-by-one-grid',
    label: 'cmsbundle.block.image-three-by-one-grid.label',
    category: 'cmsbundleGrids',
    component: 'sw-cms-block-cmsbundle-image-three-by-one-grid',
    previewComponent: 'sw-cms-preview-cmsbundle-image-three-by-one-grid',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        'left-top': {
            type: 'image',
        },
        'left-bottom': {
            type: 'image',
        },
        'left-middle': {
            type: 'image',
        },
        right: {
            type: 'image',
        },
    },
});
