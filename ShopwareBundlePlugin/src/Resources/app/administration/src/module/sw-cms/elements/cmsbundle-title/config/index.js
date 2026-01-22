import template from './sw-cms-el-config-cmsbundle-title.html.twig';
import './sw-cms-el-config-cmsbundle-title.scss';

const { Component, Mixin } = Shopware;

Component.register('sw-cms-el-config-cmsbundle-title', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('cmsbundle-title');
        },
    },
});
