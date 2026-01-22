import template from "./sw-cms-el-config-cmsbundle-best-seller-slider.html.twig";
import "./sw-cms-el-config-cmsbundle-best-seller-slider.scss";

const { Component, Mixin } = Shopware;
Component.register("sw-cms-el-config-cmsbundle-best-seller-slider", {
  template,

  inject: ["repositoryFactory"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      propertyGroups: [],
    };
  },

  computed: {
    propertyGroupRepository() {
      return this.repositoryFactory.create('property_group');
    },

    propertyGroupCriteria() {
      const criteria = new Criteria();
      criteria.addSorting(Criteria.sort('name', 'ASC'));
      return criteria;
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-best-seller-slider");
    },

    loadPropertyGroups() {
      this.propertyGroupRepository.search(this.propertyGroupCriteria).then((result) => {
        this.propertyGroups = result;
      });
    },

    onChangePropertyGroup(propertyGroupId) {
      if (propertyGroupId) {
        this.element.config.productColorProperty.value = propertyGroupId;
        this.$emit('element-update', this.element);
      }
    }
  },
});