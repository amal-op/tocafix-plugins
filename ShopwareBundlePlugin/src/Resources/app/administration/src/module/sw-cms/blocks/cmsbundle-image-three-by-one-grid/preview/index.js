import template from './sw-cms-preview-cmsbundle-image-three-by-one-grid.html.twig';
import './sw-cms-preview-cmsbundle-image-three-by-one-grid.scss';

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
