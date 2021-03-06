(function () {
  UIUtil = {
    viewRegex: /\{\{([^\{\}]*)\}\}/g,
    populate: function (template, data) {
      template = template.replace(this.viewRegex, function (match, key) {
        //eval make it possible to reach nested objects
        var val = eval("data." + key);
        return "undefined" === typeof val ? "" : val;
      });
      return template;
    },
    stringifyAttribute: function (element, attr, value) {
      if ("object" === typeof value) {
        value = JSON.stringify(value);
        //element.setAttribute(attr, value);
        //return false;
      }
      return value;
      //return true;
    },
    hasCSSClass: function (element, className) {
      if (element.classList)
        return  element.classList.contains(className);
      else
        return new RegExp('(^| )' + className + '( |$)', 'gi').test(element.className);
    },
    addCSSClass: function (element, className) {
      if (this.hasCSSClass(element, className))
        return;
      if (element.classList)
        element.classList.add(className);
      else
        element.className += ' ' + className;
    },
    removeCSSClass: function (element, className) {
      if (element.classList)
        element.classList.remove(className);
      else
        element.className = element.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
    }
  };
})();

(function (xtag) {
  var ewList = {
  };

  ewList.lifecycle = {
    created: function () {
      this.template = this.innerHTML;
      this.innerHTML = "";
      this.links = {};
      this.data = [
      ];
      this.value = null;
      //console.log(this);
    },
    inserted: function () {
    },
    attributeChanged: function (attrName, oldValue, newValue) {
    }
  };

  ewList.methods = {
    render: function (data) {
      //var data = this.data;
      this.innerHTML = "";
      var a = null;
      for (var i = 0, len = data.length; i < len; i++) {
        data[i]._itemIndex = i;
        var item = xtag.createFragment(UIUtil.populate(this.template, data[i]));
        a = xtag.query(item, "a")[0];

        if (data[i].id)
          this.links[data[i].id] = a;

        this.links[i] = a;
        a.dataset.index = i;

        this.appendChild(item);
      }
    },
    selectItem: function (i, element) {
      if (this.onItemSelected) {
        this.onItemSelected(this.xtag.data[i], i, element);
      }

      /*if (EW_List.selectedElement) {
       UIUtil.removeCSSClass(EW_List.selectedElement, "selected");
       }
       
       EW_List.selectedElement = element;
       UIUtil.addCSSClass(EW_List.selectedElement, "selected");*/
    }
  };

  ewList.accessors = {
    data: {
      attribute: {},
      set: function (value) {

        this.xtag.value = null;
        if ("object" !== typeof value) {
          this.xtag.data = [
          ];
          value = [
          ];
          //return;
        }

        this.xtag.data = value;

        if (this.onSetData) {
          this.onSetData(value);
        }

        this.render(value);
      },
      get: function () {
        return this.xtag.data;
      }
    },
    onItemSelected: {
      attribute: {},
      set: function (value) {
        //console.log(this.onSetData, typeof this.onSetData);
        this.xtag.onItemSelected = value;
      },
      get: function (value) {
        return this.xtag.onItemSelected;
      }
    },
    onSetData: {
      attribute: {},
      set: function (value) {
        this.xtag.onSetData = value;
      },
      get: function (value) {
        return this.xtag.onSetData;
      }
    },
    value: {
      attribute: {},
      set: function (value) {
        value = parseInt(value);

        if (value > -1 /*&& value !== this.value*/ && this.xtag.data.length) {
          this.selectItem(value, this.links[value]);
        }

        this.xtag.value = value;
      },
      get: function () {
        return this.xtag.value;
      }
    }
  };

  ewList.events = {
    "click:delegate(a)": function (e) {
      e.preventDefault();
    },
    "tap:delegate(a)": function (e) {
      e.preventDefault();
      e.currentTarget.value = e.target.dataset.index;

    }
  };

  xtag.register("ew-list", ewList);

  // EW Actions Container

  var ewFloatMenu = {
    lifecycle: {
      created: function () {
        var _this = this;
        this.xtag.indicator = document.createElement("div");
        this.xtag.indicator.className = "ew-float-menu-indicator";
        this.xtag.indicator.style.position = "absolute";

        this.xtag.indicator.addEventListener("click", function () {
          if (_this.expanded) {
            _this.contract();

          } else {
            _this.expand();
          }
        });

        this.style.position = "absolute";
        this.xtag.originClassName = this.className;

        this.render();
      },
      inserted: function () {
        this.className = this.xtag.originClassName;
        this.xtag.indicator.className = "ew-float-menu-indicator";
        this.parentNode.appendChild(this.xtag.indicator);
      },
      attributeChanged: function (attrName, oldValue, newValue) {
      },
      removed: function () {
        this.off(true);
      }
    },
    accessors: {
      position: {
        attribute: {}
      },
      parent: {
        attribute: {}
      },
      onAttached: {
        attribute: {},
        set: function (value) {
          this.xtag.onAttached = value;
        },
        get: function (value) {
          return this.xtag.onAttached;
        }
      }
    },
    methods: {
      render: function () {
        switch (this.position || "css") {
          case "css":
            this.xtag.indicator.style.right = this.style.right = "";
            this.xtag.indicator.style.top = this.style.bottom = "";
            this.xtag.indicator.style.position = "";
            this.style.position = "";
            break;
            /*case "ne":
             this.xtag.indicator.style.right = this.style.right = "5%";
             this.xtag.indicator.style.top = this.style.bottom = "5%";*/
            break;
          case "se":
          default:
            //this.xtag.indicator.style.right = this.style.right = "5%";
            //this.xtag.indicator.style.bottom = this.style.bottom = "5%";
            this.xtag.indicator.setAttribute("position", "se");
            break;
        }
      },
      expand: function () {
        if (this.expanded)
          return;
        this.expanded = true;
        var originDim = this.getBoundingClientRect();
        //this.className += " expand";
        //this.style.width = "auto";
        //this.style.height = "auto";

        var distDim = this.getBoundingClientRect();
        //this.className = this.xtag.originClassNaame;
        /*TweenLite.fromTo(this, 1, {
         width: originDim.width,
         height: originDim.height
         }, {
         width: distDim.width,
         height: distDim.height
         });*/

        TweenLite.to(this, .3, {
          className: this.xtag.originClassName + " expand",
          ease: "Power2.easeInOut"
        });

        TweenLite.to(this.xtag.indicator, .3, {
          className: "+=active",
          ease: "Power2.easeInOut"
        });
      },
      contract: function () {
        /*if (!this.expanded)
          return;*/
        this.expanded = false;
        TweenLite.to(this, .4, {
          className: this.xtag.originClassName,
          ease: "Power2.easeInOut"
        });

        TweenLite.to(this.xtag.indicator, .4, {
          className: this.xtag.originClassName + "-indicator",
          ease: "Power2.easeInOut"
        });
      },
      on: function (flag) {
        if (this.xtag.indicator.parentNode) {
          //this.xtag.indicator.className = this.xtag.originClassName + "-indicator";
          TweenLite.to(this.xtag.indicator, .4, {
            className: "-=destroy",
            onComplete: function () {
            }
          });
          
          TweenLite.to(this.xtag.indicator, .4, {
          className: this.xtag.originClassName + "-indicator",
          ease: "Power2.easeInOut"
        });
        }
      },
      off: function (flag) {
        var _this = this;
        if (_this.xtag.indicator.parentNode) {
          this.xtag.indicator.className = "ew-float-menu-indicator";

          TweenLite.to(this.xtag.indicator, .3, {
            className: "+=destroy",
            onComplete: function () {
              if (flag)
                _this.xtag.indicator.parentNode.removeChild(_this.xtag.indicator);
            }
          });
        }
      },
      clean: function () {
        this.innerHTML = "";
        //this.appendChild(this.xtag.indicator);
      }
    },
    events: {
      "mouseleave": function () {
        //this.contract();
      }
    }
  };

  xtag.register("ew-float-menu", ewFloatMenu);
  
  var ewUITemplate = {
    lifecycle: {
      created: function () {
       
      },
      inserted: function () {
       alert("ew-ui-template");
      },
      attributeChanged: function (attrName, oldValue, newValue) {
      },
      removed: function () {
      
      }
    }
  };
  
  xtag.register("ew-ui-template", ewUITemplate);

})(xtag);