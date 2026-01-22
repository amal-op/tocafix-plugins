import template from './tocafix-job-list.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('tocafix-job-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            isLoading: false,
            jobs: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        jobRepository() {
            return this.repositoryFactory.create('tocafix_job');
        },

        columns() {
            return [{
                property: 'name',
                label: this.$tc('tocafix-job.list.columnName'),
                routerLink: 'tocafix.job.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'jobDate',
                label: this.$tc('tocafix-job.list.columnJobDate'),
                allowResize: true
            }];
        }
    },

    created() {
        this.loadList();
    },

    methods: {
        async loadList() {
            this.isLoading = true;
            const jobCriteria = new Criteria();

            try {
                const result = await this.jobRepository.search(jobCriteria, Shopware.Context.api);
                this.jobs = result;
            } catch (error) {
                console.error(error);
            } finally {
                this.isLoading = false;
            }
        },

        formatDate(value) {
            if (!value) return '';

            const dateObj = new Date(value);
            if (isNaN(dateObj)) return '';

            return Shopware.Utils.format.date(value, {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        },

        onChangeLanguage() {
            this.loadList();
        }
    }
});