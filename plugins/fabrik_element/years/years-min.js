/*! Fabrik */

define(["jquery","fab/element"],function(i,t){return window.FbBirthday=new Class({Extends:t,initialize:function(t,e){this.setPlugin("birthday_remove_slashes"),this.default_sepchar="-",this.parent(t,e)},getFocusEvent:function(){return"click"},getValue:function(){var e=[];return this.options.editable?(this.getElement(),this._getSubElements().each(function(t){e.push(i(t).val())}),e):this.options.value},update:function(i){var t;"string"==typeof i&&(t=this.options.separator,-1===i.indexOf(t)&&(t=this.default_sepchar),i=i.split(t)),this._getSubElements().each(function(t,e){t.value=i[e]})}}),window.FbBirthday});