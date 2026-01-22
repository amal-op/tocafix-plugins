import template from "./index.html.twig";
import "./index.scss";

const { Mixin } = Shopware;

Shopware.Component.register("sw-cms-el-cmsbundle-breadcrumbs", {
  template: template,

  mixins: [Mixin.getByName("cms-element")],
  created() {
    this.createdComponent();
  },

  computed: {
    horiztalAlign() {
      if (!this.element.config.horizontalAlign.value) {
        return null;
      }
      return `align-self: ${this.element.config.horizontalAlign.value};`;
    },
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-breadcrumbs");
    },
  },
});
