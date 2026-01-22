Shopware.Component.register('sw-cms-preview-cmsbundle-image-one-by-three-grid', () => import('./preview'));
Shopware.Component.register('sw-cms-block-cmsbundle-image-one-by-three-grid', () => import('./component'));


Shopware.Service('cmsService').registerCmsBlock({
    name: 'cmsbundle-image-one-by-three-grid',
    label: 'cmsbundle.block.image-one-by-three-grid.label',
    category: 'cmsbundleGrids',
    component: 'sw-cms-block-cmsbundle-image-one-by-three-grid',
    previewComponent: 'sw-cms-preview-cmsbundle-image-one-by-three-grid',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        'right-top': {
            type: 'image',
        },
        'right-bottom': {
            type: 'image',
        },
        'right-middle': {
            type: 'image',
        },
        left: {
            type: 'image',
        },
    },
});
