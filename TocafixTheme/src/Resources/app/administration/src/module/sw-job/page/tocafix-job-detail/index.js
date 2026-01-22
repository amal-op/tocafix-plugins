import template from "./tocafix-job-detail.html.twig";

const { Component, Mixin } = Shopware;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register("tocafix-job-detail", {
  template,

  inject: ["repositoryFactory"],

  mixins: [Mixin.getByName("notification")],

  metaInfo() {
    return {
      title: this.$createTitle(),
    };
  },

  data() {
    return {
      job: null,
      isLoading: false,
      processSuccess: false,
      repository: null,
    };
  },

  computed: {
    ...mapPropertyErrors("job", ["name", "jobDate"]),
    isNewJob() {
      return this.job && this.job.isNew();
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.repository = this.repositoryFactory.create("tocafix_job");
      this.getJob();
    },
    getJob() {
      this.repository
        .get(this.$route.params.id, Shopware.Context.api)
        .then((entity) => {
          this.job = entity;
        });
    },

    onClickSave() {
      this.isLoading = true;

      this.repository
        .save(this.job, Shopware.Context.api)
        .then(() => {
          this.getJob();
          this.isLoading = false;
          this.processSuccess = true;
        })
        .catch((exception) => {
          this.isLoading = false;
          this.createNotificationError({
            title: this.$tc("tocafix-job.detail.errorTitle"),
            message: exception,
          });
        });
    },

    saveFinish() {
      this.processSuccess = false;
    },

    onChangeLanguage() {
      this.getJob();
    },
  },
});
