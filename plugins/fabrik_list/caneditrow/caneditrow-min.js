/*! Fabrik */
var FbListCanEditRow=new Class({Extends:FbListPlugin,initialize:function(a){this.parent(a),Fabrik.addEvent("onCanEditRow",function(a,b){this.onCanEditRow(a,b)}.bind(this))},onCanEditRow:function(a,b){b=b[0],a.result=this.options.acl[b]}});