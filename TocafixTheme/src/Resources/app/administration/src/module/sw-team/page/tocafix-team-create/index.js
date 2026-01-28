const { Component, Context } = Shopware;

Component.extend("tocafix-team-create", "tocafix-team-detail", {
  methods: {
    createdComponent() {
      const systemLanguageId = Context.api.systemLanguageId;
      const currentLanguageId = Context.api.languageId;

      if (currentLanguageId !== systemLanguageId) {
        Context.api.languageId = systemLanguageId;
      }

      this.$super("createdComponent");
    },

    getTeam() {
      this.team = this.repository.create(Context.api);
    },

    onClickSave() {
      this.isLoading = true;

      this.repository
        .save(this.team)
        .then(() => {
          this.isLoading = false;
          this.createNotificationSuccess({
            title: this.$t("tocafix-team.detail.successTitle"),
            message: this.$t("tocafix-team.detail.successMessage"),
          });
          this.$router.push({
            name: "tocafix.team.detail",
            params: { id: this.team.id },
          });
        })
        .catch((exception) => {
          this.isLoading = false;

          let errorMessage = this.$t("tocafix-team.detail.errorTitle");

          if (exception.response?.data?.errors) {
            errorMessage = exception.response.data.errors
              .map((error) => error.detail)
              .join(" ");
          }

          this.createNotificationError({
            title: this.$t("tocafix-team.detail.errorTitle"),
            message: errorMessage,
          });
        });
    },
  },
});