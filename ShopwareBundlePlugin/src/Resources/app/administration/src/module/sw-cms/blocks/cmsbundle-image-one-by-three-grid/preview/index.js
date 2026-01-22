import template from './sw-cms-preview-cmsbundle-image-one-by-three-grid.html.twig';
import './sw-cms-preview-cmsbundle-image-one-by-three-grid.scss';

/**
 * @private
 * @package buyers-experience
 */
export default {
    template,

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset');
        },
    },
};
