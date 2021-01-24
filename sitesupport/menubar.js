/* jshint browser: true */
/* jshint -W097 */
/* globals */
/* exported menubarElement*/

var menubarElement = function(options) {
  'use strict';

  return {
    settings: Object.assign({
      responsive: true,
    }, options),

    options: function(opts) {
      this.settings = Object.assign(this.settings, opts);
    },

    _baseCreateMenu: function(input) {
      var menu = document.createElement('A');
      var data = new URLSearchParams(window.location.search);

      menu.classList.add('UI-main-bar-item');

      if (data.get('Function') == input.function) {
        if (input.selectedStyle) {
          if (Array.isArray(input.selectedStyle)) {
            input.selectedStyle.forEach(function(m) {
              menu.classList.add(m);
            })
          } else {
            menu.classList.add(input.selectedStyle);
          }
        }
      } else {
        if (input.baseStyle) {
          if (Array.isArray(input.baseStyle)) {
            input.baseStyle.forEach(function(m) {
              menu.classList.add(m);
            })
          } else {
            menu.classList.add(input.baseStyle);
          }
        }
      }

      var icon = '';
      if (input.icon) {
        icon = '<em class="' + input.icon + '"></em>&nbsp;';
      }
      menu.innerHTML = icon + input.text;
      if (input.title) {
        menu.title = input.title;
      }
      menu.href = 'index.php?Function=' + input.function;

      var responsiveMenu = menu.cloneNode(true);

      if (input.responsive) {
        menu.classList.add('UI-main-responsive-bar-item');
      }

      return [menu, responsiveMenu];
    },

    _baseCreateMenuTree: function(input) {
      var obj = this
      var responsiveMenu = document.createElement('DIV');
      var menus = document.createElement('DIV');
      menus.classList.add('UI-menu-div');
      if (input.responsive) {
        menus.classList.add('UI-main-responsive-bar-item');
      }
      var button = document.createElement('BUTTON');
      button.classList.add('UI-button');
      menus.append(button);
      if (input.baseStyle) {
        if (Array.isArray(input.baseStyle)) {
          input.baseStyle.forEach(function(m) {
            button.classList.add(m);
          })
        } else {
          button.classList.add(input.baseStyle);
        }
      }
      if (input.title) {
        button.title = input.title;
      }
      var icon = '';
      if (input.icon) {
        icon = '<em class="' + input.icon + '"></em>&nbsp;';
      }
      button.innerHTML = icon + input.text;
      var submenus = document.createElement('DIV');
      submenus.classList.add('UI-menu-end');
      submenus.id = input.text;

      input.function.forEach(function(item) {
        var subm = obj._baseCreateMenu(item)
        submenus.append(subm[0]);
        responsiveMenu.append(subm[1]);
      })
      menus.append(submenus);

      return [menus, responsiveMenu];
    },

    createElement: function() {
      var menu = null;
      if (Array.isArray(this.settings.function)) {
        menu = this._baseCreateMenuTree(this.settings);
      } else {
        menu = this._baseCreateMenu(this.settings);
      }

      var bar = document.getElementById('main_nav');
      bar.append(menu[0]);

      var list = document.getElementById('main_nav_list');
      list.append(menu[1]);
    },

    _createSubElement: function(target, input) {
      var menu = this._baseCreateMenu(input);
      var topmenu = document.getElementById(target);
      if (topmenu) {
        topmenu.parentElement.classList.remove('UI-hide');
        topmenu.append(menu[0]);
      }
      topmenu = document.getElementById('responsive_' + target);
      if (topmenu) {
        topmenu.append(menu[1]);
      }
    },

    createAdminElement: function() {
      this._createSubElement('Administer', this.settings);
    },

    createReportElement: function() {
      this._createSubElement('Reports', this.settings);
    },

  };
}
