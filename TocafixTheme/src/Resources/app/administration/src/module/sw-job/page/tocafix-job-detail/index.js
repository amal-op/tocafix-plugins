import template from "./tocafix-job-detail.html.twig";

const { Component, Mixin, Context } = Shopware;
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
        .get(this.$route.params.id)
        .then((entity) => {
          this.job = entity;
        })
        .catch(() => {
          this.createNotificationError({
            message: this.$tc("tocafix-job.detail.errorLoadingEntity"),
          });
        });
    },

    onClickSave() {
      this.isLoading = true;

      this.repository
        .save(this.job)
        .then(() => {
          this.getJob();
          this.isLoading = false;
          
          this.createNotificationSuccess({
            title: this.$tc("tocafix-job.detail.successTitle"),
            message: this.$tc("tocafix-job.detail.successMessage"),
          });
          
          this.processSuccess = true;
        })
        .catch((exception) => {
          this.isLoading = false;
          
          let errorMessage = this.$tc("tocafix-job.detail.errorMessage");

          if (exception.response?.data?.errors) {
            errorMessage = exception.response.data.errors
              .map((error) => error.detail)
              .join(" ");
          }

          this.createNotificationError({
            title: this.$tc("tocafix-job.detail.errorTitle"),
            message: errorMessage,
          });
        });
    },

    saveFinish() {
      this.processSuccess = false;
    },

    onChangeLanguage(languageId) {
      Context.api.languageId = languageId;
      this.getJob();
    },
  },
});