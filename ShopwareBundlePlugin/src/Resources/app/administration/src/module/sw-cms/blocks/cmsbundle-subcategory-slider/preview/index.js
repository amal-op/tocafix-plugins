import template from './sw-cms-preview-sub-category-slider.html.twig';
import './sw-cms-preview-sub-category-slider.scss';

const { Component } = Shopware;

Component.register('sw-cms-preview-cmsbundle-subcategory-slider', {
    template,

    computed:{
        assetFilter() {
            return Shopware.Filter.getByName("asset");
          },
    }
});
