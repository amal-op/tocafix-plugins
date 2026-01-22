import template from "./sw-cms-el-config-cmsbundle-product-list.html.twig";
import "./sw-cms-el-config-cmsbundle-product-list.scss";

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.register("sw-cms-el-config-cmsbundle-product-list", {
  template,

  inject: ["repositoryFactory", "feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      productCollection: null,
      showProductStreamPreview: false,

      // Temporary values to store the previous selection in case the user changes the assignment type.
      tempProductIds: [],
      tempStreamId: null,
    };
  },

  computed: {
    productRepository() {
      return this.repositoryFactory.create("product");
    },

    products() {
      if (
        this.element?.data?.products &&
        this.element.data.products.length > 0
      ) {
        return this.element.data.products;
      }

      return null;
    },

    productMediaFilter() {
      const criteria = new Criteria(1, 25);
      criteria.addAssociation("cover");

      return criteria;
    },

    productMultiSelectContext() {
      const context = { ...Shopware.Context.api };
      context.inheritance = true;

      return context;
    },

    productAssignmentTypes() {
      return [
        {
          label: this.$tc(
            "sw-cms.elements.productSlider.config.productAssignmentTypeOptions.manual"
          ),
          value: "static",
        },
        {
          label: this.$tc(
            "cmsbundle.element.product-list.config.category-assignment"
          ),
          value: "product_stream",
        },
      ];
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-product-slider");

      this.productCollection = new EntityCollection(
        "/product",
        "product",
        Shopware.Context.api
      );

      if (this.element.config.products.value.length <= 0) {
        return;
      }

      const criteria = new Criteria(1, 100);
      criteria.addAssociation("cover");
      criteria.setIds(this.element.config.products.value);

      this.productRepository
        .search(criteria, { ...Shopware.Context.api, inheritance: true })
        .then((result) => {
          this.productCollection = result;
        });
    },

    onChangeAssignmentType(type) {
      if (type === "static") {
        this.element.config.products.value = this.tempProductIds;
      } else {
        this.tempProductIds = this.element.config.products.value;
        this.element.config.products.value = [];
      }
    },

    onProductsChange() {
      this.element.config.products.value = this.productCollection.getIds();

      if (!this.element?.data) {
        return;
      }

      this.$set(this.element.data, "products", this.productCollection);
    },

    isSelected(itemId) {
      return this.productCollection.has(itemId);
    },
  },
});
