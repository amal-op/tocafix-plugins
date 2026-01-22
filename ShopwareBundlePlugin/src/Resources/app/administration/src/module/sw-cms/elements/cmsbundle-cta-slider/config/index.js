import template from "./sw-cms-el-config-cmsbundle-cta-slider.html.twig";
import "./sw-cms-el-config-cmsbundle-cta-slider.scss";

const { Component, Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;
const { moveItem, object: { cloneDeep } } = Shopware.Utils;

Component.register("sw-cms-el-config-cmsbundle-cta-slider", {
  template,

  inject: ["repositoryFactory", "feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      mediaModalIsOpenDesktop: false,
      mediaModalIsOpenTablet: false,
      mediaModalIsOpenMobile: false,
      mediaModalIsOpenBackgroundDesktop: false,
      mediaModalIsOpenBackgroundTablet: false,
      mediaModalIsOpenBackgroundMobile: false,
      initialFolderId: null,
      currentEditingIndex: null,
currentEditingType: null,
    };
  },

  computed: {
    mediaRepository() {
      return this.repositoryFactory.create("media");
    },

    contentUpdate: {
      get() {
        return this.element.config.content.value;
      },

      set(value) {
        this.element.config.content.value = value;
      },
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    onOpenMediaModal(index, type) {
      this.currentEditingIndex = index;
      this.currentEditingType = type;
    
      switch (type) {
        case 'desktop':
          this.mediaModalIsOpenDesktop = true;
          break;
          case 'tablet':
            this.mediaModalIsOpenTablet= true;
            break;
          case 'mobile':
            this.mediaModalIsOpenMobile= true;
            break;
          case 'backgroundDesktop':
            this.mediaModalIsOpenBackgroundDesktop= true;
            break;
          case 'backgroundTablet':
            this.mediaModalIsOpenBackgroundTablet= true;
            break;
          case 'backgroundMobile':
            this.mediaModalIsOpenBackgroundMobile= true;
            break;
          default:
            console.error('Unknown targetId type:', targetId.type);
      }
    },
    onCloseMediaModal() {
      this.mediaModalIsOpenDesktop = false;
      this.mediaModalIsOpenTablet= false;
      this.mediaModalIsOpenMobile= false;
      this.mediaModalIsOpenBackgroundDesktop= false;
      this.mediaModalIsOpenBackgroundTablet= false;
      this.mediaModalIsOpenBackgroundMobile= false;
      this.currentEditingIndex = null;
      this.currentEditingType = null;
    },
    onMediaSelectionChange(mediaItems) {
      const mediaItem = mediaItems[0];
    console.log("onMediaSelectionChange", mediaItem);
      if (mediaItem) {
        this.onImageUpload(this.currentEditingIndex, { targetId: mediaItem.id }, this.currentEditingType);
      }
      this.onCloseMediaModal();
    },
    addSliderItem() {
      this.element.config.sliderItems.value.push({
        desktop: { source: 'static', value: null },
        tablet: { source: 'static', value: null },
        mobile: { source: 'static', value: null },
        content: { source: 'static', value: null },
        title: { source: 'static', value: null },
        subTitle: { source: 'static', value: null },
        titleHeading: { source: 'static', value: null },
        subTitleHeading: { source: 'static', value: null },
        shortDescription: { source: 'static', value: null },
        buttons: [
          {
            url: { source: 'static', value: null },
            newTab: { source: 'static', value: false },
            buttonText: { source: 'static', value: null },
            buttonType: { source: 'static', value: null }
          }
        ],
        backgroundDesktop: { source: 'static', value: null },
        backgroundTablet: { source: 'static', value: null },
        backgroundMobile: { source: 'static', value: null },
        backgroundColor: { source: 'static', value: null },
        titlePosition: { source: 'static', value: 'start' },
        alignment: { source: 'static', value: 'center' },
        lazyLoad: { source: 'static', value: false },
        customClass: { source: 'static', value: null },
      });
    },

    addButtonToSlider(index) {
      this.element.config.sliderItems.value[index].buttons.push({
        url: { source: 'static', value: null },
        newTab: { source: 'static', value: false },
        buttonText: { source: 'static', value: null },
        buttonType: { source: 'static', value: null }
      });
    },
    removeButtonFromSlider(index, btnIndex) {
      this.element.config.sliderItems.value[index].buttons.splice(btnIndex, 1);
    },

    onImageRemove(index, type) {
      if (this.element.config.sliderItems.value[index]) {
        switch (type) {
          case 'desktop':
            this.element.config.sliderItems.value[index].desktop.value = null;
            break;
          case 'tablet':
            this.element.config.sliderItems.value[index].tablet.value = null;
            break;
          case 'mobile':
            this.element.config.sliderItems.value[index].mobile.value = null;
            break;
          case 'backgroundDesktop':
            this.element.config.sliderItems.value[index].backgroundDesktop.value = null;
            break;
          case 'backgroundTablet':
            this.element.config.sliderItems.value[index].backgroundTablet.value = null;
            break;
          case 'backgroundMobile':
            this.element.config.sliderItems.value[index].backgroundMobile.value = null;
            break;
          default:
            console.error('Unknown targetId type:', targetId.type);
        }
        this.$emit('element-update', this.element);
      } else {
        console.error("Invalid index for image removal:", index);
      }
    },
    async onImageUpload(index, targetId, type) {
      try {
        // Ensure sliderItems array and index are valid
        if (!this.element.config.sliderItems || !this.element.config.sliderItems.value) {
          console.error("sliderItems array is not initialized.");
          return;
        }

        if (!this.element.config.sliderItems.value[index]) {
          console.error("sliderItem at the specified index is not initialized.");
          return;
        }

        const mediaEntity = await this.mediaRepository.get(targetId.targetId);

        // Ensure the correct media object exists before assigning a value
        switch (type) {
          case 'desktop':
            if (!this.element.config.sliderItems.value[index].desktop) {
              this.element.config.sliderItems.value[index].desktop = { value: null };
            }
            this.element.config.sliderItems.value[index].desktop.value = mediaEntity;
            break;
          case 'tablet':
            if (!this.element.config.sliderItems.value[index].tablet) {
              this.element.config.sliderItems.value[index].tablet = { value: null };
            }
            this.element.config.sliderItems.value[index].tablet.value = mediaEntity;
            break;
          case 'mobile':
            if (!this.element.config.sliderItems.value[index].mobile) {
              this.element.config.sliderItems.value[index].mobile = { value: null };
            }
            this.element.config.sliderItems.value[index].mobile.value = mediaEntity;
            break;
          case 'backgroundDesktop':
            if (!this.element.config.sliderItems.value[index].backgroundDesktop) {
              this.element.config.sliderItems.value[index].backgroundDesktop = { value: null };
            }
            this.element.config.sliderItems.value[index].backgroundDesktop.value = mediaEntity;
            break;
          case 'backgroundTablet':
            if (!this.element.config.sliderItems.value[index].backgroundTablet) {
              this.element.config.sliderItems.value[index].backgroundTablet = { value: null };
            }
            this.element.config.sliderItems.value[index].backgroundTablet.value = mediaEntity;
            break;
          case 'backgroundMobile':
            if (!this.element.config.sliderItems.value[index].backgroundMobile) {
              this.element.config.sliderItems.value[index].backgroundMobile = { value: null };
            }
            this.element.config.sliderItems.value[index].backgroundMobile.value = mediaEntity;
            break;
          default:
            console.error('Unknown targetId type:', type);
        }

        this.$emit('element-update', this.element);
      } catch (error) {
        console.error("Error uploading image:", error);
      }
    },
    getPreviewSource(item) {

      if (item && item.value) {
        return item.value;
      }

      return null;
    },
    removeSliderItem(index) {
      this.element.config.sliderItems.value.splice(index, 1);
    },
    getUploadTag(index, type) {
      return `cmsbundle-cta-${index}-${type}`;
    },

    createdComponent() {
      this.initElementConfig("cmsbundle-cta");
    },

    onElementUpdate(value) {
      this.element.config.content.value = value;

      this.$emit("element-update", this.element);
    },
  },
});
