import template from "./sw-cms-el-config-cmsbundle-category-slider.html.twig";
import "./sw-cms-el-config-cmsbundle-category-slider.scss";

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.register("sw-cms-el-config-cmsbundle-category-slider", {
  template,

  inject: ["repositoryFactory", "feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      categoryCollection: null,
      showProductStreamPreview: false,

      // Temporary values to store the previous selection in case the user changes the assignment type.
      tempProductIds: [],
      tempStreamId: null,
    };
  },

  computed: {
    categoryRepository() {
      return this.repositoryFactory.create("category");
    },

    categories() {
      if (
        this.element?.data?.categories &&
        this.element.data.categories.length > 0
      ) {
        return this.element.data.categories;
      }

      return null;
    },

    categoryMediaFilter() {
      const criteria = new Criteria(1, 25);
      criteria.addAssociation("media");

      return criteria;
    },

    categoryMultiSelectContext() {
      const context = { ...Shopware.Context.api };
      context.inheritance = true;

      return context;
    },

    categoryAssignmentTypes() {
      return [
        {
          label: this.$tc(
            "sw-cms.elements.productSlider.config.productAssignmentTypeOptions.manual"
          ),
          value: "static",
        },
      ];
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-category-slider");

      this.categoryCollection = new EntityCollection(
        "/category",
        "category",
        Shopware.Context.api
      );

      if (this.element.config.categories.value.length <= 0) {
        return;
      }

      const criteria = new Criteria(1, 100);
      criteria.addAssociation("media");
      criteria.setIds(this.element.config.categories.value);

      this.categoryRepository
        .search(criteria, { ...Shopware.Context.api, inheritance: true })
        .then((result) => {
          this.categoryCollection = result;
        });
    },

    onChangeAssignmentType(type) {
      if (type === "product_stream") {
        this.tempProductIds = this.element.config.categories.value;
        this.element.config.categories.value = this.tempStreamId;
      } else {
        this.tempStreamId = this.element.config.categories.value;
        this.element.config.categories.value = this.tempProductIds;
      }
    },

    onCategoriesChange() {
      this.element.config.categories.value = this.categoryCollection.getIds();

      if (!this.element?.data) {
        return;
      }
      this.$set(this.element.data, "categories", this.categoryCollection);
    },

    isSelected(itemId) {
      return this.categoryCollection.has(itemId);
    },
  },
});
