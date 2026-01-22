Shopware.Component.register('sw-cms-preview-cmsbundle-image-three-column-grid', () => import('./preview'));
Shopware.Component.register('sw-cms-block-cmsbundle-image-three-column-grid', () => import('./component'));


Shopware.Service('cmsService').registerCmsBlock({
    name: 'cmsbundle-image-three-column-grid',
    label: 'sw-cms.blocks.image.imageSimpleGrid.label',
    category: 'cmsbundleGrids',
    component: 'sw-cms-block-cmsbundle-image-three-column-grid',
    previewComponent: 'sw-cms-preview-cmsbundle-image-three-column-grid',
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
        left: {
            type: 'image',
        },
    },
});
