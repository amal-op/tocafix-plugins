import template from "./index.html.twig";
import "./index.scss";

const { Mixin } = Shopware;
Shopware.Component.register("sw-cms-el-cmsbundle-manufacturer-detail", {
  template,

  inject: ["repositoryFactory"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      sliderBoxLimit: 3,
    };
  },

  created() {
    this.createdComponent();
  },
  computed: {
    productManufacturerRepository() {
      return this.repositoryFactory.create("product_manufacturer");
    },
    manufacturerName() {
      if (this.element.data.manufacturer?.name) {
        return this.element.data.manufacturer.name;
      }

      return this.$tc("cmsbundle.element.manufacturer-detail.name");
    },
    description() {
      if (this.element.data.manufacturer?.description) {
        return this.element.data.manufacturer.description;
      }

      return this.$tc("cmsbundle.element.manufacturer-detail.description");
    },
    demoProductElement() {
      return {
        config: {
          boxLayout: {
            source: "static",
            value: this.element.config.boxLayout.value,
          },
          displayMode: {
            source: "static",
            value: this.element.config.displayMode.value,
          },
        },
        data: null,
      };
    },
    verticalAlignStyle() {
      if (!this.element.config.verticalAlign.value) {
        return null;
      }
      return `align-items: ${this.element.config.verticalAlign.value};`;
    },
  },
  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-manufacturer-detail");
      this.initElementData("cmsbundle-manufacturer-detail");

      this.fetchManufacturer();
    },

    fetchManufacturer() {
      console.log({ ...this.element });
      //   if (this.element.config.manufacturerId.value) {
      //     this.productManufacturerRepository
      //       .get(this.element.config.manufacturerId.value, Shopware.Context.api)
      //       .then((manufacturer) => {
      //         this.element.data.manufacturer = manufacturer;
      //       });
      //   }
    },
  },
});
