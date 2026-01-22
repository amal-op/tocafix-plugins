// tocafix-team-detail.js
import template from "./tocafix-team-detail.html.twig";

const { Component } = Shopware;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();
const { Criteria } = Shopware.Data;

Component.register("tocafix-team-detail", {
  template,

  inject: ["repositoryFactory"],

  mixins: ["notification"],

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
    ...mapPropertyErrors("team", ["name", "email", "position"]),

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
      return this.$route.name === "tocafix.team.create";
    }
    
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
      const criteria = new Criteria();
      criteria.addAssociation("teamCategories");
      criteria.addAssociation("media");

      this.repository
        .get(this.$route.params.id, Shopware.Context.api, criteria)
        .then((entity) => {
          this.team = entity;
        });
    },

    onClickSave() {
      this.isLoading = true;

      this.repository
        .save(this.team, Shopware.Context.api)
        .then(() => {
          this.getTeam();
          this.isLoading = false;
          this.processSuccess = true;
        })
        .catch((exception) => {
          this.isLoading = false;
          this.createNotificationError({
            title: this.$t("tocafix-team.detail.errorTitle"),
            message: exception,
          });
        });
    },
  handleCategoryRemove(item) {
        // Remove from the association
        this.team.teamCategories.remove(item.id);
        
        // Force Vue to detect the change
        this.$forceUpdate();
    },

    onChangeLanguage() {
      this.getTeam();
    },
  },
});
