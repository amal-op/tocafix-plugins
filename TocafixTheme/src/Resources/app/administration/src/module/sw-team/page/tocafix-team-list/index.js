import template from './tocafix-team-list.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('tocafix-team-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            isLoading: false,
            // Initialize as null or empty array
            teams: null
        };
    },

    // Shopware 6.6 still supports metaInfo, but moving toward 
    // a more standard Vue 3 approach in the future.
    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        // Use the repository factory to create the repository
        teamRepository() {
            return this.repositoryFactory.create('tocafix_team');
        },

        columns() {
            return [{
                property: 'name',
                label: this.$t('tocafix-team.list.columnName'),
                routerLink: 'tocafix.team.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'position',
                label: this.$t('tocafix-team.list.columnPosition'),
                allowResize: true
            }, {
                property: 'email',
                label: this.$t('tocafix-team.list.columnEmail'),
                allowResize: true
            }, {
                property: 'phoneNumber',
                label: this.$t('tocafix-team.list.columnPhoneNumber'),
                allowResize: true
            }, {
                property: 'sortOrder',
                label: this.$t('tocafix-team.list.columnSortOrder'),
                allowResize: true
            }];
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.loadList();
        },

        async loadList() {
            this.isLoading = true;
            
            const teamCriteria = new Criteria();
            teamCriteria.addSorting(Criteria.sort('sortOrder', 'ASC'));

            try {
                const result = await this.teamRepository.search(teamCriteria, Shopware.Context.api);
                this.teams = result;
            } catch (error) {
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },

        onChangeLanguage() {
            this.loadList();
        }
    }
});