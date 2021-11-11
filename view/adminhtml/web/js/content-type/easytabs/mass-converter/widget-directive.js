/*eslint-disable */
/* jscs:disable */

function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    _setPrototypeOf(subClass, superClass);
}

function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; };
    return _setPrototypeOf(o, p);
}

define([
    "Magento_PageBuilder/js/mass-converter/widget-directive-abstract",
    "Magento_PageBuilder/js/utils/object"
    ],
    function (_widgetDirectiveAbstract, _object
    ) {

  /**
   * Enables the settings of the content type to be stored as a widget directive.
   *
   * @api
   */
  var WidgetDirective = /*#__PURE__*/function (_widgetDirectiveAbstr) {
    "use strict";

    _inheritsLoose(WidgetDirective, _widgetDirectiveAbstr);

    function WidgetDirective() {
      return _widgetDirectiveAbstr.apply(this, arguments) || this;
    }

    var _proto = WidgetDirective.prototype;

    /**
     * Convert value to internal format
     *
     * @param {object} data
     * @param {object} config
     * @returns {object}
     */
    _proto.fromDom = function fromDom(data, config) {
      var attributes = _widgetDirectiveAbstr.prototype.fromDom.call(this, data, config);

      data.template = attributes.template;
      data.filter_tabs = attributes.filter_tabs;
      data.tabs_layout = attributes.tabs_layout;

      return data;
    }
    /**
     * Convert value to knockout format
     *
     * @param {object} data
     * @param {object} config
     * @returns {object}
     */
    ;

    _proto.toDom = function toDom(data, config) {
      var attributes = {
        type: "Swissup\\Easytabs\\Block\\WidgetTabs",
        template: "Swissup_Easytabs::tabs.phtml",
        filter_tabs: data.filter_tabs,
        tabs_layout: data.tabs_layout,
      };

      if (!attributes.filter_tabs || !attributes.template) {
        return data;
      }

      (0, _object.set)(data, config.html_variable, this.buildDirective(attributes));
      return data;
    };

    return WidgetDirective;
  }(_widgetDirectiveAbstract);

  return WidgetDirective;
});
//# sourceMappingURL=widget-directive.js.map
