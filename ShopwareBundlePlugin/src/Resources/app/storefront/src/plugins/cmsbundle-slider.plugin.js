import deepmerge from "deepmerge";
import BaseSliderPlugin from "src/plugin/slider/base-slider.plugin";

export default class CmsbundleSliderPlugin extends BaseSliderPlugin {
  /**
   * default slider options
   *
   * @type {*}
   */
  static options = deepmerge(BaseSliderPlugin.options, {
    containerSelector: "[data-cmsbundle-slider-container=true]",
    controlsSelector: "[data-cmsbundle-slider-controls=true]",
    productboxMinWidth: "300px",
  });

  /**
   * returns the slider settings for the current viewport
   *
   * @param viewport
   * @private
   */
  _getSettings(viewport) {
    super._getSettings(viewport);

    this._addItemLimit(viewport);
  }

  /**
   * extends the slider settings with the slider item limit depending on the product-box and the container width
   *
   * @private
   */
  _addItemLimit(viewport) {
    const containerWidth = this._getInnerWidth();
    const gutter = this._sliderSettings.gutter;
    const itemWidth = parseInt(
      this.options.productboxMinWidth.replace("px", ""),
      0
    );

    let itemLimit = Math.floor(containerWidth / (itemWidth + gutter));

    const countProperties = {
      mobile: "mobileCount",
      tablet: "tabletCount",
      desktop: "desktopCount",
    };

    for (const [viewPort, countProperty] of Object.entries(countProperties)) {
      const breakpoint = this._getBreakpointValue(viewPort);
      const mediaQuery = `(max-width: ${breakpoint}px)`;

      if (
        window.matchMedia(mediaQuery).matches &&
        this._sliderSettings[countProperty]
      ) {
        itemLimit = this._sliderSettings[countProperty];
        break;
      }
    }

    this._sliderSettings.items = itemLimit;
  }

  /**
   * returns the inner width of the container without padding
   *
   * @returns {number}
   * @private
   */
  _getInnerWidth() {
    const computedStyle = getComputedStyle(this.el);

    if (!computedStyle) return;

    // width with padding
    let width = this.el.clientWidth;

    width -=
      parseFloat(computedStyle.paddingLeft) +
      parseFloat(computedStyle.paddingRight);

    return width;
  }

  _getBreakpointValue(viewport) {
    switch (viewport) {
      case "mobile":
        return 576; 
      case "tablet":
        return 768; 
      case "desktop":
        return 992; 
      default:
        return 0;
    }
  }
}
