import Plugin from "src/plugin-system/plugin.class";

export default class CmsbundleProductViewed extends Plugin {
  init() {
    this.productId = this.options.productId;

    this._loadRecentlyViewedProducts();

    this._saveToRecentlyViewedProducts();
  }

  _loadRecentlyViewedProducts() {
    const products =
      JSON.parse(localStorage.getItem("CmsbundleProductsViewed")) || [];
    this._products = products;
  }

  _saveToRecentlyViewedProducts() {
    if (this._productAlreadyViewed()) {
      return;
    }

    this._products.push(this.productId);

    localStorage.setItem(
      "CmsbundleProductsViewed",
      JSON.stringify(this._products)
    );
  }

  _productAlreadyViewed() {
    return this._products.includes(this.productId);
  }
}
