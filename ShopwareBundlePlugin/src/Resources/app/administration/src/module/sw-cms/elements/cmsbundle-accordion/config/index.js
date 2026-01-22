import template from "./index.html.twig";
import "./index.scss";

const { Mixin } = Shopware;

Shopware.Component.register("sw-cms-el-config-cmsbundle-accordion", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      deleteConfirmationIndex: null,
    };
  },

  created() {
    this.createdComponent();
  },
  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-accordion");
    },
    showDeleteConfirmation(index) {
      this.deleteConfirmationIndex = index;
    },
    deleteItem(index) {
      this.element.config.data.value.splice(index, 1);
      this.deleteConfirmationIndex = null;
    },
    cancelDelete() {
      this.deleteConfirmationIndex = null;
    },
    addNew() {
      this.element.config.data.value.push({
        name: "This is my entry",
        content:
          "<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua</p>",
      });
    },
  },
});
