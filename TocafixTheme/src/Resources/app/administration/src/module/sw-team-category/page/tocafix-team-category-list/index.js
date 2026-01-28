import template from './tocafix-team-category-list.html.twig';

Shopware.Component.register('tocafix-team-category-list', {
    template,

    inject: [
        'repositoryFactory',
    ],

    data() {
        return {
            isLoading: false,
            repository: null,
            teamCategories: null
        };
    },

    computed: {
        columns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: this.$t('tocafix-team-category.list.columnName'),
                routerLink: 'tocafix.team.category.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }];
        },

        teamCategoryRepository() {
            return this.repositoryFactory.create('tocafix_team_category');
        }
    },

    created() {
        this.repository = this.teamCategoryRepository;
        this.loadList();
    },

    methods: {
        loadList() {
            this.isLoading = true;
            const teamCategoryCriteria = new Shopware.Data.Criteria();

            this.repository
                .search(teamCategoryCriteria)
                .then((result) => {
                    this.teamCategories = result;
                })
                .catch(() => {
                    this.teamCategories = null;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onChangeLanguage(languageId) {
            Shopware.Context.api.languageId = languageId;
            this.loadList();
        }

    }
});