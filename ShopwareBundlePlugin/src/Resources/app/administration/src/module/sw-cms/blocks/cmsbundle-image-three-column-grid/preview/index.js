import template from './sw-cms-preview-cmsbundle-image-three-column-grid.html.twig';
import './sw-cms-preview-cmsbundle-image-three-column-grid.scss';

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
