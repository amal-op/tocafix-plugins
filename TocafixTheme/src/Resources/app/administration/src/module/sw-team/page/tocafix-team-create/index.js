// tocafix-team-create.js
const { Component } = Shopware;

Component.extend("tocafix-team-create", "tocafix-team-detail", {
  methods: {
    createdComponent() {
      const systemLanguageId = Shopware.Context.api.systemLanguageId;
      const currentLanguageId = Shopware.Context.api.languageId;

      if (currentLanguageId !== systemLanguageId) {
        Shopware.State.commit("context/setApiLanguageId", systemLanguageId);
      }

      this.$super("createdComponent");
    },
    getTeam() {
      this.team = this.repository.create(Shopware.Context.api);

      if (this.team.sortOrder === undefined || this.team.sortOrder === null) {
        this.team.sortOrder = 1;
      }
    },

    onClickSave() {
      this.isLoading = true;

      this.repository
        .save(this.team, Shopware.Context.api)
        .then(() => {
          this.isLoading = false;
          this.$router.push({
            name: "tocafix.team.detail",
            params: { id: this.team.id },
          });
        })
        .catch((exception) => {
          this.isLoading = false;

          this.createNotificationError({
            title: this.$t("tocafix-team.detail.errorTitle"),
            message: exception,
          });
        });
    },

    saveFinish() {
      this.processSuccess = false;
    },
  },
});
