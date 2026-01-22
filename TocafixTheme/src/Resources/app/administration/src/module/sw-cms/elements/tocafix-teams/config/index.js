import template from './sw-cms-el-config-tocafix-teams.html.twig';

const { Criteria } = Shopware.Data;
const { Mixin } = Shopware;

Shopware.Component.register('sw-cms-el-config-tocafix-teams', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            repositoryCategories: null,
            categories: []
        };
    },

    created() {
        this.repositoryCategories = this.repositoryFactory.create('tocafix_team_category');
        this.getCategories();
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('tocafixteams');
        },

        getCategories() {
            const criteria = new Criteria(1, 500);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            this.repositoryCategories.search(criteria, Shopware.Context.api).then((result) => {
                this.categories = result;
                this.categories.unshift({
                    id: null,
                    name: '---'
                });
            });
        }
    }
});