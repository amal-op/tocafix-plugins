import template from "./index.html.twig";
import "./index.scss";

const { Mixin } = Shopware;

Shopware.Component.register("sw-cms-el-cmsbundle-nav-tabs", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-nav-tabs");
      this.initElementData("cmsbundle-nav-tabs");
    },
  },
});
