import template from './sw-cms-preview-cmsbundle-three-column-banner.html.twig';
import './sw-cms-preview-cmsbundle-three-column-banner.scss';

const { Component } = Shopware;

Component.register('sw-cms-preview-cmsbundle-three-column-banner', {
    template,

    computed: {
        assetFilter() {
          return Shopware.Filter.getByName("asset");
        },
      },
});
