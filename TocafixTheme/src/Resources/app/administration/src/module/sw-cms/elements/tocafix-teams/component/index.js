import template from "./sw-cms-el-tocafix-teams.html.twig";
import "./sw-cms-el-tocafix-teams.scss";

const { Mixin } = Shopware;

Shopware.Component.register("sw-cms-el-tocafix-teams", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  computed: {
    numberOfPosts() {
      if (this.element.config.numberOfPosts.value === 0) {
        return 3;
      }

      return this.element.config.numberOfPosts.value;
    },
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("tocafixteams");
    },
  },
});
