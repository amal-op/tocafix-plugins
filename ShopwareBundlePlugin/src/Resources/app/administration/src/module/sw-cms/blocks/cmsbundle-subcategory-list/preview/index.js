import template from './sw-cms-preview-sub-category-list.html.twig';
import './sw-cms-preview-sub-category-list.scss';

const { Component } = Shopware;

Component.register('sw-cms-preview-cmsbundle-subcategory-list', {
    template,

    computed:{
        assetFilter() {
            return Shopware.Filter.getByName("asset");
          },
    }
});
