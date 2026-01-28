const { Component, Context } = Shopware;

Component.extend("tocafix-job-create", "tocafix-job-detail", {
  methods: {
    createdComponent() {
      const systemLanguageId = Context.api.systemLanguageId;
      const currentLanguageId = Context.api.languageId;

      if (currentLanguageId !== systemLanguageId) {
        Context.api.languageId = systemLanguageId;
      }

      this.$super("createdComponent");
    },
    
    getJob() {
      this.job = this.repository.create(Context.api);
    },

    onClickSave() {
      this.isLoading = true;

      this.repository
        .save(this.job)
        .then(() => {
          this.isLoading = false;
          
          this.createNotificationSuccess({
            title: this.$tc("tocafix-job.detail.successTitle"),
            message: this.$tc("tocafix-job.detail.successMessage"),
          });
          
          this.$router.push({
            name: "tocafix.job.detail",
            params: { id: this.job.id },
          });
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
  },
});