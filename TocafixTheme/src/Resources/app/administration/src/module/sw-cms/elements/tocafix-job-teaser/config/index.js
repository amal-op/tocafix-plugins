import template from './sw-cms-el-config-tocafix-job-teaser.html.twig';

const { Criteria, EntityCollection } = Shopware.Data;

Shopware.Component.register('sw-cms-el-config-tocafix-job-teaser', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        'cms-element'
    ],

    data() {
        return {
            jobCollection: null,
        };
    },

    computed: {
        jobRepository() {
            return this.repositoryFactory.create('tocafix_job');
        },

        jobSelectContext() {
            const context = Object.assign({}, Shopware.Context.api);
            context.inheritance = true;

            return context;
        },

        jobCriteria() {
            const criteria = new Criteria();

            return criteria;
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('tocafix-job-teaser');
            this.jobCollection = new EntityCollection('/tocafix_job', 'tocafix_job', Shopware.Context.api);
            if (this.element.config.job.value.length <= 0) {
                return;
            }
            const criteria = new Criteria(1, 100);
            criteria.setIds(this.element.config.job.value);

            this.jobRepository
                .search(criteria, Object.assign({}, Shopware.Context.api, { inheritance: true }))
                .then((result) => {
                    this.jobCollection = result;
                });
        },

        onJobChange(collection) {
            this.jobCollection = collection;
            this.element.config.job.value = collection.getIds();

            if (!this.element?.data) {
                return;
            }
            this.element.data.job = collection;
            this.$emit('element-update', this.element);
        },
    },
});