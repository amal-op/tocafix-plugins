import template from "./index.html.twig";
import "./index.scss";

const { Mixin } = Shopware;

Shopware.Component.register("sw-cms-el-config-cmsbundle-manufacturer-detail", {
  template,

  inject: ["repositoryFactory"],

  mixins: [Mixin.getByName("cms-element")],

  created() {
    this.createdComponent();
  },
  computed: {
    manufacturerRepository() {
      return this.repositoryFactory.create("product_manufacturer");
    },
  },
  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-manufacturer-detail");
    },
    async onManufacturerChange(manufacturerId) {
      if (!this.element?.data) {
        return;
      }

      const manufacturer = await this.manufacturerRepository.get(
        manufacturerId,
        Shopware.Context.api
      );

      this.$set(this.element.data, "manufacturer", manufacturer);
      this.$emit("element-update", this.element);
    },
  },
});
