import template from "./sw-cms-el-cmsbundle-cta-slider.html.twig";
import "./sw-cms-el-cmsbundle-cta-slider.scss";

const { Component, Mixin, Filter } = Shopware;

Component.register("sw-cms-el-cmsbundle-cta-slider", {
  template,

  inject: ["feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      sliderBoxLimit: 3,
    };
  },

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-cta-slider");
      this.initElementData("cmsbundle-cta-slider");
    },
  },
});
