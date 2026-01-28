import template from "./tocafix-team-category-detail.html.twig";

const { mapPropertyErrors } = Shopware.Component.getComponentHelper();

Shopware.Component.register("tocafix-team-category-detail", {
  template,

  inject: ["repositoryFactory"],

  mixins: [
    Shopware.Mixin.getByName("notification"),
    Shopware.Mixin.getByName("placeholder"),
  ],

  data() {
    return {
      teamCategory: null,
      isLoading: false,
      processSuccess: false,
    };
  },

  computed: {
    ...mapPropertyErrors("teamCategory", ["name"]),

    identifier() {
      return this.teamCategory?.name || "";
    },

    teamCategoryRepository() {
      return this.repositoryFactory.create("tocafix_team_category");
    },

    tooltipSave() {
      const systemKey = this.$device.getSystemKey();

      return {
        message: `${systemKey} + S`,
        appearance: "light",
      };
    },

    tooltipCancel() {
      return {
        message: "ESC",
        appearance: "light",
      };
    },
    
    isCreateMode() {
      return this.$route.name === "tocafix.team.category.create";
    }
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.getTeamCategory();
    },
    
    getTeamCategory() {
      this.isLoading = true;

      this.teamCategoryRepository
        .get(this.$route.params.id)
        .then((entity) => {
          this.teamCategory = entity;
        })
        .catch(() => {
          this.createNotificationError({
            message: this.$tc(
              "tocafix-team-category.detail.errorLoadingEntity",
            ),
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    onClickSave() {
      this.isLoading = true;

      this.teamCategoryRepository
        .save(this.teamCategory)
        .then(() => {
          this.getTeamCategory();
          this.isLoading = false;
          this.processSuccess = true;
        })
        .catch((exception) => {
          this.isLoading = false;

          let errorMessage = this.$tc(
            "tocafix-team-category.detail.errorSaving",
          );

          if (exception.response?.data?.errors) {
            errorMessage = exception.response.data.errors
              .map((error) => error.detail)
              .join(" ");
          }

          this.createNotificationError({
            message: errorMessage,
          });
        });
    },

    saveFinish() {
      this.processSuccess = false;
    },

    onChangeLanguage(languageId) {
      Shopware.State.commit('context/setApiLanguageId', languageId);
      this.getTeamCategory();
    },
    
    saveOnLanguageChange() {
      return this.onClickSave();
    },

    abortOnLanguageChange() {
      return this.getTeamCategory();
    },
  },
});