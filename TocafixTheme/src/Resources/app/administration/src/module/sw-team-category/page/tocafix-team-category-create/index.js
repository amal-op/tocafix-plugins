const { Component } = Shopware;

Component.extend(
  "tocafix-team-category-create",
  "tocafix-team-category-detail",
  {
    methods: {
      createdComponent() {
        const systemLanguageId = Shopware.Context.api.systemLanguageId;
        const currentLanguageId = Shopware.Context.api.languageId;

        if (currentLanguageId !== systemLanguageId) {
          Shopware.Context.api.languageId = systemLanguageId;
          this.$nextTick(() => {
            this.$forceUpdate();
          });
        }

        this.$super("createdComponent");
      },
      getTeamCategory() {
        this.teamCategory = this.teamCategoryRepository.create(Shopware.Context.api);
      },

      onClickSave() {
        this.isLoading = true;

        this.teamCategoryRepository
          .save(this.teamCategory)
          .then(() => {
            this.isLoading = false;
            this.$router.push({
              name: "tocafix.team.category.detail",
              params: { id: this.teamCategory.id },
            });
          })
          .catch((exception) => {
            this.isLoading = false;

            let errorMessage = this.$tc(
              "tocafix-team-category.detail.errorTitle",
            );

            if (exception.response?.data?.errors) {
              errorMessage = exception.response.data.errors
                .map((error) => error.detail)
                .join(" ");
            }

            this.createNotificationError({
              title: this.$tc("tocafix-team-category.detail.errorTitle"),
              message: errorMessage,
            });
          });
      },
    },
  },
);