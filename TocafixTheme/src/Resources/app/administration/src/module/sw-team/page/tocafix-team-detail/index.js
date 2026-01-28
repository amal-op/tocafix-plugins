import template from "./tocafix-team-detail.html.twig";

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Component.register("tocafix-team-detail", {
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
      team: null,
      isLoading: false,
      processSuccess: false,
      repository: null,
    };
  },

  computed: {
    ...mapPropertyErrors("team", ["name", "email", "position", "sortOrder"]),
    isNewTeam() {
      return this.team && this.team.isNew();
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.repository = this.repositoryFactory.create("tocafix_team");
      this.getTeam();
    },
    getTeam() {
      if (!this.$route.params.id) {
        this.team = this.repository.create(Shopware.Context.api);
        if (!this.team.teamCategories) {
          this.team.teamCategories = new EntityCollection(
            "/tocafix_team_category",
            "tocafix_team_category",
            Shopware.Context.api,
          );
        }
        return;
      }

      const criteria = new Criteria();
      criteria.addAssociation("teamCategories");
      criteria.addAssociation("media");

      this.repository
        .get(this.$route.params.id, Shopware.Context.api, criteria)
        .then((entity) => {
          this.team = entity;
          if (!this.team.teamCategories) {
            this.team.teamCategories = new EntityCollection(
              "/tocafix_team_category",
              "tocafix_team_category",
              Shopware.Context.api,
            );
          }
        });
    },

    onClickSave() {
      this.isLoading = true;
      this.repository
        .save(this.team, Shopware.Context.api)
        .then(() => {
          this.getTeam();
          this.isLoading = false;
          this.createNotificationSuccess({
            title: this.$t("tocafix-team.detail.successTitle"),
            message: this.$t("tocafix-team.detail.successMessage"),
          });
          this.processSuccess = true;
        })
        .catch((exception) => {
          this.isLoading = false;
          if (exception.response && exception.response.status === 400) {
            return;
          }

          if (exception.response && exception.response.status >= 500) {
            this.createNotificationError({
              title: this.$t("tocafix-team.detail.errorTitle"),
              message: this.$t("tocafix-team.detail.errorMessage"),
            });
            return;
          }

          let errorMessage = this.$t("tocafix-team.detail.errorMessage");

          this.createNotificationError({
            title: this.$t("tocafix-team.detail.errorTitle"),
            message: errorMessage,
          });
        });
    },

    saveFinish() {
      this.processSuccess = false;
    },

    onChangeLanguage() {
      this.getTeam();
    },

    onTeamCategoriesChange(collection) {
      this.team.teamCategories = collection;
    },
  },
});