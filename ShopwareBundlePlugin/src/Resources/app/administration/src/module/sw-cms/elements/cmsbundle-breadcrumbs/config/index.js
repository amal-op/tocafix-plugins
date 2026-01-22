import template from "./index.html.twig";

const { Mixin } = Shopware;

Shopware.Component.register("sw-cms-el-config-cmsbundle-breadcrumbs", {
  template: template,

  mixins: [Mixin.getByName("cms-element")],

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-breadcrumbs");
    },
  },
});
