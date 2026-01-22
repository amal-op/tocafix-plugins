import CmsbundleSliderPlugin from "./plugins/cmsbundle-slider.plugin";
import CmsbundleProductViewed from "./plugins/cmsbundle-product-viewed.plugin";
import CmsbundleRecentlyViewedProduct from "./plugins/cmsbundle-recently-viewed-product.plugin";

PluginManager.register(
  "CmsbundleSlider",
  CmsbundleSliderPlugin,
  "[data-cmsbundle-slider]"
);
PluginManager.register(
  "CmsbundleProductViewed",
  CmsbundleProductViewed,
  "[data-cmsbundle-product-viewed]"
);
PluginManager.register(
  "CmsbundleRecentlyViewedProduct",
  CmsbundleRecentlyViewedProduct,
  "[data-cmsbundle-recently-viewed-product]"
);

