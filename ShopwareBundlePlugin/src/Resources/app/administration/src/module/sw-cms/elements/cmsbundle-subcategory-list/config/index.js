import template from "./sw-cms-el-config-sub-category-list.html.twig";
import "./sw-cms-el-config-sub-category-list.scss";

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.register("sw-cms-el-config-sub-category-list", {
  template,

  inject: ["repositoryFactory", "feature"],

  mixins: [Mixin.getByName("cms-element")],
  data() {
    return {
      categoryCollection: null,
      blogPostCollection: null
    };
  },
  created() {
    this.createdComponent();
  },
  computed: {
    categoryRepository() {
      return this.repositoryFactory.create('category');
    },

    categoryMultiSelectContext() {
      const context = Object.assign({}, Shopware.Context.api);
      context.inheritance = true;
      return context;
    },
  },

  methods: {
  //   mainCategoryCriteria() {
  //     const criteria = new Criteria(1, 25);
  //     criteria.addFilter(Criteria.equals('id', this.salesChannel.navigationCategoryId || null));

  //     return criteria;
  // },
    createdComponent() {
      this.initElementConfig("cmsbundle-subcategory-list");
      this.categoryCollection = new EntityCollection('/category', 'category', Shopware.Context.api);

      if (this.element.config.category.value.length <= 0) {
        return;
      }
      const criteria = new Criteria(1, 25);
      criteria.setIds(this.element.config.category.value);

      this.categoryRepository.search(criteria, this.categoryMultiSelectContext).then((result) => {
          this.categoryCollection = result;
        });
    },
    onCategoryChange() {
      this.element.config.category.value = this.categoryCollection.getIds();
      if (!this.element?.data) {
        return;
      }
      this.$set(this.element.data, 'category', this.categoryCollection);
      this.$emit('element-update', this.element);
    },

    isSelected(itemId) {
      return this.categoryCollection.has(itemId);
    },
  },
});
